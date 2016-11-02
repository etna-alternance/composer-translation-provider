<?php

namespace Tests\Bootstrap;

use ETNA\FeatureContext\BaseContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Features context
 */
class FeatureContext extends BaseContext
{
    private $base_url;
    private $request;
    private $response;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct()
    {
        $this->base_url = "http://localhost:8080";
        $this->request  = [
            "headers" => [],
            "cookies" => [],
            "files"   => [],
        ];
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @Then /^je devrais avoir un résultat d\'API en JSON$/
     */
    public function jeDevraisAvoirUnResultatDApiEnJSON()
    {
        if ("application/json" !== $this->response["headers"]["content-type"]) {
            throw new \Exception("Invalid response type");
        }
        if ("" == $this->response['body']) {
            throw new \Exception("No response");
        }
        $json = json_decode($this->response['body']);

        if (null === $json && json_last_error()) {
            throw new \Exception("Invalid response");
        }
        $this->data = $json;
    }

    /**
     * @Then /^le status HTTP devrait être (\d+)$/
     */
    public function leStatusHTTPDevraitEtre($code)
    {
        $retCode = $this->response["http_code"];
        if ("$retCode" !== "$code") {
            echo $this->response["body"];
            throw new \Exception("Bad http response code {$retCode} != {$code}");
        }
    }

    /**
     * @When /^je fais un (GET|POST|PUT|DELETE|OPTIONS) sur ((?:[a-zA-Z0-9,:!\/\.\?\&\=\+_%-]*)|"(?:[^"]+)") avec le JSON suivant :$/
     */
    public function jeFaisUneRequetteHTTPAvecDuJSON($method, $url, $body)
    {
        if (preg_match('/^".*"$/', $url)) {
            $url = substr($url, 1, -1);
        }

        if ($body !== null) {
            if (is_object($body)) {
                $body = $body->getRaw();
            }
            $this->request["headers"]["Content-Type"] = 'application/json';
            // add content-length ...
        }

        if (null === $body && isset($this->request["headers"]["Content-Type"])) {
            unset($this->request["headers"]["Content-Type"]);
        }

        $request = Request::create($this->base_url . $url, $method, [], [], [], [], $body);
        $request->headers->add($this->request["headers"]);
        $request->cookies->add($this->request["cookies"]);
        $request->files->add($this->request["files"]);

        $response = self::$silex_app->handle($request, HttpKernelInterface::MASTER_REQUEST, true);

        $result = [
            "http_code"    => $response->getStatusCode(),
            "http_message" => Response::$statusTexts[$response->getStatusCode()],
            "body"         => $response->getContent(),
            "headers"      => array_map(
                function ($item) {
                    return $item[0];
                },
                $response->headers->all()
            ),
        ];

        $this->response = $result;
    }

    /**
     * @When /^je fais un (GET|POST|PUT|DELETE|OPTIONS) sur ((?:[a-zA-Z0-9,:!\/\.\?\&\=\+_%-]*)|"(?:[^"]+)")(?: avec le corps contenu dans "([^"]*\.json)")?$/
     */
    public function jeFaisUneRequetteHTTP($method, $url, $body = null)
    {
        if ($body !== null) {
            $body = file_get_contents($this->requests_path . $body);
            if (!$body) {
                throw new \Exception("File not found : {$this->requests_path}${body}");
            }
        }
        $this->jeFaisUneRequetteHTTPAvecDuJSON($method, $url, $body);
    }

    /**
     * @Then /^le résultat devrait être identique à "(.*)"$/
     * @Then /^le résultat devrait être identique au JSON suivant :$/
     * @Then /^le résultat devrait ressembler au JSON suivant :$/
     *
     * @param string $string
     */
    public function leResultatDevraitRessemblerAuJsonSuivant($string)
    {
        $result = json_decode($string);
        if (null === $result) {
            throw new \Exception("json_decode error");
        }

        $this->check($result, $this->data, "result", $errors);
        $this->handleErrors($this->data, $errors);
    }
}

<?php

namespace ETNA\Silex\Provider\Translation;

use Silex\Application;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app->after(
            function (Request $request, Response $response) use ($app) {
                //on récupère les tokens que l'on a surchargé dans la réponse
                $tokens = json_decode($response->headers->get('Translation', "[]"), true);

                // on récupère le contenu de la réponse càd le message d'erreur
                $content = json_decode($response->getContent(), TRUE);

                // on vérifie que c'est bien une string on la traduit et on change le contenu de la réponse
                if (is_string($content)) {
                    $translated = json_encode($app["translator"]->trans($content, $tokens, 'abort'));

                    if ($content !== $translated) {
                        $response->setContent($translated);
                    }
                }
            }
        );
    }
}

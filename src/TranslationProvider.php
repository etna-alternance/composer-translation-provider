<?php

namespace ETNA\Silex\Provider\Translation;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        if (!isset($app["translator"])) {
            $app->register(new TranslationServiceProvider());
        }

        $app->after(
            function (Request $request, Response $response) use ($app) {
                //on récupère les tokens que l'on a surchargé dans la réponse
                $tokens = json_decode($response->headers->get('Translation', "[]"), true);
                if (isset($tokens["Translation"])) {
                    $tokens = json_decode($tokens["Translation"], true);

                    // on clean le header que l'on a ajouté
                    $response->headers->remove('Translation');
                }

                // on récupère le contenu de la réponse càd le message d'erreur
                $content = json_decode($response->getContent(), true);

                // on vérifie que c'est bien une string on la traduit et on change le contenu de la réponse
                if (is_string($content)) {
                    $translated = $app["translator"]->trans($content, $tokens, 'abort', $request->getLocale());
                    $response->setContent(json_encode($translated));
                }
            }
        );

        $app["dispatcher"]->addSubscriber(new ExceptionListenerWithHeaders($app));
    }
}

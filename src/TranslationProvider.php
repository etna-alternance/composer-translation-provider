<?php

namespace ETNA\Silex\Provider\Translation;

use Silex\Application;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
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
            $app->register(
                new TranslationServiceProvider(),
                [
                    'locale_fallbacks' => ['fr']
                ]
            );
        }

        $app->after(
            function (Request $request, Response $response) use ($app) {
                //on récupère les tokens que l'on a surchargé dans la réponse
                $tokens = json_decode($response->headers->get('Translation', "[]"), true);
                if (isset($tokens["Translation"])) {
                    $tokens = json_decode($tokens["Translation"], true);
                }

                // on clean le header que l'on a ajouté
                $response->headers->remove('Translation');

                // on récupère le contenu de la réponse càd le message d'erreur
                $content = json_decode($response->getContent(), true);

                // on vérifie que c'est bien une string on la traduit et on change le contenu de la réponse
                if (is_string($content)) {
                    if ('fr' === $request->getLocale()) {
                        $translated = json_encode($app["translator"]->trans($content, $tokens, 'abort'));

                        if ($content !== $translated) {
                            $response->setContent($translated);
                        }
                    } else if (isset($tokens)) {
                        foreach ($tokens as $variable => $value) {
                            $content = str_replace($variable, $value, $content);
                        }
                        $response->setContent(json_encode($content));
                    }
                }
            }
        );
    }
}

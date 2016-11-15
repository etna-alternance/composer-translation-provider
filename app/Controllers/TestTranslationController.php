<?php

namespace TestTranslation\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class TestTranslationController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->get('/translate_fr', [$this, 'translateFr']);

        $controllers->get('/translate_en', [$this, 'translateEn']);

        $controllers->get('/translate_with_tokens_fr', [$this, 'translateWithTokensFr']);

        $controllers->get('/translate_with_tokens_en', [$this, 'translateWithTokensEn']);

        return $controllers;
    }

    /**
     * Traduire un message d'erreur
     *
     * @param Application            $app Silex Application
     * @param Request                $req
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function translateFr(Application $app, Request $req)
    {
        $req->setLocale('fr');

        return $app->abort(400, "Bad request : operation forbidden on a deleted contract");
    }

    /**
     * Traduire un message d'erreur ou ne pas le traduire car l'utilisateur est anglais
     *
     * @param Application            $app Silex Application
     * @param Request                $req
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function translateEn(Application $app, Request $req)
    {
        $req->setLocale('en');

        return $app->abort(400, "Bad request : operation forbidden on a deleted contract");
    }

    /**
     * Traduire un message d'erreur contenant des tokens
     *
     * @param Application            $app Silex Application
     * @param Request                $req
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function translateWithTokensFr(Application $app, Request $req)
    {
        $req->setLocale('fr');

        $id = "42";

        return $app->abort(404, "Manager not found : %id%", [
            'Translation' => json_encode(['%id%' => $id])
        ]);
    }

    /**
     * Traduire un message d'erreur contenant des tokens ou ne pas le traduire car l'utilisateur est anglais
     *
     * @param Application            $app Silex Application
     * @param Request                $req
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function translateWithTokensEn(Application $app, Request $req)
    {
        $req->setLocale('en');

        $id = '42';

        return $app->abort(404, "Manager not found : %id%", [
            'Translation' => json_encode(['%id%' => $id])
        ]);
    }
}

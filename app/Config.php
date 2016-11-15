<?php

namespace TestTranslation;

use Silex\Application;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Configuration principale de l'application
 */
class Config implements ServiceProviderInterface
{
    /**
     * @params string $env
     */
    public function __construct()
    {
        if (true === file_exists(__DIR__ . "/env/testing.php")) {
            require_once __DIR__ . "/env/testing.php";
        }
    }

    /**
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $this->registerEnvironmentParams($app);
        $this->registerRoutes($app);
        $app->register(new EtnaConfig());

        $app["dispatcher"]->addSubscriber(new Utils\Silex\EventSubscriber\ExceptionListener($app));
    }

    /**
     * Set up environmental variables
     *
     * If development environment, set xdebug to display all the things
     *
     * @param Application $app Silex Application
     *
     */
    private function registerEnvironmentParams(Application $app)
    {
        $app['application_name']      = 'test_translation';
        $app['application_env']       = 'testing';
        $app['application_path']      = realpath(__DIR__ . "/../");
        $app['application_namespace'] = __NAMESPACE__;

        ini_set('display_errors', true);
        ini_set('xdebug.var_display_max_depth', 100);
        ini_set('xdebug.var_display_max_children', 100);
        ini_set('xdebug.var_display_max_data', 100);
        error_reporting(E_ALL);
        $app["debug"] = true;
    }

    /**
     * Mount all controllers and routes
     * Set corresponding endpoints on the controller classes
     *
     * @param  Application $app Silex Application
     *
     */
    private function registerRoutes(Application $app)
    {
        // Recherche tous les controllers pour les loader dans $app
        foreach (glob(__DIR__ . "/Controllers/*.php") as $controller_name) {
            $controller_name = pathinfo($controller_name)["filename"];
            $class_name      = "\\TestTranslation\\Controllers\\{$controller_name}";
            if (class_exists($class_name)
                && in_array("Silex\Api\ControllerProviderInterface", class_implements($class_name))
            ) {
                $app[$controller_name] = function () use ($class_name) {
                    return new $class_name();
                };
                $app->mount('/', $app[$controller_name]);
            }
        }
    }
}

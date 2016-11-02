<?php

namespace TestTranslation;

use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\LocaleServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider;

use ETNA\Silex\Provider\Translation\TranslationProvider;
use ETNA\Silex\Provider\Config as ETNAConf;

class EtnaConfig implements ServiceProviderInterface
{
    private $rabbitmq_config;

    /**
     *
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $app->register(new ServiceControllerServiceProvider());

        $app->register(new DoctrineOrmManagerRegistryProvider());

        $app->register(new LocaleServiceProvider());

        $app->register(new TranslationProvider());

        $app['translator'] = $app->extend('translator', function ($translator) {
            $translator->addResource('xliff', __DIR__.'/Utils/Silex/Translator/abort.fr.xlf', 'fr', 'abort');

            return $translator;
        });
    }

}

<?php

namespace Osm\EasyRestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OsmEasyRestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('rest_serializer.yml');

        if ($config['enable_exception_listener']) {
            $loader->load('exception_listener.yml');
        }

        if ($config['enable_json_param_converter']) {
            $loader->load('json_param_converter.yml');
        }

        if ($config['enable_json_response_listener']) {
            $loader->load('json_response_listener.yml');
        }

        if ($config['enable_request_body_listener']) {
            $loader->load('request_body_listener.yml');
        }
    }
}



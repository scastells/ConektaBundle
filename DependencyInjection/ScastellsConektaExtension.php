<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 17:02
 */

namespace Scastells\ConektaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ScastellsConektaExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('conekta.api.key', $config['api_key']);
        $container->setParameter('conekta.public.key', $config['public_key']);
        $container->setParameter('conekta.currency', $config['currency']);
        $container->setParameter('conekta.oxxo_execute.route', $config['oxxo_execute_route']);
        $container->setParameter('conekta.oxxo_notify.route', $config['oxxo_notify_route']);
        $container->setParameter('conekta.spei_execute.route', $config['spei_execute_route']);
        $container->setParameter('conekta.spei_notify.route', $config['spei_notify_route']);
        $container->setParameter('conekta.controller.route', $config['controller_route']);
        $container->setParameter('conekta.success.route', $config['payment_success']['route']);
        $container->setParameter('conekta.success.order.append', $config['payment_success']['order_append']);
        $container->setParameter('conekta.success.order.field', $config['payment_success']['order_append_field']);
        $container->setParameter('conekta.fail.route', $config['payment_fail']['route']);
        $container->setParameter('conekta.fail.order.append', $config['payment_fail']['order_append']);
        $container->setParameter('conekta.fail.order.field', $config['payment_fail']['order_append_field']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('parameters.yml');
        $loader->load('services.yml');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 16:18
 */

namespace Fancy\ConektaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fancy_conekta');
        $rootNode->children()
            ->scalarNode('api_key')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('currency')
                ->defaultValue('MXN')
            ->end()
            ->scalarNode('oxxo_execute_route')
                ->defaultValue('/payment/conekta/oxxo/execute')
            ->end()
            ->scalarNode('oxxo_notify_route')
                ->defaultValue('/payment/conekta/oxxo/notify')
            ->end()
            ->arrayNode('payment_success')
                ->children()
                    ->scalarNode('route')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('order_append')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('order_append_field')
                        ->defaultValue('order_id')
                    ->end()
                ->end()
            ->end()
            ->arrayNode('payment_fail')
                ->children()
                    ->scalarNode('route')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('order_append')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('order_append_field')
                        ->defaultValue('card_id')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
<?php

namespace SlackCron;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SlackCronConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('slack_cron');
        
        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->isRequired()
                    ->children()
                        ->scalarNode('slack_webhook_url')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($s) {
                                return filter_var($s, FILTER_VALIDATE_URL) == false;
                            })->thenInvalid('invalid url')
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}

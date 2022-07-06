<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Austral Email Configuration.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class Configuration implements ConfigurationInterface
{
  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $treeBuilder = new TreeBuilder('austral_email');

    $rootNode = $treeBuilder->getRootNode();
    $node = $rootNode->children();
    $node = $node->booleanNode("create_not_exist")->end()
          ->booleanNode("async")->end()
          ->scalarNode("async_command")->end()
          ->arrayNode("history")
            ->addDefaultsIfNotSet()
            ->children()
              ->booleanNode("enabled")->end()
              ->booleanNode("anonymize")->end()
            ->end()
          ->end()
          ->arrayNode("defaults")
            ->addDefaultsIfNotSet()
            ->children()
              ->scalarNode("from")->end()
              ->scalarNode("to")->end()
              ->scalarNode("to_cc")->end()
              ->scalarNode("to_cci")->end();
    $node = $this->buildVars($node
      ->arrayNode('vars')
      ->scalarPrototype()
    );
    $node->end();
    return $treeBuilder;
  }

  /**
   * @param ScalarNodeDefinition $node
   *
   * @return mixed
   */
  protected function buildVars(ScalarNodeDefinition $node)
  {
    return $node->end();
  }

  /**
   * @return array
   */
  public function getConfigDefault(): array
  {
    return array(
      "create_not_exist"  =>  true,
      "async"             =>  true,
      "async_command"     =>  false,
      "history"           =>  array(
        "enabled"           =>  true,
        "anonymize"         =>  true,
      ),
      "defaults"          =>  array(
        "from"              =>  "email_from",
        "to"                =>  "email_to",
        "vars"              =>  array(
          "email_from"        =>  "noreply@your-domain.com",
          "email_to"          =>  "contact@your-domain.com"
        )
      )
    );
  }

}

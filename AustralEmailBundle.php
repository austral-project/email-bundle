<?php
/*
 * This file is part of the Austral EmailBundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle;
use Austral\EmailBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Austral\EmailBundle\DependencyInjection\Compiler\MessengerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Austral Email Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class AustralEmailBundle extends Bundle
{

  /**
   * @param ContainerBuilder $container
   */
  public function build(ContainerBuilder $container)
  {
    parent::build($container);
    $container->addCompilerPass(new DoctrineResolveTargetEntityPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
    $container->addCompilerPass(new MessengerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
  }
  
  
}

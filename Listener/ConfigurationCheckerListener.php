<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Listener;


use Austral\AdminBundle\Configuration\ConfigurationChecker;
use Austral\AdminBundle\Configuration\ConfigurationCheckerValue;
use Austral\AdminBundle\Event\ConfigurationCheckerEvent;
use Austral\EmailBundle\Configuration\EmailConfiguration;
use Austral\ToolsBundle\AustralTools;

/**
 * Austral ConfigurationChecker Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ConfigurationCheckerListener
{

  /**
   * @var EmailConfiguration
   */
  protected EmailConfiguration $emailConfiguration;

  /**
   * @param EmailConfiguration $emailConfiguration
   */
  public function __construct(EmailConfiguration $emailConfiguration)
  {
    $this->emailConfiguration = $emailConfiguration;
  }

  /**
   * @param ConfigurationCheckerEvent $configurationCheckerEvent
   *
   * @throws \Exception
   */
  public function configurationChecker(ConfigurationCheckerEvent $configurationCheckerEvent)
  {
    $configurationCheckModules = $configurationCheckerEvent->getConfigurationChecker()->getChild("modules");

    $configurationCheckerNotify = new ConfigurationChecker("email");
    $configurationCheckerNotify->setName("configuration.check.modules.email.title")
      ->setIsTranslatable(true)
      ->setParent($configurationCheckModules);

    $configurationCheckerValue = new ConfigurationCheckerValue("async", $configurationCheckerNotify);
    $configurationCheckerValue->setName("configuration.check.modules.email.async.entitled")
      ->setIsTranslatable(true)
      ->setIsTranslatableValue(true)
      ->setType(ConfigurationCheckerValue::$TYPE_CHECKED)
      ->setStatus($this->emailConfiguration->get('async') ? ConfigurationCheckerValue::$STATUS_SUCCESS : ConfigurationCheckerValue::$STATUS_NONE)
      ->setValue($this->emailConfiguration->get('async') ? "configuration.check.choices.yes" : "configuration.check.choices.no");

    $configurationCheckerValue = new ConfigurationCheckerValue("mercure", $configurationCheckerNotify);
    $configurationCheckerValue->setName("configuration.check.modules.email.create.entitled")
      ->setIsTranslatable(true)
      ->setIsTranslatableValue(true)
      ->setType(ConfigurationCheckerValue::$TYPE_CHECKED)
      ->setStatus($this->emailConfiguration->get('create_not_exist') ? ConfigurationCheckerValue::$STATUS_SUCCESS : ConfigurationCheckerValue::$STATUS_NONE)
      ->setValue($this->emailConfiguration->get('create_not_exist') ? "configuration.check.choices.yes" : "configuration.check.choices.no");

    $configurationCheckerValue = new ConfigurationCheckerValue("vars", $configurationCheckerNotify);
    $configurationCheckerValue->setName("configuration.check.modules.email.vars.entitled")
      ->setIsTranslatable(true)
      ->setIsTranslatableValue(true)
      ->setType(ConfigurationCheckerValue::$TYPE_ARRAY)
      ->setValues(AustralTools::getValueByKey($this->emailConfiguration->getConfig("defaults"), "vars", array()) );

  }
}
<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EmailBundle\EventSubscriber;


use Austral\EmailBundle\Configuration\EmailConfiguration;
use Austral\EmailBundle\Event\EmailTemplateViewEvent;
use Austral\EmailBundle\Model\EmailAddress;
use Austral\ToolsBundle\AustralTools;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EmailView Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailTemplateViewSubscriber implements EventSubscriberInterface
{

  /**
   * @var EmailConfiguration
   */
  protected EmailConfiguration $emailConfiguration;

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EmailTemplateViewEvent::EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_INIT_VARS     =>  ["initVars", 1024],
      EmailTemplateViewEvent::EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_RELOAD_VARS   =>  ["reloadVars", 1024],
    ];
  }

  /**
   * EmailViewSubscriber constructor.
   *
   * @param EmailConfiguration $emailConfiguration
   */
  public function __construct(EmailConfiguration $emailConfiguration)
  {
    $this->emailConfiguration = $emailConfiguration;
  }

  /**
   * @param EmailTemplateViewEvent $emailViewEvent
   */
  public function initVars(EmailTemplateViewEvent $emailViewEvent)
  {

  }

  /**
   * @param EmailTemplateViewEvent $emailViewEvent
   */
  public function reloadVars(EmailTemplateViewEvent $emailViewEvent)
  {
    $varsExists = array_merge(
      AustralTools::getValueByKey($this->emailConfiguration->getConfig("defaults"), "vars", array()),
      $emailViewEvent->getEmailTemplate()->getVars()
    );
    $varsDetected = array();

    /** @var EmailAddress $emailTemplateAddress */
    foreach($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEmailsTo() as $emailTemplateAddress)
    {
      $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailTemplateAddress->getEmail()));
    }

    /** @var EmailAddress $emailTemplateAddress */
    foreach($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEmailsToCc() as $emailTemplateAddress)
    {
      $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailTemplateAddress->getEmail()));
    }

    /** @var EmailAddress $emailTemplateAddress */
    foreach($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEmailsToCci() as $emailTemplateAddress)
    {
      $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailTemplateAddress->getEmail()));
    }

    $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEmailFrom()));
    $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEmailReplyTo()));
    $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getEntitledEmail()));
    $varsDetected = array_merge($varsDetected, AustralTools::getKeysInValue($emailViewEvent->getEmailTemplate()->getTranslateCurrent()->getContentEmail()));

    foreach($varsDetected as $key)
    {
      $reelkeyname = str_replace("%", "", $key);
      if(!array_key_exists($reelkeyname, $varsExists) && !in_array($reelkeyname, $varsExists))
      {
        $emailViewEvent->getEmailTemplate()->addVars($reelkeyname);
      }
    }

  }


}
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

use Austral\EmailBundle\Event\EmailHistoryEvent;
use Austral\EmailBundle\Event\EmailSenderEvent;

use Austral\EmailBundle\Services\EmailSender;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EmailSender Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailSenderSubscriber implements EventSubscriberInterface
{

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EmailSenderEvent::EVENT_AUSTRAL_EMAIL_SENDER_SEND  =>  ["send", 1024],
    ];
  }

  /**
   * @var EmailSender
   */
  protected EmailSender $emailSender;

  /**
   * EmailSenderSubscriber constructor.
   *
   */
  public function __construct(EmailSender $emailSender)
  {
    $this->emailSender = $emailSender;
  }

  /**
   * @param EmailSenderEvent $emailSenderEvent
   *
   * @throws NonUniqueResultException
   */
  public function send(EmailSenderEvent $emailSenderEvent)
  {
    $this->emailSender->setAsync($emailSenderEvent->getAsync())
      ->setLanguage($emailSenderEvent->getLanguage())
      ->initEmailTemplateByKeymane($emailSenderEvent->getEmailKeyname());

    if($emailHistory = $emailSenderEvent->getEmailHistory())
    {
      $emailHistoryEvent = new EmailHistoryEvent($this->emailSender->getEmailTemplate(), $emailSenderEvent->getObject(), $emailHistory);
      $this->emailSender->initEmailHistoryEvent($emailHistoryEvent);
    }

    $this->emailSender->setObject($emailSenderEvent->getObject())
      ->addVars($emailSenderEvent->getVars())
      ->execute();
  }

}
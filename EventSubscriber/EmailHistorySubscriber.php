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

use Austral\EmailBundle\Entity\EmailHistory;
use Austral\EmailBundle\EntityManager\EmailHistoryEntityManager;
use Austral\EmailBundle\Event\EmailHistoryEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EmailHistory Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailHistorySubscriber implements EventSubscriberInterface
{

  /**
   * @var EmailHistoryEntityManager
   */
  protected EmailHistoryEntityManager $emailHistoryEntityManager;

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_CREATE     =>  ["create", 1024],
      EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE     =>  ["update", 1024],
    ];
  }

  /**
   * EmailHistorySubscriber constructor.
   *
   */
  public function __construct(EmailHistoryEntityManager $emailHistoryEntityManager)
  {
    $this->emailHistoryEntityManager = $emailHistoryEntityManager;
  }

  /**
   * @param EmailHistoryEvent $emailHistoryEvent
   *
   * @throws \Exception
   */
  public function create(EmailHistoryEvent $emailHistoryEvent)
  {
    $emailTemplate = $emailHistoryEvent->getEmailTemplate();

    /** @var EmailHistory $emailHistory */
    $emailHistory = $this->emailHistoryEntityManager->create();
    $emailHistory->setEmailTemplateKeyname($emailHistoryEvent->getEmailTemplate()->getKeyname());

    $emailHistory->setEmailFrom($emailTemplate->getTranslateCurrent()->getEmailFrom());
    $emailHistory->setEmailsTo($emailTemplate->getTranslateCurrent()->getEmailsTo());
    $emailHistory->setEmailsToCc($emailTemplate->getTranslateCurrent()->getEmailsToCc());
    $emailHistory->setEmailsToCci($emailTemplate->getTranslateCurrent()->getEmailsToCci());
    $emailHistory->setEmailReplyTo($emailTemplate->getTranslateCurrent()->getEmailReplyTo());
    $emailHistory->setEntitledEmail($emailTemplate->getTranslateCurrent()->getEntitledEmail());
    $emailHistory->setContentEmail($emailHistoryEvent->getContentEmail());

    $emailHistory->setObjectClassname($emailHistoryEvent->getObject() ? $emailHistoryEvent->getObject()->getEntityName() : null);
    $emailHistory->setObjectId($emailHistoryEvent->getObject() ? $emailHistoryEvent->getObject()->getId() : null);

    $emailHistoryEvent->setEmailHistory($emailHistory);
    $this->emailHistoryEntityManager->update($emailHistoryEvent->getEmailHistory());
  }

  /**
   * @param EmailHistoryEvent $emailHistoryEvent
   *
   */
  public function update(EmailHistoryEvent $emailHistoryEvent)
  {
    $this->emailHistoryEntityManager->update($emailHistoryEvent->getEmailHistory());
  }

}
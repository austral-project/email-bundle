<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EmailBundle\Message;

use Austral\EmailBundle\Event\EmailHistoryEvent;
use Austral\EmailBundle\Services\EmailSender;

use Austral\EntityBundle\EntityManager\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Austral EmailSender MessagerHandler.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailSenderMessageHandler implements MessageHandlerInterface
{

  /**
   * @var EmailSender
   */
  protected EmailSender $emailSender;

  /**
   * @var EntityManager
   */
  protected EntityManager $entityManager;

  /**
   * EmailSenderSubscriber constructor.
   *
   * @param EntityManager $entityManager
   * @param EmailSender $emailSender
   */
  public function __construct(EmailSender $emailSender, EntityManager $entityManager)
  {
    $this->emailSender = $emailSender;
    $this->entityManager = $entityManager;
  }

  /**
   * @param EmailSenderMessage $emailSenderMessage
   *
   * @throws NonUniqueResultException
   */
  public function __invoke(EmailSenderMessage $emailSenderMessage)
  {
    $this->entityManager->setCurrentLanguage($emailSenderMessage->getLanguage());

    $object = null;
    if($emailSenderMessage->getObjectClassname() && $emailSenderMessage->getObjectId())
    {
      $object = $this->entityManager->getDoctrineEntityManager()
        ->getRepository($emailSenderMessage->getObjectClassname())
        ->retreiveById($emailSenderMessage->getObjectId());
    }

    $emailTemplate = $this->entityManager->getDoctrineEntityManager()
      ->getRepository("Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface")
      ->retreiveByKeyname($emailSenderMessage->getEmailTemplateKeyname());

    $this->emailSender->setLanguage($emailSenderMessage->getLanguage())
      ->setEmailTemplate($emailTemplate)
      ->addVars($emailSenderMessage->getVars())
      ->setObject($object);

    if($emailSenderMessage->getEmailHistoryId())
    {
      $emailHistory = $this->entityManager->getDoctrineEntityManager()
        ->getRepository("Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface")
        ->retreiveById($emailSenderMessage->getEmailHistoryId());
      if($emailHistory)
      {
        $emailHistoryEvent = new EmailHistoryEvent($emailTemplate, $object, $emailHistory);
        $this->emailSender->initEmailHistoryEvent($emailHistoryEvent);
      }
    }
    $this->emailSender->send();
  }

}
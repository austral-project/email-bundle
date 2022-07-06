<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EmailBundle\Services;

use Austral\EmailBundle\Configuration\EmailConfiguration;
use Austral\EmailBundle\Entity\EmailHistory;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EmailBundle\EntityManager\EmailTemplateEntityManager;
use Austral\EmailBundle\Event\EmailHistoryEvent;
use Austral\EmailBundle\Message\EmailSenderMessage;
use Austral\EmailBundle\Model\EmailAddress;
use Austral\EmailBundle\Model\EmailLog;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;

use Austral\ToolsBundle\Services\ServicesStatusChecker;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email as EmailSymfony;

/**
 * Austral Email Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailSender
{

  /**
   * @var MailerInterface
   */
  protected MailerInterface $mailer;
  /**
   * @var EmailTransform
   */
  protected EmailTransform $emailTransform;
  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * @var MessageBusInterface|null
   */
  protected ?MessageBusInterface $bus;

  /**
   * @var EmailTemplateEntityManager
   */
  protected EmailTemplateEntityManager $emailEntityManager;

  /**
   * @var EmailConfiguration
   */
  protected EmailConfiguration $emailConfiguration;

  /**
   * @var EntityInterface|null
   */
  protected ?EntityInterface $object = null;

  /**
   * @var EmailTemplateInterface|EntityTranslateMasterInterface|null
   */
  protected ?EmailTemplateInterface $emailTemplate = null;

  /**
   * @var EmailHistoryEvent|null
   */
  protected ?EmailHistoryEvent $emailHistoryEvent = null;

  /**
   * @var mixed
   */
  protected $async = "default";

  /**
   * @var bool
   */
  protected bool $asyncServiceStart = false;

  /**
   * @var string|null
   */
  protected ?string $language = null;

  /**
   * @var array
   */
  protected array $varsDefault = array();

  /**
   * @var array
   */
  protected array $varsObject = array();

  /**
   * @var array
   */
  protected array $vars = array();

  /**
   * ContentBlockSubscriber constructor.
   *
   * @param MailerInterface $mailer
   * @param RequestStack $requestStack
   * @param EmailTransform $emailTransform
   * @param EmailTemplateEntityManager $emailEntityManager
   * @param EmailConfiguration $emailConfiguration
   * @param ServicesStatusChecker $servicesStatusChecker
   * @param EventDispatcherInterface $eventDispatcher
   * @param MessageBusInterface|null $bus
   *
   * @throws Exception
   */
  public function __construct(
    MailerInterface $mailer,
    RequestStack $requestStack,
    EmailTransform $emailTransform,
    EmailTemplateEntityManager $emailEntityManager,
    EmailConfiguration $emailConfiguration,
    ServicesStatusChecker $servicesStatusChecker,
    EventDispatcherInterface $eventDispatcher,
    ?MessageBusInterface $bus
  )
  {
    $this->mailer = $mailer;
    $this->language = $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getLocale() : null;
    $this->emailTransform = $emailTransform;
    $this->emailEntityManager = $emailEntityManager;
    $this->emailConfiguration = $emailConfiguration;
    $this->eventDispatcher = $eventDispatcher;
    $this->bus = $bus;
    if($servicesStatusChecker->getServiceIsRunByCommand("messenger:consume"))
    {
      $this->asyncServiceStart = true;
    }
  }

  /**
   * @param string $language
   *
   * @return EmailSender
   */
  public function setLanguage(string $language): EmailSender
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @param mixed|null $async
   *
   * @return $this
   */
  public function setAsync($async = null): EmailSender
  {
    $this->async = ($async !== null) ? $async : "default";
    return $this;
  }

  /**
   * @param string $emailTemplateKeyname
   *
   * @return $this
   * @throws NonUniqueResultException
   */
  public function initEmailTemplateByKeymane(string $emailTemplateKeyname): EmailSender
  {
    if(!$this->language)
    {
      throw new Exception("The language is required !!!");
    }

    /** @var EmailTemplateInterface|EntityTranslateMasterInterface $emailTemplate */
    $emailTemplate = $this->emailEntityManager->setCurrentLanguage($this->language)->retreiveByKeyname($emailTemplateKeyname);
    if(!$emailTemplate)
    {
      if($this->emailConfiguration->get("create_not_exist"))
      {
        $emailTemplate = $this->emailEntityManager->create();
        $emailTemplate->setName($emailTemplateKeyname)->setKeyname($emailTemplateKeyname);

        $defaultFrom = $this->emailConfiguration->get('defaults.from');
        $defaultTo = $this->emailConfiguration->get('defaults.to');

        $emailAddress = new EmailAddress();
        $emailAddress->setId(Uuid::uuid4()->toString());
        $emailAddress->setEmail(strpos($defaultTo, "@") !== false ? $defaultTo : "%{$defaultTo}%");

        $emailTemplate->getTranslateCurrent()
          ->setEntitledEmail($emailTemplateKeyname)
          ->setEmailFrom(strpos($defaultFrom, "@") !== false ? $defaultFrom : "%{$defaultFrom}%")
          ->setEmailsTo(array($emailAddress));
      }
    }
    else
    {
      $emailTemplate->setCurrentLanguage($this->language);
    }
    $this->emailTemplate = $emailTemplate;
    return $this;
  }

  /**
   * @param EmailTemplateInterface $emailTemplate
   *
   * @return EmailSender
   */
  public function setEmailTemplate(EmailTemplateInterface  $emailTemplate): EmailSender
  {
    $this->emailTemplate = $emailTemplate;
    return $this;
  }

  /**
   * @return EmailTemplateInterface
   */
  public function getEmailTemplate()
  {
    return $this->emailTemplate;
  }

  /**
   * @param EmailHistoryEvent $emailHistoryEvent
   *
   * @return EmailSender
   */
  public function initEmailHistoryEvent(EmailHistoryEvent  $emailHistoryEvent): EmailSender
  {
    $this->emailHistoryEvent = $emailHistoryEvent;
    return $this;
  }

  /**
   * @param EntityInterface|null $object
   *
   * @return $this
   */
  public function setObject(?EntityInterface $object = null): EmailSender
  {
    if($object)
    {
      $this->object = $object;
      $this->varsObject = AustralTools::flattenArray(".", method_exists($object, "getEmailVars") ? $object->getEmailVars() : (array) $object, "object.");
    }
    return $this;
  }

  /**
   * @param array $vars
   * @param string $prefix
   *
   * @return EmailSender
   */
  public function addVars(array $vars = array(), string $prefix = ""): EmailSender
  {
    if($vars)
    {
      $this->vars = array_merge($this->vars, AustralTools::flattenArray(".", $vars, $prefix));
    }
    return $this;
  }

  /**
   * @throws Exception
   */
  public function execute()
  {
    if($this->emailTemplate)
    {
      $this->emailTransform->setEmailTemplate($this->emailTemplate)
        ->addVars($this->vars)
        ->addVars($this->varsObject);

      if($this->emailConfiguration->get('history.enabled') && !$this->emailHistoryEvent)
      {
        $this->emailHistoryEvent = new EmailHistoryEvent($this->emailTemplate, $this->object);
        $this->emailHistoryEvent->setContentEmail($this->emailTransform->getContentEmail());
        $this->eventDispatcher->dispatch($this->emailHistoryEvent, EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_CREATE);
      }

      try {

        $varsInConfig = AustralTools::getValueByKey($this->emailConfiguration->getConfig("defaults"), "vars", array()) ;
        $vars = array_keys($this->vars);
        $varsFinal = array();
        foreach($vars as $var)
        {
          if(!array_key_exists($var, $varsInConfig))
          {
            $varsFinal[] = $var;
          }
        }
        $this->emailTemplate->setVars(array_merge(array_keys($this->varsObject), $varsFinal));
        $this->emailEntityManager->update($this->emailTemplate, true);

        if($this->emailHistoryEvent)
        {
          $this->emailHistoryEvent->getEmailHistory()->setVars($this->vars);
          $this->emailHistoryEvent->getEmailHistory()->setAnonymise($this->emailConfiguration->get('history.anonymize'));

          $emailLog = new EmailLog();
          $emailLog->setId(Uuid::uuid4()->toString());
          $emailLog->setStatus(EmailHistory::STATUS_CREATE);
          $this->emailHistoryEvent->getEmailHistory()->addLog($emailLog);

          $this->eventDispatcher->dispatch($this->emailHistoryEvent, EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE);
        }

        if($this->asyncServiceStart && $this->bus && (($this->emailConfiguration->get("async") && $this->async == "default") || $this->async === true))
        {
          $this->bus->dispatch(new EmailSenderMessage(
              $this->emailTemplate,
              $this->language,
              $this->object,
              $this->vars,
              $this->emailHistoryEvent ? $this->emailHistoryEvent->getEmailHistory() : null
            )
          );
        }
        else
        {
          $this->send();
        }
      }
      catch (Exception $e) {
        $emailLog = new EmailLog();
        $emailLog->setId(Uuid::uuid4()->toString());
        $emailLog->setStatus(EmailHistory::STATUS_ERROR);
        $emailLog->setMessage($e->getMessage());
        $this->emailHistoryEvent->getEmailHistory()->addLog($emailLog);
        $this->eventDispatcher->dispatch($this->emailHistoryEvent, EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE);
      }
    }
  }

  public function send()
  {
    if($this->emailTemplate)
    {
      $this->emailTransform->setEmailTemplate($this->emailTemplate)
        ->addVars($this->vars)
        ->addVars($this->varsObject);

      $this->emailTemplate->setCurrentLanguage($this->language);

      $emailTemplateSymfony = null;
      try {
        $emailTemplateSymfony = (new EmailSymfony())
          ->from($this->emailTransform->getEmailFrom());

        /** @var string $emailTemplateAddress */
        foreach($this->emailTransform->getEmailsTo() as $emailTemplateAddress)
        {
          $emailTemplateSymfony->addTo($emailTemplateAddress);
        }

        /** @var string $emailTemplateAddress */
        foreach($this->emailTransform->getEmailsToCc() as $emailTemplateAddress)
        {
          $emailTemplateSymfony->addCc($emailTemplateAddress);
        }

        /** @var string $emailTemplateAddress */
        foreach($this->emailTransform->getEmailsToCci() as $emailTemplateAddress)
        {
          $emailTemplateSymfony->addBcc($emailTemplateAddress);
        }

        if($this->emailTemplate->getTranslateCurrent()->getEmailReplyTo())
        {
          $emailTemplateSymfony->replyTo($this->emailTransform->getEmailReplyTo());
        }
        $emailTemplateSymfony->subject($this->emailTransform->getEntitledEmail());
        $emailTemplateSymfony->html($this->emailTransform->getContentEmail(true));
      }
      catch (Exception $exception) {
        $emailLog = new EmailLog();
        $emailLog->setId(Uuid::uuid4()->toString());
        $emailLog->setStatus(EmailHistory::STATUS_ERROR);
        $emailLog->setMessage($exception->getMessage());

        $this->emailHistoryEvent->getEmailHistory()->addLog($emailLog);
        $this->eventDispatcher->dispatch($this->emailHistoryEvent, EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE);
      }

      try {
        $this->mailer->send($emailTemplateSymfony);
        $emailLogStatus = EmailHistory::STATUS_SEND;
        $emailLogMessage = "Send email is successful";
      } catch (Exception $exception) {
        $emailLogStatus = EmailHistory::STATUS_ERROR;
        $emailLogMessage = $exception->getMessage();
      }

      if($this->emailHistoryEvent)
      {
        $emailLog = new EmailLog();
        $emailLog->setId(Uuid::uuid4()->toString());
        $emailLog->setStatus($emailLogStatus);
        $emailLog->setMessage($emailLogMessage);
        $this->emailHistoryEvent->getEmailHistory()->addLog($emailLog);

        $this->eventDispatcher->dispatch($this->emailHistoryEvent, EmailHistoryEvent::EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE);
      }

    }
  }

}
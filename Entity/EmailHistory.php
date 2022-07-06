<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\EmailBundle\Entity;

use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;

use Austral\EmailBundle\Model\EmailAddress;
use Austral\EmailBundle\Model\EmailLog;
use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Austral EmailHistory Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class EmailHistory extends Entity implements EmailHistoryInterface, EntityInterface
{

  const STATUS_CREATE = "create";
  const STATUS_SEND = "send";
  const STATUS_ERROR = "error";

  use EntityTimestampableTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @var boolean
   * @ORM\Column(name="anonymise", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $anonymise = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="email_template_keyname", type="string", length=255, nullable=true )
   */
  protected ?string $emailTemplateKeyname = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="object_classname", type="string", length=255, nullable=true )
   */
  protected ?string $objectClassname = null;

  /**
   * @var string|null
   * @ORM\Column(name="object_id", type="string", length=255, nullable=true )
   */
  protected ?string $objectId = null;

  /**
   * @var string|null
   * @ORM\Column(name="status", type="string", length=255, nullable=false)
   */
  protected ?string $status = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="email_from", type="string", length=255, nullable=false)
   */
  protected ?string $emailFrom = null;

  /**
   * @var array
   * @ORM\Column(name="emails_to", type="json", nullable=false)
   */
  protected array $emailsTo = array();

  /**
   * @var array
   * @ORM\Column(name="emails_to_cc", type="json", nullable=true )
   */
  protected array $emailsToCc = array();

  /**
   * @var array
   * @ORM\Column(name="emails_to_cci", type="json", nullable=true )
   */
  protected array $emailsToCci = array();

  /**
   * @var string|null
   * @ORM\Column(name="email_reply_to", type="string", length=255, nullable=true )
   */
  protected ?string $emailReplyTo = null;

  /**
   * @var string|null
   * @ORM\Column(name="entitled_email", type="string", length=255, nullable=true )
   */
  protected ?string $entitledEmail = null;

  /**
   * @var string|null
   * @ORM\Column(name="contentEmail", type="text", nullable=true )
   */
  protected ?string $contentEmail = null;

  /**
   * @var array
   * @ORM\Column(name="logs", type="json", nullable=true )
   */
  protected array $logs = array();

  /**
   * @var array
   * @ORM\Column(name="vars", type="json", nullable=true )
   */
  protected array $vars = array();

  /**
   * Email constructor.
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->status = self::STATUS_CREATE;
  }

  /**
   * @return string|null
   */
  public function getObjectClassname(): ?string
  {
    return $this->objectClassname;
  }

  /**
   * @param string|null $objectClassname
   *
   * @return $this
   */
  public function setObjectClassname(?string $objectClassname): EmailHistory
  {
    $this->objectClassname = $objectClassname;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getObjectId(): ?string
  {
    return $this->objectId;
  }

  /**
   * @param string|null $objectId
   *
   * @return $this
   */
  public function setObjectId(?string $objectId): EmailHistory
  {
    $this->objectId = $objectId;
    return $this;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getCreated()->format("Y-m-d");
  }

  /**
   * @return string|null
   */
  public function getEmailTemplateKeyname(): ?string
  {
    return $this->emailTemplateKeyname;
  }

  /**
   * @param string|null $emailTemplateKeyname
   *
   * @return $this
   */
  public function setEmailTemplateKeyname(?string $emailTemplateKeyname): EmailHistory
  {
    $this->emailTemplateKeyname = $emailTemplateKeyname;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getStatus(): ?string
  {
    return $this->status;
  }

  /**
   * @param string|null $status
   *
   * @return $this
   */
  public function setStatus(?string $status): EmailHistory
  {
    $this->status = $status;
    return $this;
  }

  /**
   * @return bool
   */
  public function getAnonymise(): bool
  {
    return $this->anonymise;
  }

  /**
   * @param bool $anonymise
   *
   * @return $this
   */
  public function setAnonymise(bool $anonymise): EmailHistory
  {
    $this->anonymise = $anonymise;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getEmailFrom(): ?string
  {
    return $this->emailFrom;
  }

  /**
   * @param string|null $emailFrom
   *
   * @return $this
   */
  public function setEmailFrom(?string $emailFrom): EmailHistory
  {
    $this->emailFrom = $emailFrom;
    return $this;
  }

  /**
   * @return string
   */
  public function getEmailsToString(): string
  {
    $emails = array();
    /** @var EmailAddress $emailAddress */
    foreach($this->getEmailsTo() as $emailAddress)
    {
      $emails[] = $emailAddress->getEmail();
    }
    return implode(", ", $emails);
  }

  /**
   * @return array
   */
  public function getEmailsTo(): array
  {
    $emailsTo = array();
    foreach($this->emailsTo as $emailToValue)
    {
      /** @var EmailAddress $emailToObject */
      $emailToObject = unserialize($emailToValue);
      $emailsTo[$emailToObject->getId()] = $emailToObject;
    }
    return $emailsTo;
  }

  /**
   * @param array $emailsTo
   *
   * @return $this
   */
  public function setEmailsTo(array $emailsTo): EmailHistory
  {
    $this->emailsTo = array();
    /** @var EmailAddress $emailTo */
    foreach ($emailsTo as $id => $emailTo)
    {
      $emailTo->setId($id);
      $this->emailsTo[$emailTo->getId()] = serialize($emailTo);
    }
    ksort($this->emailsTo);
    return $this;
  }

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailHistory
   */
  public function updateEmailsTo(string $id, EmailAddress $emailAddress): EmailHistory
  {
    $this->emailsTo[$id] = serialize($emailAddress);
    return $this;
  }

  /**
   * @return string
   */
  public function getEmailsToCcString(): string
  {
    $emails = array();
    /** @var EmailAddress $emailAddress */
    foreach($this->getEmailsToCc() as $emailAddress)
    {
      $emails[] = $emailAddress->getEmail();
    }
    return implode(", ", $emails);
  }

  /**
   * @return array
   */
  public function getEmailsToCc(): array
  {
    $emailsToCc = array();
    foreach($this->emailsToCc as $emailToCcValue)
    {
      /** @var EmailAddress $emailToCcObject */
      $emailToCcObject = unserialize($emailToCcValue);
      $emailsToCc[$emailToCcObject->getId()] = $emailToCcObject;
    }
    return $emailsToCc;
  }

  /**
   * @param array $emailsToCc
   *
   * @return $this
   */
  public function setEmailsToCc(array $emailsToCc): EmailHistory
  {
    $this->emailsToCc = array();
    /** @var EmailAddress $emailTo */
    foreach ($emailsToCc as $id => $emailToCc)
    {
      $emailToCc->setId($id);
      $this->emailsToCc[$emailToCc->getId()] = serialize($emailToCc);
    }
    ksort($this->emailsToCc);
    return $this;
  }

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailHistory
   */
  public function updateEmailsToCc(string $id, EmailAddress $emailAddress): EmailHistory
  {
    $this->emailsToCc[$id] = serialize($emailAddress);
    return $this;
  }

  /**
   * @return string
   */
  public function getEmailsToCciString(): string
  {
    $emails = array();
    /** @var EmailAddress $emailAddress */
    foreach($this->getEmailsToCci() as $emailAddress)
    {
      $emails[] = $emailAddress->getEmail();
    }
    return implode(", ", $emails);
  }

  /**
   * @return array
   */
  public function getEmailsToCci(): array
  {
    $emailsToCci = array();
    foreach($this->emailsToCci as $emailToCciValue)
    {
      /** @var EmailAddress $emailToCciObject */
      $emailToCciObject = unserialize($emailToCciValue);
      $emailsToCci[$emailToCciObject->getId()] = $emailToCciObject;
    }
    return $emailsToCci;
  }

  /**
   * @param array $emailsToCci
   *
   * @return $this
   */
  public function setEmailsToCci(array $emailsToCci): EmailHistory
  {
    $this->emailsToCci = array();
    /** @var EmailAddress $emailTo */
    foreach ($emailsToCci as $id => $emailToCci)
    {
      $emailToCci->setId($id);
      $this->emailsToCci[$emailToCci->getId()] = serialize($emailToCci);
    }
    ksort($this->emailsToCci);
    return $this;
  }

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailHistory
   */
    public function updateEmailsToCci(string $id, EmailAddress $emailAddress): EmailHistory
    {
      $this->emailsToCci[$id] = serialize($emailAddress);
      return $this;
    }

  /**
   * @return string|null
   */
  public function getEmailReplyTo(): ?string
  {
    return $this->emailReplyTo;
  }

  /**
   * @param string|null $emailReplyTo
   *
   * @return $this
   */
  public function setEmailReplyTo(?string $emailReplyTo): EmailHistory
  {
    $this->emailReplyTo = $emailReplyTo;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getEntitledEmail(): ?string
  {
    return $this->entitledEmail;
  }

  /**
   * @param string|null $entitledEmail
   *
   * @return EmailHistory
   */
  public function setEntitledEmail(?string $entitledEmail): EmailHistory
  {
    $this->entitledEmail = $entitledEmail;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getContentEmail(): ?string
  {
    return $this->contentEmail;
  }

  /**
   * @param string|null $contentEmail
   *
   * @return $this
   */
  public function setContentEmail(?string $contentEmail): EmailHistory
  {
    $this->contentEmail = $contentEmail;
    return $this;
  }

  /**
   * @return array
   */
  public function getLogs(): array
  {
    $logs = array();
    foreach($this->logs as $log)
    {
      if($log)
      {
        /** @var EmailLog $emailLog */
        $emailLog = unserialize($log);
        $logs[$emailLog->getId()] = $emailLog;
      }
    }
    return $logs;
  }

  /**
   * @param array $logs
   *
   * @return $this
   */
  public function setLogs(array $logs): EmailHistory
  {
    $this->logs = $logs;
    return $this;
  }

  /**
   * @param EmailLog $emailLog
   *
   * @return $this
   */
  public function addLog(EmailLog $emailLog): EmailHistory
  {
    $this->logs[$emailLog->getId()] = serialize($emailLog);
    return $this;
  }

  /**
   * @return array
   */
  public function getVars(): array
  {
    return $this->vars;
  }

  /**
   * @param array $vars
   *
   * @return $this
   */
  public function setVars(array $vars): EmailHistory
  {
    $this->vars = $vars;
    return $this;
  }

}
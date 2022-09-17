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

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateTranslateInterface;
use Austral\EmailBundle\Model\EmailAddress;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;

use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateChildTrait;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;


use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;


/**
 * Austral EmailTranslate Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class EmailTemplateTranslate extends Entity implements EmailTemplateTranslateInterface, EntityInterface, TranslateChildInterface
{

  use EntityTranslateChildTrait;
  use EntityTimestampableTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @var EmailTemplateInterface|TranslateMasterInterface
   * @ORM\ManyToOne(targetEntity="\Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface", inversedBy="translates", cascade={"persist"})
   * @ORM\JoinColumn(name="master_id", referencedColumnName="id")
   */
  protected TranslateMasterInterface $master;
  
  /**
   * @var boolean
   * @ORM\Column(name="has_template", type="boolean", nullable=true)
   */
  protected bool $hasTemplate = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="template_path", type="string", length=255, nullable=true )
   */
  protected ?string $templatePath = null;

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
   * Constructor
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
  }

  /**
   * __ToString
   * 
   * @return string
   */
  public function __toString()
  {
    return $this->getId();
  }

  /**
   * @return bool
   */
  public function getHasTemplate(): bool
  {
    return $this->hasTemplate;
  }

  /**
   * @param bool $hasTemplate
   *
   * @return $this
   */
  public function setHasTemplate(bool $hasTemplate): EmailTemplateTranslate
  {
    $this->hasTemplate = $hasTemplate;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getTemplatePath(): ?string
  {
    return $this->templatePath;
  }

  /**
   * @param string|null $templatePath
   *
   * @return $this
   */
  public function setTemplatePath(?string $templatePath): EmailTemplateTranslate
  {
    $this->templatePath = $templatePath;
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
   * @return EmailTemplateTranslate
   */
  public function setEntitledEmail(?string $entitledEmail): EmailTemplateTranslate
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
  public function setContentEmail(?string $contentEmail): EmailTemplateTranslate
  {
    $this->contentEmail = $contentEmail;
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
  public function setEmailFrom(?string $emailFrom): EmailTemplateTranslate
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
  public function setEmailsTo(array $emailsTo): EmailTemplateTranslate
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
   * @return EmailTemplateTranslate
   */
  public function updateEmailsTo(string $id, EmailAddress $emailAddress): EmailTemplateTranslate
  {
    $this->emailsToCc[$id] = serialize($emailAddress);
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
  public function setEmailsToCc(array $emailsToCc): EmailTemplateTranslate
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
   * @return EmailTemplateTranslate
   */
  public function updateEmailsToCc(string $id, EmailAddress $emailAddress): EmailTemplateTranslate
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
  public function setEmailsToCci(array $emailsToCci): EmailTemplateTranslate
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
   * @return EmailTemplateTranslate
   */
  public function updateEmailsToCci(string $id, EmailAddress $emailAddress): EmailTemplateTranslate
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
  public function setEmailReplyTo(?string $emailReplyTo): EmailTemplateTranslate
  {
    $this->emailReplyTo = $emailReplyTo;
    return $this;
  }

}
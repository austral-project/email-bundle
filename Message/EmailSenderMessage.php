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

use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EntityBundle\Entity\EntityInterface;

/**
 * Austral EmailSender Messenger.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailSenderMessage
{

  /**
   * @var string|null
   */
  private ?string $emailTemplateKeyname;

  /**
   * @var string|null
   */
  private ?string $emailHistoryId;

  /**
   * @var string|null
   */
  private ?string $objectClassname;

  /**
   * @var mixed
   */
  private $objectId;

  /**
   * @var string
   */
  private string $language;

  /**
   * @var array
   */
  private array $vars;

  /**
   * EmailSenderEvent constructor.
   *
   * @param EmailTemplateInterface $emailTemplate
   * @param string $language
   * @param EntityInterface|null $object
   * @param array $vars
   * @param EmailHistoryInterface|null $emailHistory
   */
  public function __construct(EmailTemplateInterface $emailTemplate, string $language, ?EntityInterface $object = null, array $vars = array(), ?EmailHistoryInterface $emailHistory = null)
  {
    $this->emailTemplateKeyname = $emailTemplate->getKeyname();
    $this->emailHistoryId = $emailHistory ? $emailHistory->getId() : $emailHistory;
    $this->objectClassname = $object ? $object->getEntityName() : null;
    $this->objectId = $object ? $object->getId() : null;
    $this->language = $language;
    $this->vars = $vars;
  }

  /**
   * @return string
   */
  public function getEmailTemplateKeyname(): ?string
  {
    return $this->emailTemplateKeyname;
  }

  /**
   * @return string|null
   */
  public function getObjectClassname(): ?string
  {
    return $this->objectClassname;
  }

  /**
   * @return int|string|null
   */
  public function getObjectId()
  {
    return $this->objectId;
  }

  /**
   * @return string
   */
  public function getLanguage(): string
  {
    return $this->language;
  }

  /**
   * @return array
   */
  public function getVars(): array
  {
    return $this->vars;
  }

  /**
   * @return string
   */
  public function getEmailHistoryId()
  {
    return $this->emailHistoryId;
  }

  /**
   * @param string|null $emailHistoryId
   *
   * @return $this
   */
  public function setEmailHistoryId(?string $emailHistoryId = null): EmailSenderMessage
  {
    $this->emailHistoryId = $emailHistoryId;
    return $this;
  }

}
<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Event;

use Austral\EmailBundle\Entity\EmailHistory;
use Austral\EmailBundle\Entity\EmailTemplate;
use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;
use Austral\EntityBundle\Entity\EntityInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral EmailHistory Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailHistoryEvent extends Event
{

  const EVENT_AUSTRAL_EMAIL_HISTORY_CREATE = "austral.event.email_history.create";
  const EVENT_AUSTRAL_EMAIL_HISTORY_UPDATE = "austral.event.email_history.update";

  /**
   * @var EmailTemplate
   */
  private EmailTemplate $emailTemplate;

  /**
   * @var EntityInterface|null
   */
  private ?EntityInterface $object;

  /**
   * @var EmailHistory|null
   */
  private ?EmailHistory $emailHistory;

  /**
   * @var string|null
   */
  protected ?string $contentEmail = null;

  /**
   * EmailHistoryEvent constructor.
   *
   * @param EmailTemplate $emailTemplate
   * @param EntityInterface|null $object
   * @param EmailHistoryInterface|null $emailHistory
   */
  public function __construct(EmailTemplate $emailTemplate, EntityInterface $object = null, ?EmailHistoryInterface $emailHistory = null)
  {
    $this->emailTemplate = $emailTemplate;
    $this->object = $object;
    $this->emailHistory = $emailHistory;
  }

  /**
   * @return EmailTemplate
   */
  public function getEmailTemplate(): EmailTemplate
  {
    return $this->emailTemplate;
  }

  /**
   * @return EntityInterface|null
   */
  public function getObject(): ?EntityInterface
  {
    return $this->object;
  }

  /**
   * @return EmailHistoryInterface|null
   */
  public function getEmailHistory(): ?EmailHistoryInterface
  {
    return $this->emailHistory;
  }

  /**
   * @param EmailHistoryInterface $emailHistory
   *
   * @return EmailHistoryEvent
   */
  public function setEmailHistory(EmailHistoryInterface $emailHistory): EmailHistoryEvent
  {
    $this->emailHistory = $emailHistory;
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
  public function setContentEmail(?string $contentEmail): EmailHistoryEvent
  {
    $this->contentEmail = $contentEmail;
    return $this;
  }


}
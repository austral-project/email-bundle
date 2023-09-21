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
use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;
use Austral\EntityBundle\Entity\EntityInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral EmailSender Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailSenderEvent extends Event
{

  const EVENT_AUSTRAL_EMAIL_SENDER_SEND= "austral.event.email_sender.send";

  /**
   * @var string
   */
  private string $emailKeyname;

  /**
   * @var EntityInterface|null
   */
  private ?EntityInterface $object;

  /**
   * @var array
   */
  private array $vars;

  /**
   * @var array
   */
  private array $attachementFiles;

  /**
   * @var string|null
   */
  private ?string $language;

  /**
   * @var mixed
   */
  private $async;


  /**
   * @var EmailHistory|null
   */
  private ?EmailHistory $emailHistory = null;

  /**
   * EmailSenderEvent constructor.
   *
   * @param string $emailKeyname
   * @param string $language
   * @param ?EntityInterface $object
   * @param array $vars
   * @param array $attachementFiles
   * @param string $async
   */
  public function __construct(string $emailKeyname, string $language, ?EntityInterface $object = null, array $vars = array(), array $attachementFiles = array(), string $async = "default")
  {
    $this->emailKeyname = $emailKeyname;
    $this->object = $object;
    $this->vars = $vars;
    $this->attachementFiles = $attachementFiles;
    $this->language = $language;
    $this->async = $async;
  }

  /**
   * @return string
   */
  public function getEmailKeyname(): string
  {
    return $this->emailKeyname;
  }

  /**
   * @return EntityInterface|null
   */
  public function getObject(): ?EntityInterface
  {
    return $this->object;
  }

  /**
   * @return array
   */
  public function getVars(): array
  {
    return $this->vars;
  }

  /**
   * addVars
   *
   * @param string $key
   * @param $value
   * @return $this
   */
  public function addVars(string $key, $value): EmailSenderEvent
  {
    $this->vars[$key] = $value;
    return $this;
  }

  /**
   * @return array
   */
  public function getAttachementFiles(): array
  {
    return $this->attachementFiles;
  }

  /**
   * @return string|null
   */
  public function getLanguage(): ?string
  {
    return $this->language;
  }

  /**
   * @param string|null $language
   *
   * @return EmailSenderEvent
   */
  public function setLanguage(?string $language): EmailSenderEvent
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getAsync()
  {
    return $this->async;
  }

  /**
   * @return EmailHistory|null
   */
  public function getEmailHistory(): ?EmailHistory
  {
    return $this->emailHistory;
  }

  /**
   * @param EmailHistoryInterface|null $emailHistory
   *
   * @return EmailSenderEvent
   */
  public function setEmailHistory(?EmailHistoryInterface $emailHistory = null): EmailSenderEvent
  {
    $this->emailHistory = $emailHistory;
    return $this;
  }

}
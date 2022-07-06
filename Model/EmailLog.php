<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EmailBundle\Model;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;

/**
 * Austral EmailLog Model.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailLog extends Entity implements EntityInterface
{

  /**
   * @var string
   */
  protected $id;

  /**
   * @var \DateTime
   */
  protected \DateTime $date;

  /**
   * @var string
   */
  protected string $status;

  /**
   * @var string|null
   */
  protected ?string $message = null;

  /**
   * @var bool
   */
  protected bool $emailVerify = false;

  /**
   * Theme constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->date = new \DateTime();
  }

  public function __toString()
  {
    return $this->date->format("Y-m-d H:i:s");
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * @param string $id
   *
   * @return EmailLog
   */
  public function setId(string $id): EmailLog
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDate(): \DateTime
  {
    return $this->date;
  }

  /**
   * @param \DateTime $date
   *
   * @return EmailLog
   */
  public function setDate(\DateTime $date): EmailLog
  {
    $this->date = $date;
    return $this;
  }

  /**
   * @return string
   */
  public function getStatus(): string
  {
    return $this->status;
  }

  /**
   * @param string $status
   *
   * @return EmailLog
   */
  public function setStatus(string $status): EmailLog
  {
    $this->status = $status;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getMessage(): ?string
  {
    return $this->message;
  }

  /**
   * @param string $message
   *
   * @return EmailLog
   */
  public function setMessage(string $message): EmailLog
  {
    $this->message = $message;
    return $this;
  }

  /**
   * @return bool
   */
  public function isEmailVerify(): bool
  {
    return $this->emailVerify;
  }

  /**
   * @param bool $emailVerify
   *
   * @return EmailLog
   */
  public function setEmailVerify(bool $emailVerify): EmailLog
  {
    $this->emailVerify = $emailVerify;
    return $this;
  }

}
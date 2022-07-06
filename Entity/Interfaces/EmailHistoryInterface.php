<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Entity\Interfaces;

use Austral\EmailBundle\Model\EmailAddress;

/**
 * Austral History Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EmailHistoryInterface
{
  /**
   * @return string|null
   */
  public function getEmailTemplateKeyname(): ?string;

  /**
   * @param string|null $emailTemplateKeyname
   *
   * @return $this
   */
  public function setEmailTemplateKeyname(?string $emailTemplateKeyname): EmailHistoryInterface;

  /**
   * @return string|null
   */
  public function getStatus(): ?string;

  /**
   * @param string|null $status
   *
   * @return $this
   */
  public function setStatus(?string $status): EmailHistoryInterface;


  /**
   * @return bool
   */
  public function getAnonymise(): bool;

  /**
   * @param bool $anonymise
   *
   * @return $this
   */
  public function setAnonymise(bool $anonymise): EmailHistoryInterface;

  /**
   * @return string|null
   */
  public function getEmailFrom(): ?string;

  /**
   * @param string|null $emailFrom
   *
   * @return $this
   */
  public function setEmailFrom(?string $emailFrom): EmailHistoryInterface;

  /**
   * @return string
   */
  public function getEmailsToString(): string;

  /**
   * @return array
   */
  public function getEmailsTo(): array;

  /**
   * @param array $emailsTo
   *
   * @return $this
   */
  public function setEmailsTo(array $emailsTo): EmailHistoryInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return $this
   */
  public function updateEmailsTo(string $id, EmailAddress $emailAddress): EmailHistoryInterface;

  /**
   * @return string
   */
  public function getEmailsToCcString(): string;

  /**
   * @return array
   */
  public function getEmailsToCc(): array;

  /**
   * @param array $emailsToCc
   *
   * @return $this
   */
  public function setEmailsToCc(array $emailsToCc): EmailHistoryInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return $this
   */
  public function updateEmailsToCc(string $id, EmailAddress $emailAddress): EmailHistoryInterface;

  /**
   * @return string
   */
  public function getEmailsToCciString(): string;

  /**
   * @return array
   */
  public function getEmailsToCci(): array;

  /**
   * @param array $emailsToCci
   *
   * @return $this
   */
  public function setEmailsToCci(array $emailsToCci): EmailHistoryInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return $this
   */
  public function updateEmailsToCci(string $id, EmailAddress $emailAddress): EmailHistoryInterface;

  /**
   * @return string|null
   */
  public function getEmailReplyTo(): ?string;

  /**
   * @param string|null $emailReplyTo
   *
   * @return $this
   */
  public function setEmailReplyTo(?string $emailReplyTo): EmailHistoryInterface;

  /**
   * @return string|null
   */
  public function getEntitledEmail(): ?string;

  /**
   * @param string|null $entitledEmail
   *
   * @return EmailHistoryInterface
   */
  public function setEntitledEmail(?string $entitledEmail): EmailHistoryInterface;

  /**
   * @return string|null
   */
  public function getContentEmail(): ?string;

  /**
   * @param string|null $contentEmail
   *
   * @return $this
   */
  public function setContentEmail(?string $contentEmail): EmailHistoryInterface;

  /**
   * @return array
   */
  public function getLogs(): array;

  /**
   * @param array $logs
   *
   * @return $this
   */
  public function setLogs(array $logs): EmailHistoryInterface;

  /**
   * @return array
   */
  public function getVars(): array;

  /**
   * @param array $vars
   *
   * @return $this
   */
  public function setVars(array $vars): EmailHistoryInterface;
}

    
    
      
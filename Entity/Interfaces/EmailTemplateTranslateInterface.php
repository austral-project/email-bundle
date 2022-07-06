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
 * Austral EmailTranslate Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EmailTemplateTranslateInterface
{
  /**
   * @return bool
   */
  public function getHasTemplate(): bool;

  /**
   * @param bool $hasTemplate
   *
   * @return $this
   */
  public function setHasTemplate(bool $hasTemplate): EmailTemplateTranslateInterface;

  /**
   * @return string|null
   */
  public function getTemplatePath(): ?string;

  /**
   * @param string|null $templatePath
   *
   * @return $this
   */
  public function setTemplatePath(?string $templatePath): EmailTemplateTranslateInterface;

  /**
   * @return string|null
   */
  public function getEntitledEmail(): ?string;

  /**
   * @param string|null $entitledEmail
   *
   * @return EmailTemplateTranslateInterface
   */
  public function setEntitledEmail(?string $entitledEmail): EmailTemplateTranslateInterface;

  /**
   * @return string|null
   */
  public function getContentEmail(): ?string;

  /**
   * @param string|null $contentEmail
   *
   * @return $this
   */
  public function setContentEmail(?string $contentEmail): EmailTemplateTranslateInterface;

  /**
   * @return string|null
   */
  public function getEmailFrom(): ?string;

  /**
   * @param string|null $emailFrom
   *
   * @return $this
   */
  public function setEmailFrom(?string $emailFrom): EmailTemplateTranslateInterface;

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
  public function setEmailsTo(array $emailsTo): EmailTemplateTranslateInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailTemplateTranslateInterface
   */
  public function updateEmailsTo(string $id, EmailAddress $emailAddress): EmailTemplateTranslateInterface;

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
  public function setEmailsToCc(array $emailsToCc): EmailTemplateTranslateInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailTemplateTranslateInterface
   */
  public function updateEmailsToCc(string $id, EmailAddress $emailAddress): EmailTemplateTranslateInterface;

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
  public function setEmailsToCci(array $emailsToCci): EmailTemplateTranslateInterface;

  /**
   * @param string $id
   * @param EmailAddress $emailAddress
   *
   * @return EmailTemplateTranslateInterface
   */
  public function updateEmailsToCci(string $id, EmailAddress $emailAddress): EmailTemplateTranslateInterface;

  /**
   * @return string|null
   */
  public function getEmailReplyTo(): ?string;

  /**
   * @param string|null $emailReplyTo
   *
   * @return $this
   */
  public function setEmailReplyTo(?string $emailReplyTo): EmailTemplateTranslateInterface;
}

    
    
      
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

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral EmailView Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailTemplateViewEvent extends Event
{

  const EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_INIT_VARS = "austral.email_template_view.init_vars";
  const EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_RELOAD_VARS = "austral.email_template_view.reload_vars";

  /**
   * @var EmailTemplateInterface
   */
  private EmailTemplateInterface $emailTemplate;

  /**
   * @var array
   */
  private array $vars = array();

  /**
   * EmailViewEvent constructor.
   */
  public function __construct(EmailTemplateInterface $emailTemplate)
  {
    $this->emailTemplate = $emailTemplate;
  }

  /**
   * @return EmailTemplateInterface
   */
  public function getEmailTemplate(): EmailTemplateInterface
  {
    return $this->emailTemplate;
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
  public function setVars(array $vars): EmailTemplateViewEvent
  {
    $this->vars = $vars;
    return $this;
  }

}
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
use Austral\EmailBundle\Entity\EmailTemplate;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EmailBundle\Model\EmailAddress;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Services\ConfigReplaceDom;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Austral Email Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailTransform
{
  /**
   * @var Environment
   */
  protected Environment $twig;

  /**
   * @var EntityInterface
   */
  protected EntityInterface $object;

  /**
   * @var ConfigReplaceDom|null
   */
  protected ?ConfigReplaceDom $configReplaceDom;

  /**
   * @var EmailTemplateInterface|EntityTranslateMasterInterface|null
   */
  protected ?EmailTemplateInterface $emailTemplate = null;

  /**
   * @var bool
   */
  protected bool $anonymize = false;

  /**
   * @var array|mixed|string|null
   */
  protected array $vars = array();


  /**
   * ContentBlockSubscriber constructor.
   *
   * @param Environment $twig
   * @param EmailConfiguration $emailConfiguration
   * @param ConfigReplaceDom|null $configReplaceDom
   */
  public function __construct(Environment $twig, EmailConfiguration $emailConfiguration, ?ConfigReplaceDom $configReplaceDom = null)
  {
    $this->twig = $twig;
    $this->vars = AustralTools::getValueByKey($emailConfiguration->getConfig("defaults"), "vars", array());
    $this->configReplaceDom = $configReplaceDom;
  }

  /**
   * @param bool $anonymize
   *
   * @return $this
   */
  public function setAnonymize(bool $anonymize): EmailTransform
  {
    $this->anonymize = $anonymize;
    return $this;
  }

  /**
   * @param EmailTemplateInterface $emailTemplate
   *
   * @return $this
   */
  public function setEmailTemplate(EmailTemplateInterface $emailTemplate): EmailTransform
  {
    $this->emailTemplate = $emailTemplate;
    return $this;
  }

  /**
   * @param array $vars
   *
   * @return $this
   */
  public function addVars(array $vars): EmailTransform
  {
    $this->vars = array_merge($this->vars, $vars);
    return $this;
  }

  /**
   * @param bool $replace
   *
   * @return string|null
   */
  public function getContentEmail(bool $replace = false): ?string
  {
    try {
      if($this->emailTemplate->getTranslateCurrent())
      {
        if($this->emailTemplate->getType() == EmailTemplate::TYPE_TEMPLATE)
        {
          $emailTemplateHtml = $this->twig->render($this->emailTemplate->getTranslateCurrent()->getTemplatePath());
        }
        else
        {
          $emailTemplateHtml = $this->emailTemplate ? $this->emailTemplate->getTranslateCurrent()->getContentEmail() : null;
        }
        if($replace)
        {
          $emailTemplateHtml = AustralTools::replaceKeyByValue($emailTemplateHtml, $this->vars);
          $emailTemplateHtml = $this->configReplaceDom ? $this->configReplaceDom->replaceDom($emailTemplateHtml, UrlGeneratorInterface::ABSOLUTE_URL): $emailTemplateHtml;
        }
        return $emailTemplateHtml;
      }
      return "";
    }
    catch(\Exception $e) {
      return "";
    }
  }

  /**
   * @return string|null
   */
  public function getEntitledEmail(): ?string
  {
    return $this->emailTemplate ? AustralTools::replaceKeyByValue($this->emailTemplate->getTranslateCurrent()->getEntitledEmail(), $this->vars) : null;
  }



  /**
   * return string
   */
  public function getEmailFrom()
  {
    return $this->emailTemplate ? AustralTools::replaceKeyByValue($this->emailTemplate->getTranslateCurrent()->getEmailFrom(), $this->vars) : null;
  }

  /**
   * @return array
   */
  public function getEmailsTo(): array
  {
    $emails = array();
    if($this->emailTemplate)
    {
      /** @var EmailAddress $emailTemplateAddress */
      foreach($this->emailTemplate->getTranslateCurrent()->getEmailsTo() as $emailTemplateAddress)
      {
        $emails[] = $this->anonymizeEmail(AustralTools::replaceKeyByValue($emailTemplateAddress->getEmail(), $this->vars));
      }
    }
    return $emails;
  }

  /**
   * @return array
   */
  public function getEmailsToCc(): array
  {
    $emails = array();
    if($this->emailTemplate)
    {
      /** @var EmailAddress $emailTemplateAddress */
      foreach($this->emailTemplate->getTranslateCurrent()->getEmailsToCc() as $emailTemplateAddress)
      {
        $emails[] = $this->anonymizeEmail(AustralTools::replaceKeyByValue($emailTemplateAddress->getEmail(), $this->vars));
      }
    }
    return $emails;
  }

  /**
   * @return array
   */
  public function getEmailsToCci(): array
  {
    $emails = array();
    if($this->emailTemplate)
    {
      /** @var EmailAddress $emailTemplateAddress */
      foreach($this->emailTemplate->getTranslateCurrent()->getEmailsToCci() as $emailTemplateAddress)
      {
        $emails[] = $this->anonymizeEmail(AustralTools::replaceKeyByValue($emailTemplateAddress->getEmail(), $this->vars));
      }
    }
    return $emails;
  }

  /**
   * @return string|null
   */
  public function getEmailReplyTo(): ?string
  {
    return $this->emailTemplate ? $this->anonymizeEmail(AustralTools::replaceKeyByValue($this->emailTemplate->getTranslateCurrent()->getEmailReplyTo(), $this->vars)) : null;
  }

  /**
   * @param $email
   *
   * @return mixed|string
   */
  protected function anonymizeEmail($email)
  {
    if($this->anonymize)
    {
      preg_match("/(.*)@.*/", $email, $matches);
      $emailName = AustralTools::getValueByKey($matches, 1, "email");
      $email = str_replace($emailName, substr($emailName, 0, 1).str_repeat("*", strlen($emailName)-1), $email);
    }
    return $email;
  }



}
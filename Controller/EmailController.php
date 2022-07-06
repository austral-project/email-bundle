<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Controller;

use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;
use Austral\EmailBundle\Event\EmailTemplateViewEvent;
use Austral\EmailBundle\Services\EmailTransform;
use Austral\ToolsBundle\AustralTools;
use Austral\HttpBundle\Controller\HttpController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Austral Email Controller.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailController extends HttpController
{

  /**
   * @param Request $request
   * @param string $type
   * @param string $id
   * @param string|null $objectId
   *
   * @return Response
   */
  public function iframeView(Request $request, string $type, string $id, ?string $objectId = null): Response
  {
    /** @var EmailTransform $emailTransform */
    $emailTransform = $this->container->get('austral.email_transform');

    if($type == "history")
    {
      /** @var EmailHistoryInterface $emailHistory */
      $emailHistory = $this->container->get("austral.entity_manager.email_history")->retreiveById($id);
      if(!$emailHistory)
      {
        throw new NotFoundHttpException();
      }
      $emailTemplate = $this->container->get('austral.entity_manager.email_template')
        ->retreiveByKeyname($emailHistory->getEmailTemplateKeyname());

      if($emailHistory->getObjectClassname() && $emailHistory->getObjectId())
      {
        $object = $this->container->get('doctrine.orm.entity_manager')
          ->getRepository($emailHistory->getObjectClassname())
          ->retreiveById($emailHistory->getObjectId());

        if($object)
        {
          if($objectId !== $object->getId())
          {
            $emailTransform->setAnonymize($emailHistory->getAnonymise());
            if(!$emailHistory->getAnonymise())
            {
              $emailTransform->addVars(AustralTools::flattenArray(".", method_exists($object, "getEmailVars") ? $object->getEmailVars() : (array) $object, "object."));
            }
          }
          else
          {
            $emailTransform->addVars(AustralTools::flattenArray(".", method_exists($object, "getEmailVars") ? $object->getEmailVars() : (array) $object, "object."));
          }
        }
      }
      $vars = $emailHistory->getVars();

    }
    elseif($type == "template")
    {
      $emailTemplate = $this->container->get('austral.entity_manager.email_template')->retreiveById($id);
      if(!$emailTemplate)
      {
        throw new NotFoundHttpException();
      }
      $emailTemplateViewEvent = new EmailTemplateViewEvent($emailTemplate);
      $this->container->get('event_dispatcher')->dispatch($emailTemplateViewEvent, EmailTemplateViewEvent::EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_INIT_VARS);
      $vars = $emailTemplateViewEvent->getVars();
    }
    else
    {
      throw new NotFoundHttpException();
    }

    $emailTransform->addVars($vars)->setEmailTemplate($emailTemplate);
    return new Response($emailTransform->getContentEmail(true));
  }
  
  
}

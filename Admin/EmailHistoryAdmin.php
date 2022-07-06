<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Admin;

use Austral\EmailBundle\Entity\EmailHistory;
use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;

use Austral\AdminBundle\Admin\Event\ActionAdminEvent;
use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EmailBundle\Event\EmailSenderEvent;
use Austral\EmailBundle\Services\EmailTransform;

use Austral\EntityBundle\Entity\EntityInterface;

use Austral\FormBundle\Field as Field;
use Austral\FormBundle\Mapper\Fieldset;

use Austral\ListBundle\Column as Column;
use Austral\ListBundle\DataHydrate\DataHydrateORM;

use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\QueryBuilder;

use Exception;

/**
 * EmailHistory Admin .
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EmailHistoryAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @return array
   */
  public function getEvents() : array
  {
    return array(
      FormAdminEvent::EVENT_END =>  "formEnd"
    );
  }

  /**
   * @param ActionAdminEvent $actionAdminEvent
   */
  public function sendEmail(ActionAdminEvent $actionAdminEvent)
  {

    try {
      $status = "success";

      /** @var EmailHistoryInterface $emailHistory */
      $emailHistory = $actionAdminEvent->getObject();

      $object = null;
      if($emailHistory->getObjectClassname() && $emailHistory->getObjectId())
      {
        /** @var EntityInterface $object */
        $object = $actionAdminEvent->getAdminHandler()->getEntityManager()
          ->getRepository($emailHistory->getObjectClassname())
          ->retreiveById($emailHistory->getObjectId());
      }

      $emailEvent = new EmailSenderEvent($emailHistory->getEmailTemplateKeyname(),
        $actionAdminEvent->getRequest()->getLocale(),
        $object,
        $emailHistory->getVars()
      );
      $emailEvent->setEmailHistory($emailHistory);
      $actionAdminEvent->getAdminHandler()->getDispatcher()->dispatch($emailEvent, EmailSenderEvent::EVENT_AUSTRAL_EMAIL_SENDER_SEND);

    } catch (\Exception $e) {
      $status = "error";
    }

    $actionAdminEvent->getAdminHandler()->addFlash($status,
      $actionAdminEvent->getAdminHandler()->getTranslate()->trans(
        "action.send-email.{$status}",
        array('%name%' => $emailHistory->__toString()), "austral"
      )
    );

    $actionAdminEvent->setRedirecturl(
      $actionAdminEvent->getCurrentModule()->generateUrl("edit", array("id"=>$emailHistory->getId()))
    );
  }

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->getSection("default")
        ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
          $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder->orderBy("root.created", "DESC");
          });
        })
        ->addColumn(new Column\Value("emailTemplateKeyname"))
        ->addColumn(new Column\Template("logs", "fields.logs.entitled", "@AustralEmail/Admin/Components/logs.html.twig"))
        ->addColumn(new Column\Date("updated", null, "d/m/Y"))
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->add(Field\TemplateField::create("buttonResend", "@AustralEmail/Admin/Components/button.html.twig", array(), array(
          "link"      =>  $this->module->generateUrl("send-email", array("id"=>$formAdminEvent->getFormMapper()->getObject()->getId())),
          "entitled"  =>  "button.email.resend",
          "picto"     =>  "austral-picto-refresh"
        )))
      ->end()

      ->addFieldset("fieldset.content")
        ->add(Field\TemplateField::create("emailInfo", "@AustralEmail/Admin/Components/email-info.html.twig"))
      ->end()
      ->addFieldset("fieldset.logs")
        ->add(Field\TemplateField::create("detailLog", "@AustralEmail/Admin/Components/detail-logs.html.twig", array('entitled'=>false)))
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   */
  protected function formEnd(FormAdminEvent $formAdminEvent)
  {
    /** @var EmailHistory $emailHistory */
    $emailHistory = $formAdminEvent->getFormMapper()->getObject();

    /** @var EmailTransform $emailTransform */
    $emailTransform = $this->container->get('austral.email_transform');
    $emailTransform->setAnonymize($emailHistory->getAnonymise());

    if($emailHistory->getEmailTemplateKeyname())
    {
      $emailTemplate = $this->container->get('austral.entity_manager.email_template')
        ->retreiveByKeyname($emailHistory->getEmailTemplateKeyname());
      $emailTransform->addVars($emailHistory->getVars())->setEmailTemplate($emailTemplate);

      if($emailHistory->getObjectClassname() && $emailHistory->getObjectId() )
      {
        $object = $this->container->get('doctrine.orm.entity_manager')
          ->getRepository($emailHistory->getObjectClassname())
          ->retreiveById($emailHistory->getObjectId());
        $emailTransform->addVars(AustralTools::flattenArray(".", method_exists($object, "getEmailVars") ? $object->getEmailVars() : (array) $object, "object."));
      }

      $formAdminEvent->getTemplateParameters()->addParameters("emailTransform", $emailTransform);
    }
    else
    {
      $formAdminEvent->getTemplateParameters()->addParameters("emailTransform", $emailTransform);
    }

  }

}
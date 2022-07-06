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

use Austral\AdminBundle\Admin\Event\ActionAdminEvent;
use Austral\EmailBundle\Configuration\EmailConfiguration;
use Austral\EmailBundle\Entity\EmailTemplate as EmailTemplateAlias;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;

use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EmailBundle\Event\EmailSenderEvent;
use Austral\EmailBundle\Event\EmailTemplateViewEvent;
use Austral\EmailBundle\Form\Type\EmailAddressFormType;
use Austral\EmailBundle\Model\EmailAddress;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\FormBundle\Field as Field;
use Austral\FormBundle\Mapper\Fieldset;
use Austral\FormBundle\Mapper\FormMapper;
use Austral\FormBundle\Mapper\GroupFields;
use Austral\ListBundle\Column as Column;
use Austral\ToolsBundle\AustralTools;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Constraints;

use Exception;

/**
 * Email Admin .
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EmailTemplateAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @return array
   */
  public function getEvents() : array
  {
    return array(
      FormAdminEvent::EVENT_UPDATE_BEFORE =>  "formUpdateBefore",
      FormAdminEvent::EVENT_END =>  "formEnd"
    );
  }

  /**
   * @param ActionAdminEvent $actionAdminEvent
   *
   * @throws \ReflectionException
   */
  public function sendEmail(ActionAdminEvent $actionAdminEvent)
  {

    /** @var EmailTemplateInterface $emailTemplate */
    $emailTemplate = $actionAdminEvent->getObject();

    $formMapper = new FormMapper($this->container->get('event_dispatcher'));
    $formMapper->setTranslateDomain("austral")->setPathToTemplateDefault("@AustralAdmin/Form/Components/Fields");

    $formMapper->setName("form_email_send")
      ->setFormTypeAction("edit")
      ->setTranslateDomain("austral")
      ->setModule($this->module);
    $defaultValues = array();

    $fieldset = $formMapper->addFieldset("fieldset.parametersSendEmail");
    foreach($emailTemplate->getVars() as $varName)
    {
      $varNameField = str_replace(".", "__POINT__", $varName);
      $fieldset->add(Field\TextField::create($varNameField, array("entitled"=>"%$varName%", "required"=>true)));
    }

    $fieldset = $formMapper->addFieldset("fieldset.parametersAutoSendEmail");
    foreach(AustralTools::getValueByKey($this->container->get("austral.email.config")->getConfig("defaults"), "vars", array()) as $key => $var)
    {
      $defaultValues[$key] = $var;
      $varNameField = str_replace(".", "__POINT__", $key);
      $fieldset->add(Field\TextField::create($varNameField, array("entitled"=>"%$key%", "required"=>true)));
    }

    $formMapper->addFieldset("fieldset.viewer")
      ->add(Field\TemplateField::create("viewer", "@AustralEmail/Admin/Components/iframe.html.twig"))
    ->end();

    if($actionAdminEvent->getAdminHandler()->getSession()->has("austral_form"))
    {
      $formDataSession = $actionAdminEvent->getAdminHandler()->getSession()->get("austral_form", array());
      $actionAdminEvent->getAdminHandler()->getSession()->remove("austral_form");
      $formMapper->setFormStatus($formDataSession['status'])->setFormSend($formDataSession['send']);
    }

    $formType = clone $this->container->get('austral.form.type.master')->setFormMapper($formMapper);

    /** @var Form $form */
    $form = $this->container->get('form.factory')->createNamed("form_email_send", get_class($formType), $defaultValues);
    if($actionAdminEvent->getRequest()->getMethod() == 'POST')
    {
      $formMapper->setFormStatus(null)->setFormSend(true);
      $form->handleRequest($actionAdminEvent->getRequest());
      if($form->isSubmitted()) {
        $vars = $form->getData();
        if($form->isValid())
        {
          $varsFinal = array();
          foreach ($vars as $key => $value) {
            $varsFinal[str_replace("__POINT__", ".", $key)] = $value;
          }
          $formMapper->setFormStatus("success");
          $emailEvent = new EmailSenderEvent($emailTemplate->getKeyname(), $emailTemplate->getLanguageCurrent(), null, $varsFinal);
          $actionAdminEvent->getAdminHandler()->getDispatcher()->dispatch($emailEvent, EmailSenderEvent::EVENT_AUSTRAL_EMAIL_SENDER_SEND);
        }
        else
        {
          $formMapper->setFormStatus("error");
        }
      }
      else
      {
        $formMapper->setFormStatus("error");
      }
      $actionAdminEvent->getAdminHandler()->addFlash($formMapper->getFormStatus(),
        $actionAdminEvent->getAdminHandler()->getTranslate()->trans(
          "action.send-email.{$formMapper->getFormStatus()}",
          array('%name%' => $emailTemplate->__toString()), "austral"
        )
      );
      if($formMapper->getFormStatus() == "success")
      {
        $actionAdminEvent->setRedirecturl(
          $actionAdminEvent->getCurrentModule()->generateUrl("send-email", array("id"=>$emailTemplate->getId()))
        );
        $actionAdminEvent->getAdminHandler()->getSession()->set("austral_form", array(
            "send"    =>  $formMapper->getFormSend(),
            "status"  =>  $formMapper->getFormStatus()
          )
        );
      }
    }


    $actionAdminEvent->getTemplateParameters()->setPath("@AustralAdmin/Form/index.html.twig");
    $actionAdminEvent->getTemplateParameters()->addParameters("form", array(
      "mapper"    =>  $formMapper,
      "form"      =>  $form,
      "view"      =>  $form->createView(),
      "object"    =>  $emailTemplate
    ));


  }

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->addColumn(new Column\Value("name"))
      ->addColumn(new Column\Value("keyname"))
      ->addColumn(new Column\Template("emails", "fields.emailsList.entitled", "@AustralEmail/Admin/Components/emails.html.twig"))
      ->addColumn(new Column\Date("updated", null, "d/m/Y"));
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
        ->add(Field\TemplateField::create("button", "@AustralEmail/Admin/Components/button.html.twig", array(), array(
          "link"      =>  $formAdminEvent->getAdminHandler()->generateUrl("email_iframe_view", array("type"=>"template", "id"=>$formAdminEvent->getFormMapper()->getObject()->getId())),
          "entitled"  =>  "button.email.view-render",
          "picto"     =>  "austral-picto-corner-forward",
          "target"    =>  true
        )))
        ->add(Field\TemplateField::create("button-send", "@AustralEmail/Admin/Components/button.html.twig", array(), array(
          "link"      =>  $this->module->generateUrl("send-email", array("id"=>$formAdminEvent->getFormMapper()->getObject()->getId())),
          "entitled"  =>  "button.email.test-send",
          "picto"     =>  "austral-picto-refresh"
        )))
        ->add(Field\ChoiceField::create("isEnabled",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\TemplateField::create("vars", "@AustralEmail/Admin/Components/vars.html.twig", array("entitled"=>"fields.vars.entitled")))
      ->end()
      ->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("name"))
        ->add(Field\TextField::create("keyname"))
      ->end()
      ->addFieldset("fieldset.emails")
        ->add(Field\TextField::create("emailFrom")
        )
        ->addGroup("emailsTo", "fields.emailsTo.entitled")
          ->add($this->createCollectionAddress($formAdminEvent, "emailsTo"))
        ->end()
        ->addGroup("emailsToCc", "fields.emailsToCc.entitled")
          ->add($this->createCollectionAddress($formAdminEvent, "emailsToCc"))
        ->end()
        ->addGroup("emailsToCci", "fields.emailsToCci.entitled")
          ->add($this->createCollectionAddress($formAdminEvent, "emailsToCci"))
        ->end()
        ->add(Field\TextField::create("emailReplyTo")
        )
      ->end()
      ->addFieldset("fieldset.content")
        ->add(Field\SelectField::create("type", array(
              "choices.email.type.". EmailTemplateAlias::TYPE_WYSIWYG       =>  EmailTemplateAlias::TYPE_WYSIWYG,
              "choices.email.type.".EmailTemplateAlias::TYPE_TEMPLATE       =>  EmailTemplateAlias::TYPE_TEMPLATE,
            ),
            array(
              "required"=>true,
              "attr" => array(
                'data-view-by-choices' =>  json_encode(array(
                  EmailTemplateAlias::TYPE_WYSIWYG           =>  "element-view-".EmailTemplateAlias::TYPE_WYSIWYG,
                  EmailTemplateAlias::TYPE_TEMPLATE          =>  "element-view-".EmailTemplateAlias::TYPE_TEMPLATE,
                ))
              )
            )
          )
        )
        ->add(Field\TextField::create("entitledEmail", array("required"=>true)))
        ->add(Field\WysiwygField::create("contentEmail", array("container" =>  array('class'=>"view-element-by-choices  element-view-".EmailTemplateAlias::TYPE_WYSIWYG))))
        ->add(Field\TextField::create("templatePath", array("container" =>  array('class'=>"view-element-by-choices  element-view-".EmailTemplateAlias::TYPE_TEMPLATE))))
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  protected function formUpdateBefore(FormAdminEvent $formAdminEvent)
  {
    /** @var EmailTemplateInterface|EntityInterface $object */
    $object = $formAdminEvent->getFormMapper()->getObject();
    if(!$object->getKeyname()) {
      $object->setKeyname($object->getName());
    }

    $emailTemplateViewEvent = new EmailTemplateViewEvent($object);
    $formAdminEvent->getAdminHandler()->getDispatcher()->dispatch($emailTemplateViewEvent, EmailTemplateViewEvent::EVENT_AUSTRAL_EMAIL_TEMPLATE_VIEW_RELOAD_VARS);

  }

  /**
   * @param FormAdminEvent $formAdminEvent
   */
  protected function formEnd(FormAdminEvent  $formAdminEvent)
  {
    /** @var EmailConfiguration $emailConfig */
    $emailConfig = $this->container->get('austral.email.config');
    $defaults = $emailConfig->getConfig("defaults", array());
    $formAdminEvent->getTemplateParameters()->addParameters("emailVarsDefault", AustralTools::getValueByKey($defaults, "vars", array()));
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   * @param $fieldName
   *
   * @return Field\CollectionEmbedField
   * @throws \ReflectionException
   * @throws Exception
   */
  protected function createCollectionAddress(FormAdminEvent $formAdminEvent, $fieldName): Field\CollectionEmbedField
  {
    $emailAddressFormMapper = new FormMapper();
    $emailAddress = new EmailAddress();
    $emailAddressFormMapper->setObject($emailAddress)
      ->addGroup("generalInformations")
        ->add(Field\TextField::create("email", array(
              'container' => array("class"=>"animate"),
              "group"       =>  array(
                'size'  => GroupFields::SIZE_COL_12
              )
            )
          )->setConstraints(array(
              new Constraints\NotNull(),
              new Constraints\Length(array(
                  "max" => 255,
                  "maxMessage" => "errors.length.max"
                )
              )
            )
          )
        )
      ->end();

    $formAdminEvent->getFormMapper()->addSubFormMapper($fieldName, $emailAddressFormMapper);
    /** @var EmailAddressFormType $emailAddressFormType */
    $emailAddressFormType = $this->container->get('austral.email.emailAddress_form_type')->setFormMapper($emailAddressFormMapper);
    return Field\CollectionEmbedField::create($fieldName, array(
        "button"              =>  "button.new.emailAddress",
        "collections"         =>  array("objects" =>  $fieldName),
        "allow"               =>  array(
          "child"               =>  false,
          "add"                 =>  true,
          "delete"              =>  true,
        ),
        "entry"               =>  array("type"  => get_class($emailAddressFormType)),
        "prototype"           =>  array("data"  =>  $emailAddress),
        "sortable"            =>  array(
          "value"               =>  "id",
        ),
      )
    );
  }

}
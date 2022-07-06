<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\EmailBundle\Entity;

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateTranslateInterface;
use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Austral Email Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class EmailTemplate extends Entity implements EmailTemplateInterface, EntityInterface, EntityTranslateMasterInterface
{

  const TYPE_TEMPLATE = "template";
  const TYPE_WYSIWYG = "wysiwyg";

  use EntityTranslateMasterTrait {
    getTranslateCurrent as getTranslateCurrentTrait;
  }
  use EntityTimestampableTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @ORM\OneToMany(targetEntity="\Austral\EmailBundle\Entity\Interfaces\EmailTemplateTranslateInterface", mappedBy="master", cascade={"persist", "remove"})
   */
  protected Collection $translates;

  /**
   * @var boolean
   * @ORM\Column(name="is_enabled", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $isEnabled = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="type", type="string", length=255, nullable=false )
   */
  protected ?string $type = null;

  /**
   * @var string|null
   * @ORM\Column(name="name", type="string", length=255, nullable=false )
   */
  protected ?string $name = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="keyname", type="string", length=255, nullable=false )
   */
  protected ?string $keyname = null;

  /**
   * @var array
   * @ORM\Column(name="vars", type="json", nullable=false)
   */
  protected array $vars = array();

  /**
   * Email constructor.
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->translates = new ArrayCollection();
    $this->type = self::TYPE_WYSIWYG;
    $this->isEnabled = false;
  }

  /**
   * @return EntityTranslateChildInterface|EmailTemplateTranslateInterface|null
   * @throws Exception
   */
  public function getTranslateCurrent(): ?EntityTranslateChildInterface
  {
    return $this->getTranslateCurrentTrait();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? "";
  }

  /**
   * Get name
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): EmailTemplate
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Get keyname
   * @return string|null
   */
  public function getKeyname(): ?string
  {
    return $this->keyname;
  }

  /**
   * Set keyname
   *
   * @param string|null $keyname
   *
   * @return $this
   */
  public function setKeyname(?string $keyname): EmailTemplate
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsEnabled(): bool
  {
    return $this->isEnabled;
  }

  /**
   * @param bool $isEnabled
   *
   * @return EmailTemplate
   */
  public function setIsEnabled(bool $isEnabled): EmailTemplate
  {
    $this->isEnabled = $isEnabled;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getType(): ?string
  {
    return $this->type;
  }

  /**
   * @param string|null $type
   *
   * @return $this
   */
  public function setType(?string $type): EmailTemplate
  {
    $this->type = $type;
    return $this;
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
  public function setVars(array $vars): EmailTemplate
  {
    $this->vars = $vars;
    return $this;
  }

  /**
   * @param string $var
   *
   * @return $this
   */
  public function addVars(string $var): EmailTemplate
  {
    $this->vars[] = $var;
    return $this;
  }



}
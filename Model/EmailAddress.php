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
use Ramsey\Uuid\Uuid;

/**
 * Austral EmailAddress Model.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailAddress extends Entity implements EntityInterface
{

  /**
   * @var string
   */
  protected $id;

  /**
   * @var string|null
   */
  protected ?string $email = null;

  /**
   * Theme constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
  }

  public function __toString()
  {
    return $this->email;
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
   * @return EmailAddress
   */
  public function setId(string $id): EmailAddress
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }

  /**
   * @param string|null $email
   *
   * @return EmailAddress
   */
  public function setEmail(?string $email): EmailAddress
  {
    $this->email = $email;
    return $this;
  }

}
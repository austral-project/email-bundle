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

use Austral\ToolsBundle\AustralTools;

/**
 * Austral EmailAttachementFile Model.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailAttachementFile
{

  /**
   * @var string
   */
  protected string $path;

  /**
   * @var string|null
   */
  protected ?string $name = null;

  /**
   * @var string|null
   */
  protected ?string $mimeType = null;


  /**
   * Theme constructor.
   */
  public function __construct($path, ?string $name = null, ?string $mimeType = null)
  {
    $this->path = $path;
    $this->name = $name ?? pathinfo($path, PATHINFO_BASENAME);
    $this->mimeType = $mimeType ?? AustralTools::mimeType($path);
  }

  /**
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @param string $path
   *
   * @return EmailAttachementFile
   */
  public function setPath(string $path): EmailAttachementFile
  {
    $this->path = $path;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @param string|null $name
   *
   * @return EmailAttachementFile
   */
  public function setName(?string $name): EmailAttachementFile
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getMimeType(): ?string
  {
    return $this->mimeType;
  }

  /**
   * @param string|null $mimeType
   *
   * @return EmailAttachementFile
   */
  public function setMimeType(?string $mimeType): EmailAttachementFile
  {
    $this->mimeType = $mimeType;
    return $this;
  }

}
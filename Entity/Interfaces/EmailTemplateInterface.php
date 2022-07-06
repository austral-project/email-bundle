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

/**
 * Austral Email Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EmailTemplateInterface
{

  /**
   * Get name
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * Set name
   *
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): EmailTemplateInterface;

  /**
   * Get keyname
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * Set keyname
   *
   * @param string|null $keyname
   *
   * @return $this
   */
  public function setKeyname(?string $keyname): EmailTemplateInterface;

  /**
   * @return bool
   */
  public function getIsEnabled(): bool;

  /**
   * @param bool $isEnabled
   *
   * @return $this
   */
  public function setIsEnabled(bool $isEnabled): EmailTemplateInterface;

  /**
   * @return string|null
   */
  public function getType(): ?string;

  /**
   * @param string|null $type
   *
   * @return $this
   */
  public function setType(?string $type): EmailTemplateInterface;

  /**
   * @return array
   */
  public function getVars(): array;

  /**
   * @param array $vars
   *
   * @return $this
   */
  public function setVars(array $vars): EmailTemplateInterface;

}

    
    
      
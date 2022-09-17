<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\EntityManager;

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EmailBundle\Repository\EmailTemplateRepository;

use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Austral Email EntityManager.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class EmailTemplateEntityManager extends EntityManager
{

  /**
   * @var EmailTemplateRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return EmailTemplateInterface|TranslateMasterInterface
   */
  public function create(array $values = array()): EmailTemplateInterface
  {
    /** @var EmailTemplateInterface|TranslateMasterInterface $object */
    $object = parent::create($values);
    $object->setCurrentLanguage($this->currentLanguage);
    $object->createNewTranslateByLanguage();
    return $object;
  }

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return EmailTemplateInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?EmailTemplateInterface
  {
    return $this->repository->retreiveByKeyname($keyname, $closure);
  }

}

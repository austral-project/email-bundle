<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Repository;

use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;

use Austral\EntityBundle\Repository\EntityRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Austral Email Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailTemplateRepository extends EntityRepository
{

  /**
   * @param string $keyname
   * @param \Closure|null $closure
   *
   * @return EmailTemplateInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname(string $keyname, \Closure $closure = null): ?EmailTemplateInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->leftJoin("root.translates", "translates")->addSelect("translates")
      ->where("root.keyname = :keyname")
      ->setParameter("keyname", $keyname);

    $queryBuilder = $this->queryBuilderExtends("retreive-by-key", $queryBuilder);
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

}

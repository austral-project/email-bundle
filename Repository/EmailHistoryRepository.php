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
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Repository\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * Austral EmailHistory Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailHistoryRepository extends EntityRepository
{

  /**
   * @param EmailTemplateInterface $emailTemplate
   * @param EntityInterface $object
   *
   * @return int|mixed|string|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByEmailTemplateIdAndObject(EmailTemplateInterface $emailTemplate, EntityInterface $object)
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->andWhere("root.emailTemplateKeyname = :emailTemplateKeyname")
      ->andWhere("root.objectClassname = :objectClassname")
      ->andWhere("root.objectId = :objectId")
      ->setParameter("emailTemplateKeyname", $emailTemplate->getKeyname())
      ->setParameter("objectClassname", $object->getEntityName())
      ->setParameter("objectId", $object->getId())
      ->setMaxResults(1);
    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }



}

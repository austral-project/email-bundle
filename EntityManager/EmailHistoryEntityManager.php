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

use Austral\EmailBundle\Entity\Interfaces\EmailHistoryInterface;
use Austral\EmailBundle\Entity\Interfaces\EmailTemplateInterface;
use Austral\EmailBundle\Repository\EmailHistoryRepository;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\EntityManager\EntityManager;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Austral EmailHistory EntityManager.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class EmailHistoryEntityManager extends EntityManager
{

  /**
   * @var EmailHistoryRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return EmailHistoryInterface
   */
  public function create(array $values = array()): EmailHistoryInterface
  {
    return parent::create($values);
  }

  /**
   * @param EmailTemplateInterface $emailTemplate
   * @param EntityInterface $object
   *
   * @return int|mixed|string|null
   * @throws NonUniqueResultException
   */
  public function retreiveByEmailTemplateIdAndObject(EmailTemplateInterface $emailTemplate, EntityInterface $object)
  {
    return $this->repository->retreiveByEmailTemplateIdAndObject($emailTemplate, $object);
  }


}

<?php
/*
 * This file is part of the Austral Email Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EmailBundle\Form\Type;

use Austral\EmailBundle\Model\EmailAddress;

use Austral\FormBundle\Form\Type\FormType;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Austral EmailAddress Form Type.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EmailAddressFormType extends FormType
{

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => EmailAddress::class,
    ]);
  }

}
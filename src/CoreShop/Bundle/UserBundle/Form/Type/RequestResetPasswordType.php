<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $identifier = $options['reset_identifier'];
        $typeClass = $identifier === 'email' ? EmailType::class : TextType::class;

        $builder->add($identifier, $typeClass, [
            'label' => sprintf('coreshop.form.customer.%s', $identifier)
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('reset_identifier', 'email');
        $resolver->setAllowedTypes('reset_identifier', 'string');
        $resolver->setAllowedValues('reset_identifier', ['email', 'username']);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_request_reset_password';
    }
}

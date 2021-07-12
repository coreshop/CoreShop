<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CustomerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerLoginType extends AbstractType
{
    protected string $loginIdentifier;

    public function __construct(string $loginIdentifier)
    {
        $this->loginIdentifier = $loginIdentifier;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $loginIdentifierLabel = sprintf('coreshop.form.login.%s', $this->loginIdentifier);

        $builder
            ->add('_username', TextType::class, [
                'label' => $loginIdentifierLabel,
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'coreshop.form.login.password',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label'    => 'coreshop.form.login.remember_me',
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_customer_login';
    }
}

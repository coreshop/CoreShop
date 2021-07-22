<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Valid;

class CustomerRegistrationType extends AbstractType
{
    /**
     * @var string[]
     */
    protected $validationGroups = [];

    protected $loginIdentifier;

    /**
     * @param string[] $validationGroups
     * @param string $loginIdentifier
     */
    public function __construct(array $validationGroups, string $loginIdentifier)
    {
        $this->validationGroups = $validationGroups;
        $this->loginIdentifier = $loginIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('customer', CustomerType::class, [
                'label' => 'coreshop.form.customer_registration.customer',
                'label_attr' => [
                    'class' => 'cs-customer',
                ],
                'allow_password_field' => true,
                'allow_username' => $this->loginIdentifier === 'username',
                'constraints' => [
                    new Valid(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('address', AddressType::class, [
                'label' => 'coreshop.form.customer_registration.address',
                'label_attr' => [
                    'class' => 'cs-address',
                ],
                'constraints' => [
                    new Valid(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('termsAccepted', CheckboxType::class, array(
                'label' => 'coreshop.form.customer.terms',
                'mapped' => false,
                'validation_groups' => $this->validationGroups,
                'constraints' => new IsTrue(['groups' => $this->validationGroups]),
            ))
            ->add('submit', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_customer_registration';
    }
}

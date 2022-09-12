<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\Order;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentType extends AbstractResourceType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('store', null);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('cancel', SubmitType::class, [
            'label' => 'coreshop.form.order.revise.cancel',
        ]);

        $builder->add('submitPayment', SubmitType::class, [
            'label' => 'coreshop.form.order.revise.submit_payment',
        ]);
    }

    public function getParent(): string
    {
        return \CoreShop\Bundle\CoreBundle\Form\Type\Checkout\PaymentType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_order_payment';
    }
}

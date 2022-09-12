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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\OrderBundle\Form\Type\AddToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $originalCartItem = $builder->get('cartItem');

        $builder->add('cartItem', CartItemType::class, [
            'allow_units' => true,
            'constraints' => $originalCartItem->getOption('constraints'),
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddToCartType::class];
    }
}

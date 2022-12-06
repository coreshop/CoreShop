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

namespace CoreShop\Bundle\WishlistBundle\Form\Type;

use CoreShop\Bundle\StorageListBundle\Form\Type\AddToSelectableStorageListType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class AddToSelectableWishlistType extends AddToSelectableStorageListType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('storageListItem', WishlistItemType::class, [
            'constraints' => [new Valid(['groups' => $this->validationGroups])],
        ]);
        $builder->add('storageList', WishlistChoiceType::class);
    }
}

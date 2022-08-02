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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Form\DataMapper;

use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class CartItemQuantityDataMapper implements DataMapperInterface
{
    public function __construct(private StorageListItemQuantityModifierInterface $cartItemQuantityModifier, private DataMapperInterface $propertyPathDataMapper)
    {
    }

    public function mapDataToForms($viewData, $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $formsOtherThanQuantity = [];
        $quantityForm = null;

        //First map all the other fields, then map quantity.
        foreach ($forms as $form) {
            if ('quantity' === $form->getName()) {
                $quantityForm = $form;

                $targetQuantity = $form->getData();
                $this->cartItemQuantityModifier->modify($viewData, (float)$targetQuantity);

                continue;
            }

            $formsOtherThanQuantity[] = $form;
        }

        if (!empty($formsOtherThanQuantity)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanQuantity, $viewData);
        }

        if (null !== $quantityForm) {
            $targetQuantity = $quantityForm->getData();
            $this->cartItemQuantityModifier->modify($viewData, (float)$targetQuantity);
        }
    }
}

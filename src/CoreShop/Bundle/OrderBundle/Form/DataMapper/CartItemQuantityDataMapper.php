<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Form\DataMapper;

use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class CartItemQuantityDataMapper implements DataMapperInterface
{
    /**
     * @var StorageListItemQuantityModifierInterface
     */
    private $cartItemQuantityModifier;

    /**
     * @var DataMapperInterface
     */
    private $propertyPathDataMapper;

    /**
     * @param StorageListItemQuantityModifierInterface $cartItemQuantityModifier
     * @param DataMapperInterface                      $propertyPathDataMapper
     */
    public function __construct(
        StorageListItemQuantityModifierInterface $cartItemQuantityModifier,
        DataMapperInterface $propertyPathDataMapper
    ) {
        $this->cartItemQuantityModifier = $cartItemQuantityModifier;
        $this->propertyPathDataMapper = $propertyPathDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        $formsOtherThanQuantity = [];
        $quantityForm = null;

        //First map all the other fields, then map quantity.
        foreach ($forms as $key => $form) {
            if ('quantity' === $form->getName()) {
                $quantityForm = $form;

                continue;
            }

            $formsOtherThanQuantity[] = $form;
        }

        if (!empty($formsOtherThanQuantity)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanQuantity, $data);
        }

        if (null !== $quantityForm) {
            $targetQuantity = $quantityForm->getData();
            $this->cartItemQuantityModifier->modify($data, (float) $targetQuantity);
        }
    }
}

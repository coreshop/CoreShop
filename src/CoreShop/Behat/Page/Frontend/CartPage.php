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

namespace CoreShop\Behat\Page\Frontend;

use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;

class CartPage extends AbstractFrontendPage implements CartPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_cart_summary';
    }

    public function isEmpty(): bool
    {
        return false !== strpos($this->getElement('cart_empty')->getText(), 'Your cart is empty.');
    }

    public function isSingleItemOnPage(): bool
    {
        $items = $this->getElement('cart_items')->findAll('css', '[data-test-cart-item-row]');

        return 1 === count($items);
    }

    public function hasItemNamed(string $name): bool
    {
        return $this->hasItemWith($name, '[data-test-cart-item-name]');
    }

    public function hasProductInUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): bool
    {
        return null !== $this->getElement('cart_item_unit',
                [
                    '%unitId%' => $unitDefinition->getId(),
                    '%name%' => $name,
                ]
            );
    }

    public function getItemUnitPrice(string $name): string
    {
        $unitPrice = $this->getElement('item_unit_price', ['%name%' => $name]);

        return trim($unitPrice->getText());
    }

    public function getItemUnitPriceWithUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): string
    {
        $unitPrice = $this->getElement('item_unit_price_unit', ['%name%' => $name, '%unitId%' => $unitDefinition->getId()]);

        return trim($unitPrice->getText());
    }

    public function getItemTotalPrice(string $productName): string
    {
        $unitPrice = $this->getElement('item_total_price', ['%name%' => $productName]);

        return trim($unitPrice->getText());
    }

    public function getItemTotalPriceWithUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): string
    {
        $unitPrice = $this->getElement('item_total_price_unit', ['%name%' => $name, '%unitId%' => $unitDefinition->getId()]);

        return trim($unitPrice->getText());
    }

    public function getQuantity(string $productName): int
    {
        return (int)$this->getElement('item_quantity_input', ['%name%' => $productName])->getValue();
    }

    public function changeQuantity(string $productName, string $quantity): void
    {
        $this->getElement('item_quantity_input', ['%name%' => $productName])->setValue($quantity);
        $this->getElement('update_cart_button')->click();
    }

    public function removeProduct(string $productName): void
    {
        $this->getElement('delete_button', ['%name%' => $productName])->press();
    }

    public function getTotal(): string
    {
        $cartTotalText = $this->getElement('cart_total')->getText();

        if (strpos($cartTotalText, ',') !== false) {
            return strstr($cartTotalText, ',', true);
        }

        return trim($cartTotalText);
    }

    /**
     * @param string|array $selector
     *
     * @throws ElementNotFoundException
     */
    private function hasItemWith(string $attributeName, $selector): bool
    {
        $itemsAttributes = $this->getElement('cart_items')->findAll('css', $selector);

        foreach ($itemsAttributes as $itemAttribute) {
            if ($attributeName === $itemAttribute->getText()) {
                return true;
            }
        }

        return false;
    }

    private function getPriceFromString(string $price): int
    {
        return (int)round((float)str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'cart_empty' => '[data-test-cart-empty]',
            'cart_items' => '[data-test-cart-items]',
            'item_unit_price' => '[data-test-cart-item-row="%name%"] [data-test-cart-item-unit-price]',
            'item_unit_price_unit' => '[data-test-cart-item-row-unit-%unitId%="%name%"] [data-test-cart-item-unit-price]',
            'item_total_price' => '[data-test-cart-item-row="%name%"] [data-test-cart-item-total-price]',
            'item_total_price_unit' => '[data-test-cart-item-row-unit-%unitId%="%name%"] [data-test-cart-item-total-price]',
            'item_quantity_input' => '[data-test-cart-item-quantity-input="%name%"]',
            'update_cart_button' => '[data-test-update-cart-button]',
            'delete_button' => '[data-test-cart-remove-button="%name%"]',
            'cart_total' => '[data-test-cart-total]',
            'cart_item_unit' => '[data-test-cart-item-unit-%unitId%="%name%"]',
        ]);
    }
}

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

namespace CoreShop\Behat\Page\Frontend;

use Behat\Mink\Element\NodeElement;

class CategoryPage extends AbstractFrontendPage implements CategoryPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_index';
    }

    public function getContent(): string
    {
        return $this->getDocument()->getContent();
    }

    public function getProductsInCategory(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('category_products')->findAll('css', '[data-test-cat_product-name]'),
        );
    }

    public function switchView(string $name): void
    {
        $this->getElement('view-' . $name)->click();
    }

    public function getProductsInCategoryGrid(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('category-products-grid')->findAll('css', '[data-test-cat_product-name-grid]'),
        );
    }

    public function changeOrder(string $order): void
    {
        $this->getElement('order-selection')->selectOption($order);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'category_products' => '[data-test-category-products]',
            'category-products-grid' => '[data-test-category-products-grid]',
            'order-selection' => '[data-test-order-selection]',
            'view-list' => '[data-test-view-list]',
            'view-grid' => '[data-test-view-grid]',
        ]);
    }
}

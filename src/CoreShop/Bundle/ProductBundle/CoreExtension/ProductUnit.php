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

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Product\Model\ProductUnitInterface;

class ProductUnit extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductUnit';

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_unit');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.product_unit.class');
    }

    protected function getInterface(): string
    {
        return '\\' . ProductUnitInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}

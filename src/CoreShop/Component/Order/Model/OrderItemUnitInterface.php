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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface OrderItemUnitInterface extends
    PimcoreModelInterface,
    AdjustableInterface,
    ConvertedAdjustableInterface
{
    public function getOrderItem(): OrderItemInterface;

    public function getTotal(bool $withTax = true): int;

    public function setTotal(int $total, bool $withTax = true);

    public function getSubtotal(bool $withTax = true): int;

    public function setSubtotal(int $total, bool $withTax = true);

    public function getTotalTax(): int;

    /**
     * @return Fieldcollection
     */
    public function getTaxes();

    /**
     * @param ?Fieldcollection $taxes
     */
    public function setTaxes(?Fieldcollection $taxes);

    public function getConvertedTotal(bool $withTax = true): int;

    public function setConvertedTotal(int $total, bool $withTax = true);

    public function getConvertedSubtotal(bool $withTax = true): int;

    public function setConvertedSubtotal(int $total, bool $withTax = true);

    public function getConvertedTotalTax(): int;

    /**
     * @return ?Fieldcollection
     */
    public function getConvertedTaxes();

    /**
     * @param ?Fieldcollection $taxes
     */
    public function setConvertedTaxes(?Fieldcollection $taxes);
}

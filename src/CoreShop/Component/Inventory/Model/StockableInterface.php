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

namespace CoreShop\Component\Inventory\Model;

interface StockableInterface
{
    /**
     * @return string
     */
    public function getInventoryName();

    /**
     * @return bool
     */
    public function isInStock();

    /**
     * @return int
     */
    public function getOnHold();

    /**
     * @param int $onHold
     */
    public function setOnHold($onHold);

    /**
     * @return int
     */
    public function getOnHand();

    /**
     * @param int $onHand
     */
    public function setOnHand($onHand);

    /**
     * @return bool
     */
    public function getIsTracked();

    /**
     * @param bool $tracked
     */
    public function setIsTracked($tracked);
}

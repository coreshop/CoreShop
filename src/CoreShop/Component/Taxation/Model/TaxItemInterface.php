<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface TaxItemInterface extends ResourceInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $name
     *
     * @return mixed
     */
    public function setName($name);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @param float $rate
     *
     * @return static
     */
    public function setRate($rate);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return static
     */
    public function setAmount($amount);
}

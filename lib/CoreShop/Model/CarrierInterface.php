<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

interface CarrierInterface
{
    /**
     * Get Shipping Costs
     *
     * @deprecated Not used anymore
     * @param Cart $cart
     * @param Zone $zone
     * @return mixed
     */
    public function getShipping(Cart $cart, Zone $zone);

    /**
     * Get Carrier Name
     *
     * @return mixed
     */
    public function getName();

    /**
     * Get Carrier Image
     *
     * @return mixed
     */
    public function getImage();

    /**
     * Get Carrier Description
     *
     * @return mixed
     */
    public function getDescription();

    /**
     * Get Carrier identifier
     *
     * @return mixed
     */
    public function getIdentifier();
}
<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin;

/**
 * Interface AbstractPlugin
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin
 */
interface AbstractPlugin
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Unique Identifier
     *
     * @return string
     */
    public function getIdentifier();
}

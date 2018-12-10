<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

interface CheckoutIdentifierHelperInterface extends HelperInterface
{
    /**
     * Get all Steps of Checkout (cart is always first step here).
     *
     * @return array
     */
    public function getSteps();

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getStep($type = '');
}

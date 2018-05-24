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

namespace CoreShop\Component\Resource\Pimcore;

if (interface_exists(\CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface::class)) {
    @trigger_error('Interface CoreShop\Component\Resource\Pimcore\ObjectServiceInterface is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Interface CoreShop\Component\Resource\Pimcore\ObjectServiceInterface is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface instead.
     */
    interface ObjectServiceInterface
    {

    }
}
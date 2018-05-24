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

namespace CoreShop\Component\Pimcore;

if (class_exists(\CoreShop\Component\Pimcore\DataObject\ClassUpdate::class)) {
    @trigger_error('Class CoreShop\Component\Pimcore\ClassUpdate is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use CoreShop\Component\Pimcore\DataObject\ClassUpdate class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Component\Pimcore\ClassUpdate is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use CoreShop\Component\Pimcore\DataObject\ClassUpdate class instead.
     */
    class ClassUpdate
    {

    }
}
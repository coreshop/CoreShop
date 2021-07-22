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

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ThemeHelper as NewThemeHelper;

if (class_exists(NewThemeHelper::class)) {
    @trigger_error('Class CoreShop\Bundle\StoreBundle\Theme\ThemeHelper is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Bundle\ThemeBundle\Service\ThemeHelper class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Bundle\StoreBundle\Theme\ThemeHelper is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Bundle\ThemeBundle\Service\ThemeHelper class instead.
     */
    class ThemeHelper
    {
    }
}

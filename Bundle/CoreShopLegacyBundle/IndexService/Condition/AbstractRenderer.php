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

namespace CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition;

/**
 * Class AbstractRenderer
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition
 */
abstract class AbstractRenderer
{
    /**
     * Renders the condition
     *
     * @param Condition $condition
     * @return mixed
     *
     * @throws Exception
     */
    public function render(Condition $condition)
    {
        $type = ucfirst($condition->getType());

        $functionName = "render" . $type;

        if (method_exists($this, $functionName)) {
            return $this->$functionName($condition);
        }

        throw new Exception(sprintf("No render function for type %s found", $condition->getType()));
    }
}

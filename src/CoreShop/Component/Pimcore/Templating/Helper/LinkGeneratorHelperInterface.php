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

namespace CoreShop\Component\Pimcore\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

interface LinkGeneratorHelperInterface extends HelperInterface
{
    /**
     * @param $nameOrObject
     * @param $params
     * @param bool $relative
     *
     * @return string
     */
    public function getPath($nameOrObject, $params, $relative = false);

    /**
     * @param $nameOrObject
     * @param array $parameters
     * @param bool  $schemeRelative
     *
     * @return string
     */
    public function getUrl($nameOrObject, $parameters = array(), $schemeRelative = false);
}

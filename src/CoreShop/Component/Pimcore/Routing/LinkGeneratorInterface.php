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

namespace CoreShop\Component\Pimcore\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface LinkGeneratorInterface
{
    /**
     * @param mixed  $object
     * @param string $routeName
     * @param array  $params
     * @param int    $referenceType
     *
     * @return mixed
     */
    public function generate($object, $routeName = null, $params = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH);
}

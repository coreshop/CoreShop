<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface LinkGeneratorInterface
{
    /**
     * @param             $object
     * @param string|null $routeName
     * @param array       $params
     * @param int         $referenceType
     * @return string
     */
    public function generate($object, ?string $routeName = null, array $params = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;
}

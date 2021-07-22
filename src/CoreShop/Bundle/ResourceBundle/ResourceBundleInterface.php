<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle;

interface ResourceBundleInterface
{
    public const MAPPING_XML = 'xml';
    public const MAPPING_YAML = 'yaml';
    public const MAPPING_ANNOTATION = 'annotation';

    /**
     * Returns a vector of supported drivers.
     *
     * @see CoreShopResourceBundle::DRIVER_DOCTRINE_ORM
     * @see CoreShopResourceBundle::DRIVER_PIMCORE
     *
     * @return array
     */
    public function getSupportedDrivers(): array;
}

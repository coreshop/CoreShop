<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
     */
    public function getSupportedDrivers(): array;
}

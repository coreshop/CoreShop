<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle;

interface ResourceBundleInterface
{
    public const string MAPPING_XML = 'xml';

    public const string MAPPING_YAML = 'yaml';

    public const string MAPPING_ANNOTATION = 'annotation';

    /**
     * Returns a vector of supported drivers.
     *
     * @see CoreShopResourceBundle::DRIVER_DOCTRINE_ORM
     * @see CoreShopResourceBundle::DRIVER_PIMCORE
     */
    public function getSupportedDrivers(): array;
}

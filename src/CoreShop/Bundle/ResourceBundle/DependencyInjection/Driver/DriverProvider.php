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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Pimcore\PimcoreDriver;
use CoreShop\Component\Resource\Metadata\MetadataInterface;

final class DriverProvider
{
    /**
     * @var DriverInterface[]
     */
    private static array $drivers = [];

    public static function get(MetadataInterface $metadata): DriverInterface
    {
        $type = $metadata->getDriver();

        if (isset(self::$drivers[$type])) {
            return self::$drivers[$type];
        }

        return match ($type) {
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM => new DoctrineORMDriver(),
            CoreShopResourceBundle::DRIVER_PIMCORE => new PimcoreDriver(),
            default => throw new UnknownDriverException($type),
        };
    }
}

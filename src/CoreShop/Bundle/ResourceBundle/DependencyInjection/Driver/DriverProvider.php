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

declare(strict_types=1);

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

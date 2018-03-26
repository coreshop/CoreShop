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

namespace CoreShop\Bundle\ResourceBundle\Serialization\Driver;

use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Pimcore\Model\Asset;

final class PimcoreClassDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private $driverChain;

    /**
     * @param DriverInterface $driverChain
     */
    public function __construct(DriverInterface $driverChain)
    {
        $this->driverChain = $driverChain;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (strpos($class->getFileName(), PIMCORE_CLASS_DIRECTORY) === 0) {
            return null;
        }

        if ($class->isSubclassOf(Asset::class)) {
            return null;
        }

        return $this->driverChain->loadMetadataForClass($class);
    }
}

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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Serialization\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;

class PimcoreDataObjectDriver implements DriverInterface
{
    protected DriverInterface $decorated;

    public function __construct(DriverInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param \ReflectionClass $class
     * @return \Metadata\ClassMetadata|null
     */
    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
//        //We don't want Pimcore entities to be serialized directly
        if ($class->getNamespaceName() === 'Pimcore\\Model\\DataObject') {
            return $classMetadata = new \JMS\Serializer\Metadata\ClassMetadata($name = $class->name);
        }

        if ($class->getName() === 'Pimcore\\Model\\DataObject\\Fieldcollection') {
            return $classMetadata = new \JMS\Serializer\Metadata\ClassMetadata($name = $class->name);
        }

        return $this->decorated->loadMetadataForClass($class);
    }
}

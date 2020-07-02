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

use Metadata\Driver\DriverInterface;
use Metadata\NullMetadata;

class PimcoreDataObjectDriver implements DriverInterface
{
    protected $decorated;

    public function __construct(DriverInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        //We don't want Pimcore entities to be serialized directly
        if ($class->getNamespaceName() === 'Pimcore\\Model\\DataObject') {
            return null;
        }

        return $this->decorated->loadMetadataForClass($class);
    }
}

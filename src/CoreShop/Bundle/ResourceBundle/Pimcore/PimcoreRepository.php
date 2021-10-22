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

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

class PimcoreRepository extends PimcoreDaoRepository implements PimcoreRepositoryInterface
{
    public function getClassId(): string
    {
        $class = $this->metadata->getClass('model');

        if (!method_exists($class, 'classId')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class %s has no classId function and is therefore not considered as a valid Pimcore DataObject',
                    $class
                )
            );
        }

        return $class::classId();
    }

    /**
     * @return Listing
     */
    public function getList()
    {
        $className = $this->metadata->getClass('model');

        if (method_exists($className, 'getList')) {
            return $className::getList();
        }

        /** @psalm-var class-string $listClass */
        $listClass = $className . '\\Listing';

        if (class_exists($className)) {
            return new $listClass();
        }

        throw new \InvalidArgumentException(sprintf(
            'Class %s has no getList or a Listing Class function and thus is not supported here',
            $className
        ));
    }

    public function forceFind($id, bool $force = true)
    {
        $concrete = parent::forceFind($id, $force);

        if ($concrete instanceof Concrete) {
            return $concrete;
        }

        return null;
    }
}

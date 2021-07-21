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

namespace CoreShop\Bundle\OptimisticEntityLockBundle\Exception;

use Pimcore\Model\DataObject\Concrete;

class OptimisticLockException extends \Exception
{
    private ?Concrete $entity;

    public function __construct(string $msg, Concrete $entity = null)
    {
        parent::__construct($msg);
        $this->entity = $entity;
    }
    public function getEntity(): ?Concrete
    {
        return $this->entity;
    }

    public static function lockFailed(Concrete $entity): OptimisticLockException
    {
        return new self("The optimistic lock on an entity failed.", $entity);
    }

    public static function lockFailedVersionMismatch(Concrete $entity, int $expectedLockVersion, int $actualLockVersion): OptimisticLockException
    {
        return new self("The optimistic lock failed, version ".$expectedLockVersion." was expected, but is actually ".$actualLockVersion,
            $entity);
    }

    public static function notVersioned(string $entityName): OptimisticLockException
    {
        return new self("Cannot obtain optimistic lock on unversioned entity ".$entityName, null);
    }
}

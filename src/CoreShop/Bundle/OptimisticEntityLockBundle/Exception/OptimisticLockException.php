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

namespace CoreShop\Bundle\OptimisticEntityLockBundle\Exception;

use Pimcore\Model\DataObject\Concrete;

class OptimisticLockException extends \Exception
{
    public function __construct(string $msg, private ?\Pimcore\Model\DataObject\Concrete $entity = null)
    {
        parent::__construct($msg);
    }

    public function getEntity(): ?Concrete
    {
        return $this->entity;
    }

    public static function lockFailed(Concrete $entity): self
    {
        return new self('The optimistic lock on an entity failed.', $entity);
    }

    public static function lockFailedVersionMismatch(Concrete $entity, int $expectedLockVersion, int $actualLockVersion): self
    {
        return new self(
            'The optimistic lock failed, version ' . $expectedLockVersion . ' was expected, but is actually ' . $actualLockVersion,
            $entity
        );
    }

    public static function notVersioned(string $entityName): self
    {
        return new self('Cannot obtain optimistic lock on unversioned entity ' . $entityName, null);
    }
}

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

namespace CoreShop\Bundle\OptimisticEntityLockBundle\Exception;

use Pimcore\Model\DataObject\Concrete;

class OptimisticLockException extends \Exception
{
    /**
     * @var Concrete|null
     */
    private $entity;

    /**
     * @param string   $msg
     * @param Concrete $entity
     */
    public function __construct(string $msg, Concrete $entity = null)
    {
        parent::__construct($msg);
        $this->entity = $entity;
    }

    /**
     * Gets the entity that caused the exception.
     *
     * @return Concrete|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Concrete $entity
     *
     * @return OptimisticLockException
     */
    public static function lockFailed(Concrete $entity)
    {
        return new self("The optimistic lock on an entity failed.", $entity);
    }

    /**
     * @param Concrete $entity
     * @param int      $expectedLockVersion
     * @param int      $actualLockVersion
     *
     * @return OptimisticLockException
     */
    public static function lockFailedVersionMismatch(Concrete $entity, int $expectedLockVersion, int $actualLockVersion)
    {
        return new self("The optimistic lock failed, version ".$expectedLockVersion." was expected, but is actually ".$actualLockVersion,
            $entity);
    }

    /**
     * @param string $entityName
     *
     * @return OptimisticLockException
     */
    public static function notVersioned(string $entityName)
    {
        return new self("Cannot obtain optimistic lock on unversioned entity ".$entityName, null);
    }
}

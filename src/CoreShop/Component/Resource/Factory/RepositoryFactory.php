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

namespace CoreShop\Component\Resource\Factory;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectManager;

class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @psalm-var class-string
     */
    private string $className;

    /**
     * @psalm-var class-string
     */
    private string $repositoryClassName;

    /**
     * @psalm-param class-string $className
     * @psalm-param class-string $repositoryClassName
     */
    public function __construct(string $className, string $repositoryClassName)
    {
        $this->className = $className;
        $this->repositoryClassName = $repositoryClassName;
    }

    public function createNewRepository(ObjectManager $objectManager): RepositoryInterface
    {
        return new $this->repositoryClassName(
            $objectManager,
            $objectManager->getMetadataFactory()->getMetadataFor($this->className)
        );
    }
}

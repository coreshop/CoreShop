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

namespace CoreShop\Component\Resource\Factory;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectManager;

class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @psalm-param class-string $className
     * @psalm-param class-string $repositoryClassName
     */
    public function __construct(
        private string $className,
        private string $repositoryClassName,
    ) {
    }

    public function createNewRepository(ObjectManager $objectManager): RepositoryInterface
    {
        return new $this->repositoryClassName(
            $objectManager,
            $objectManager->getMetadataFactory()->getMetadataFor($this->className),
        );
    }
}

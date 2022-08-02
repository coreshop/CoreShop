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
     * @psalm-param class-string $className
     * @psalm-param class-string $repositoryClassName
     */
    public function __construct(
        private string $className,
        private string $repositoryClassName
    ) {
    }

    public function createNewRepository(ObjectManager $objectManager): RepositoryInterface
    {
        return new $this->repositoryClassName(
            $objectManager,
            $objectManager->getMetadataFactory()->getMetadataFor($this->className)
        );
    }
}

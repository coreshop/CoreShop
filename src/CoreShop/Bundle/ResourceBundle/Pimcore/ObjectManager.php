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
use Pimcore\Model\AbstractModel;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class ObjectManager implements \Doctrine\Persistence\ObjectManager
{
    private array $repositories = [];
    private array $modelsToUpdate = [];
    private array $modelsToInsert = [];
    private array $modelsToRemove = [];

    public function find($className, $id): ?Concrete
    {
        /**
         * @var Concrete $className
         */
        return $className::getById($id);
    }

    public function persist($resource): void
    {
        /**
         * @var $resource AbstractModel
         */
        Assert::isInstanceOf($resource, AbstractModel::class);

        $id = $this->getResourceId($resource);
        $className = $this->getResourceClassName($resource);

        if ($id) {
            $this->modelsToUpdate[$className][$id] = $resource;
        } else {
            $this->modelsToInsert[$className][] = $resource;
        }
    }

    public function remove($resource): void
    {
        $id = $this->getResourceId($resource);
        $className = $this->getResourceClassName($resource);

        if ($resource instanceof Concrete) {
            $className = $resource->getClassName();
        }

        if ($id) {
            $this->modelsToRemove[$className][$id] = $resource;
        }
    }

    public function merge($object): object
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function clear($objectName = null): void
    {
        if (null === $objectName) {
            $this->modelsToRemove = [];
            $this->modelsToUpdate = [];
            $this->modelsToInsert = [];
        } else {
            if (isset($this->modelsToRemove[$objectName])) {
                $this->modelsToRemove[$objectName] = [];
            }

            if (isset($this->modelsToUpdate[$objectName])) {
                $this->modelsToUpdate[$objectName] = [];
            }

            if (isset($this->modelsToInsert[$objectName])) {
                $this->modelsToInsert[$objectName] = [];
            }
        }
    }

    public function detach($object): void
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function refresh($object): void
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function flush(): void
    {
        foreach ($this->modelsToRemove as $className => $classTypeModels) {
            foreach ($classTypeModels as $model) {
                $model->delete();
            }
        }

        foreach ([$this->modelsToInsert, $this->modelsToUpdate] as $modelsToSave) {
            foreach ($modelsToSave as $className => $classTypeModels) {
                foreach ($classTypeModels as $model) {
                    if ($model instanceof Concrete) {
                        if (!$model->getPublished()) {
                            $model->setOmitMandatoryCheck(true);
                        }
                    }

                    $model->save();
                }
            }
        }

        $this->modelsToUpdate =
        $this->modelsToInsert =
        $this->modelsToRemove = [];
    }

    public function getRepository($className): PimcoreRepositoryInterface
    {
        if (!array_key_exists($className, $this->repositories)) {
            throw new \InvalidArgumentException(sprintf('Repository for class %s not found', $className));
        }

        return $this->repositories[$className];
    }

    public function getClassMetadata($className)
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function getMetadataFactory()
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function initializeObject($obj): void
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function contains($object)
    {
        throw new \InvalidArgumentException('Not implemented');
    }

    public function registerRepository(string $className, string $repository): void
    {
        $this->repositories[$className] = $repository;
    }

    private function getResourceId(object $resource): int|string
    {
        $id = spl_object_hash($resource);

        if (method_exists($resource, 'getId') && $resource->getId()) {
            $id = $resource->getId();
        }

        return $id;
    }

    private function getResourceClassName(object $resource): string
    {
        $className = get_class($resource);

        if ($resource instanceof Concrete) {
            $className = $resource->getClassName();
        }

        return $className;
    }
}

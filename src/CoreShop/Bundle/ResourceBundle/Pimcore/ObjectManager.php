<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;
use Webmozart\Assert\Assert;

final class ObjectManager implements \Doctrine\Common\Persistence\ObjectManager
{
    /**
     * @var array
     */
    private $repositories = [];

    /**
     * @var array
     */
    private $modelsToUpdate = [];

    /**
     * @var array
     */
    private $modelsToInsert = [];

    /**
     * @var array
     */
    private $modelsToRemove = [];

    /**
     * {@inheritdoc}
     */
    public function find($className, $id)
    {
        return $className::getById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($resource)
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

    /**
     * {@inheritdoc}
     */
    public function remove($resource)
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

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        //TODO:
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
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

    /**
     * {@inheritdoc}
     */
    public function detach($object)
    {
        //TODO:
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        //TODO:
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
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

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        if (!array_key_exists($className, $this->repositories)) {
            throw new \InvalidArgumentException(sprintf('Repository for class %s not found', $className));
        }

        return $this->repositories[$className];
    }

    public function getClassMetadata($className)
    {
        // TODO
    }

    public function getMetadataFactory()
    {
        // TODO
    }

    public function initializeObject($obj)
    {
        // TODO
    }

    public function contains($object)
    {
        // TODO
    }

    /**
     * @param string $className
     * @param string $repository
     */
    public function registerRepository($className, $repository)
    {
        $this->repositories[$className] = $repository;
    }

    /**
     * @param string $resource
     *
     * @return int
     */
    private function getResourceId($resource)
    {
        $id = spl_object_hash($resource);

        if (method_exists($resource, 'getId')) {
            $id = $resource->getId();
        }

        return $id;
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    private function getResourceClassName($resource)
    {
        $className = get_class($resource);

        if ($resource instanceof Concrete) {
            $className = $resource->getClassName();
        }

        return $className;
    }

    /**
     * @param string $resource
     *
     * @return bool
     */
    private function isResourceNew($resource)
    {
        if ($resource instanceof ElementInterface) {
            return is_null($resource->getId()) || $resource->getId() === 0;
        }

        if (method_exists($resource, 'getId')) {
            return is_null($resource->getId()) || $resource->getId() === 0;
        }

        return true;
    }
}

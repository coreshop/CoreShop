<?php
declare(strict_types=1);

namespace CoreShop\Component\Resource\Pimcore\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Element\ElementInterface;

interface PimcoreModelInterface extends ResourceInterface, ElementInterface
{
    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param bool $published
     */
    public function setPublished($published);

    /**
     * @return bool
     */
    public function getPublished();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @param ElementInterface $parent
     */
    public function setParent($parent);

    /**
     * @return ElementInterface|null
     */
    public function getParent();

    /**
     * @return mixed
     */
    public function getObjectVar($field);

    /**
     * @return mixed
     */
    public function save();

    /**
     * @return mixed
     */
    public function delete();

    /**
     * @return array
     */
    public function getChildren(array $type = [], $includingUnpublished = false);

    /**
     * @return ClassDefinition
     */
    public function getClass();
}

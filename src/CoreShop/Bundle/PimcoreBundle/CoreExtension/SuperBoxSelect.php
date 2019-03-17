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

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation;
use Pimcore\Model\DataObject\Service;

class SuperBoxSelect extends ManyToManyRelation
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopSuperBoxSelect';

    /**
     * @var string
     */
    public $folderName;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var string
     */
    public $recursive;

    /**
     * @var string
     */
    public $sortBy;

    /**
     * @return mixed
     */
    public function getFolderName()
    {
        return $this->folderName;
    }

    /**
     * @param mixed $folderName
     */
    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param mixed $methodName
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @return mixed
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param mixed $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return bool
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        //if not set, return null
        if ($data === null or $data === false) {
            return null;
        }

        $elements = array();
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $id) {
                $elements[] = Service::getElementById('object', $id);
            }
        }

        //must return array if data shall be set
        return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $return = array();

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $element) {
                /** @var AbstractObject $element */
                $return[] = $element->getId();
            }

            return implode(',', $return);
        }

        return false;
    }
}

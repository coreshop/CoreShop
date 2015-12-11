<?php
/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @category   Pimcore
 * @package    Object|Class
 * @copyright  Copyright (c) 2009-2014 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     New BSD License
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Element;

class ObjectSelect extends Model\Object\ClassDefinition\Data\Select {

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "objectSelect";

    /**
     * @var integer
     */
    public $width;

    /**
     * @var relation objects
     */
    public $options;

    /**
     * Type for the column to query
     *
     * @var array
     */
    public $queryColumnType = "int(11)";

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = "int(11)";

    /**
     * Type for the generated phpdoc
     *
     * @var string
     */
    public $phpdocType = "\\Pimcore\\Model\\Object\\AbstractObject";

    /**
     * Set of allowed classes
     *
     * @var array
     */
    public $classes;

    /**
     * @return array
     */
    public function getClasses() {
        $this->classes = $this->correctClasses($this->classes);
        return $this->classes;
    }

    /**
     * @param array
     * @return void $classes
     */
    public function setClasses($classes) {
        $this->classes = $this->correctClasses($classes);
        return $this;
    }
    /**
     * @see Object\ClassDefinition\Data::getDataForResource
     * @param Asset $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForResource($data, $object = null) {
        if ($data instanceof Object\CoreShopOrderState) {
            return $data->getId();
        }
        return null;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataFromResource
     * @param integer $data
     * @return Country
     */
    public function getDataFromResource($data) {
        if (intval($data) > 0) {
            return Object\CoreShopOrderState::getById($data);
        }
        return null;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     * @param Country $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForQueryResource($data, $object = null) {

        if ($data instanceof Object\CoreShopOrderState) {
            return $data->getId();
        }
        return null;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForEditmode
     * @param Country $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer
     */
    public function getDataForEditmode($data, $object = null, $objectFromVersion = NULL) {
        return $this->getDataForResource($data, $object);
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     * @param integer $data
     * @param null|Model\Object\AbstractObject $object
     * @return Country
     */
    public function getDataFromEditmode($data, $object = null) {
        return $this->getDataFromResource($data);
    }

    /**
     * this is a hack for import see: http://www.pimcore.org/issues/browse/PIMCORE-790
     * @param array
     * @return array
     */
    protected function correctClasses ($classes) {

        // this is the new method with Ext.form.MultiSelect
        if(is_string($classes) && !empty($classes)) {
            $classParts = explode(",", $classes);
            $classes = array();
            foreach ($classParts as $class) {
                $classes[] = array("classes" => $class);
            }
        }

        // this was the legacy method with Ext.SuperField
        if(is_array($classes) && array_key_exists("classes",$classes)) {
            $classes = array($classes);
        }

        if(!is_array($classes)) {
            $classes = array();
        }

        return $classes;
    }

    public function __construct() {
        $this->buildOptions();
    }

    public function __wakeup() {
        $this->buildOptions();
    }

    public function buildOptions(){
        $classes = $this->classes;
        $options = array();

        if(is_array($classes)) {
            foreach ($classes as $class) {
                $class = ClassDefinition::getByName($class);

                if ($class instanceof ClassDefinition) {
                    $listClassName = "Pimcore\\Model\\Object\\" . $class->getName() . "\\Listing";
                    $listObject = new $listClassName();
                    $listObject->getObjects();

                    foreach ($listObject as $listItem) {
                        $options[] = array(
                            "key" => $listItem->getKey(),
                            "value" => $listItem->getId(),
                            "path" => $listItem->getFullPath()
                        );
                    }
                }
            }
        }

        $this->setOptions($options);
    }
}

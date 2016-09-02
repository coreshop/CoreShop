<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use CoreShop\Model\Product;
use CoreShop\Model\Product\SpecificPrice;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\ClassDefinition\Data;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Webservice\Data\Object\Element;
use Pimcore\Tool;

/**
 * Class CoreShopSpecificPrices
 * @package Pimcore\Model\Object\ClassDefinition\Data
 */
class CoreShopSpecificPrices extends Data
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopSpecificPrices';

    /**
     * @var integer
     */
    public $height;

    /**
     * @param mixed $data
     * @param null $object
     * @param array $params
     * @return SpecificPrice[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if($object instanceof Product) {
            $prices = SpecificPrice::getSpecificPrices($object);

            return $prices;
        }

        return [];
    }

    /**
     * @param mixed $data
     * @param null $object
     * @param array $params
     * @return SpecificPrice[]
     * @throws \CoreShop\Exception
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];

        if($data) {
            foreach($data as $dataRow) {
                $conditions = $dataRow['conditions'];
                $actions = $dataRow['actions'];
                $actionInstances = array();
                $conditionInstances = array();

                $actionNamespace = 'CoreShop\\Model\\PriceRule\\Action\\';
                $conditionNamespace = 'CoreShop\\Model\\PriceRule\\Condition\\';

                foreach ($conditions as $condition) {
                    $class = $conditionNamespace.ucfirst($condition['type']);

                    if (Tool::classExists($class)) {
                        $instance = new $class();
                        $instance->setValues($condition);

                        $conditionInstances[] = $instance;
                    } else {
                        throw new \CoreShop\Exception(sprintf('Condition with type %s not found'), $condition['type']);
                    }
                }

                foreach ($actions as $action) {
                    $class = $actionNamespace.ucfirst($action['type']);

                    if (Tool::classExists($class)) {
                        $instance = new $class();
                        $instance->setValues($action);

                        $actionInstances[] = $instance;
                    } else {
                        throw new \CoreShop\Exception(sprintf('Action with type %s not found'), $action['type']);
                    }
                }

                $specificPrice = null;

                if($dataRow['id']) {
                    $specificPrice = SpecificPrice::getById($dataRow['id']);
                }

                if(!$specificPrice instanceof SpecificPrice) {
                    $specificPrice = new SpecificPrice();
                }

                $specificPrice->setValues($dataRow['settings']);
                $specificPrice->setActions($actionInstances);
                $specificPrice->setConditions($conditionInstances);

                $prices[] = $specificPrice;
            }
        }

        return $prices;
    }

    /**
     * @param Concrete $object
     * @param array $params
     */
    public function save($object, $params = []) {
        if($object) {
            $getter = "get" . ucfirst($this->getName());

            $all = $this->load($object, $params);

            $founds = [];
            $prices = $object->$getter();

            if(is_array($prices)) {
                foreach ($prices as $price) {
                    if ($price instanceof SpecificPrice) {
                        $price->setO_Id($object->getId());
                        $price->save();

                        $founds[] = $price->getId();
                    }
                }
            }

            foreach($all as $price) {
                if(!in_array($price->getId(), $founds)) {
                    $price->delete();
                }
            }
        }
    }

    /**
     * @param $object
     * @param array $params
     * @return SpecificPrice[]
     */
    public function load($object, $params = [])
    {
        return $this->getDataForEditmode(null, $object, $params);
    }
    /**
     * Returns the data which should be stored in the query columns
     *
     * @param mixed $data
     * @return mixed
    */
    public function getDataForQueryResource($data) {
        return "not_supported";
    }

    /**
     * @param mixed $data
     * @param null $relatedObject
     * @param mixed $params
     * @param null $idMapper
     * @return mixed
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[]
     * @return []
     */
    protected function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->arrayCastRecursive($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = $this->arrayCastRecursive((array)$value);
                }
            }
        }
        if ($array instanceof \stdClass) {
            return $this->arrayCastRecursive((array)$array);
        }
        return $array;
    }
}

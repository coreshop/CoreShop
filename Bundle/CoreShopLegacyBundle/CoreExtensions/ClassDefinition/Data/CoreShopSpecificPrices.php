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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\CoreExtensions\ClassDefinition\Data;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\SpecificPrice;
use Pimcore\Cache;
use CoreShop\Bundle\CoreShopLegacyBundle\CoreExtensions\ClassDefinition\Data;
use Pimcore\Model\Object\Concrete;

/**
 * Class CoreShopSpecificPrices
 * @package CoreShop\Bundle\CoreShopLegacyBundle\CoreExtensions\ClassDefinition\Data
 */
class CoreShopSpecificPrices extends \Pimcore\Model\Object\ClassDefinition\Data
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
        if ($object instanceof Product) {
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
     * @throws \CoreShop\Bundle\CoreShopLegacyBundle\Exception
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];

        if ($data) {
            foreach ($data as $dataRow) {
                $conditions = $dataRow['conditions'];
                $actions = $dataRow['actions'];

                $specificPrice = null;

                if ($dataRow['id']) {
                    $specificPrice = SpecificPrice::getById($dataRow['id']);
                }

                if (!$specificPrice instanceof SpecificPrice) {
                    $specificPrice = new SpecificPrice();
                    $specificPrice->setInherit(false);
                    $specificPrice->setPriority(0);
                }

                $actionInstances = $specificPrice->prepareActions($actions);
                $conditionInstances = $specificPrice->prepareConditions($conditions);

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
    public function save($object, $params = [])
    {
        if ($object) {
            $getter = "get" . ucfirst($this->getName());

            $all = $this->load($object, $params);

            $founds = [];
            $prices = $object->$getter();

            if (is_array($prices)) {
                foreach ($prices as $price) {
                    if ($price instanceof SpecificPrice) {
                        $price->setO_Id($object->getId());
                        $price->save();

                        $founds[] = $price->getId();
                    }
                }
            }

            foreach ($all as $price) {
                if (!in_array($price->getId(), $founds)) {
                    $price->delete();
                }
            }

            if ($object instanceof Product) {
                Cache::clearTag($object->getCacheKey());
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
     * @return string
    */
    public function getDataForQueryResource($data)
    {
        return "not_supported";
    }

    /**
     * @param mixed $data
     * @param null $relatedObject
     * @param mixed $params
     * @param null $idMapper
     * @return SpecificPrice[]
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[]
     * @return array
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

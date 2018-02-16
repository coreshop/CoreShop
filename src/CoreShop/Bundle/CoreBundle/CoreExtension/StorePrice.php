<?php

/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\CoreExtension;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Model;

class StorePrice extends Model\DataObject\ClassDefinition\Data
{
    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = 'int';

    /**
     * @var float
     */
    public $width;

    /**
     * @var int
     */
    public $defaultValue;

    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = 'text';

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = 'text';

    /**
     * Type for the generated phpdoc
     *
     * @var string
     */
    public $phpdocType = 'array';

    /**
     * @var float
     */
    public $minValue;

    /**
     * @var float
     */
    public $maxValue;

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $this->getAsIntegerCast($width);

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultValue()
    {
        if ($this->defaultValue !== null) {
            return $this->toNumeric($this->defaultValue);
        }

        return 0;
    }

    /**
     * @param int $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        if (strlen(strval($defaultValue)) > 0) {
            $this->defaultValue = $defaultValue;
        }

        return $this;
    }

    /**
     * @param float $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param float $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return float
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Get ' . str_replace(['/**', '*/', '//'], '', $this->getName()) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '* @return ' . $this->getPhpdocType() . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function get' . ucfirst($key) . ' (\CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . 'if (is_null($store)) {' . "\n";
        $code .= "\t\t" . 'return $this->' . $key . ";\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . '$data = $this->' . $key . ";\n";
        $code .= "\t" . 'if (array_key_exists($store->getId(), $data) && is_numeric($data[$store->getId()])) {' . "\n";
        $code .= "\t\t" . 'return intval($data[$store->getId()]);' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t return null;" . "\n";
        $code .= "}\n\n";

        return $code;
    }

    public function getSetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Get ' . str_replace(['/**', '*/', '//'], '', $this->getName()) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '* @return static' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function set' . ucfirst($key) . ' ($' . $key . ', \CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . 'if (is_null($' . $key . ')) {' . "\n";
        $code .= "\t\t" . '$' . $key . ' = [];' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . "\n";
        $code .= "\t" . 'if (is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . ' = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'else if (!is_null($store)) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . '[$store->getId()] = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'return $this;' . "\n";
        $code .= "}\n\n";

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if (is_null($data)) {
            $data = [];
        } else {
            $data = unserialize($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        $queryResource = [];

        if (is_array($data)) {
            foreach ($data as $storePrice) {
                $queryResource[] = $storePrice['id'] . '-' . $storePrice['price'];
            }
        }

        return ',' . implode(',', $queryResource) . ',';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $stores = $this->getStoreRepository()->findAll();
        $storeData = [];

        /**
         * @var $store StoreInterface
         */
        foreach ($stores as $store) {
            $price = (is_array($data) && array_key_exists($store->getId(), $data) ? $data[$store->getId()] : 0);
            $price = doubleval(sprintf('%0.2f', $price / 100));

            $storeData[$store->getId()] = [
                'name' => $store->getName(),
                'currencySymbol' => $store->getCurrency()->getSymbol(),
                'price' => $price
            ];
        }

        return $storeData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $validData = [];

        foreach ($data as $storeId => $price) {
            if ($storeId === 0) {
                continue;
            }
            if ($price === null) {
                continue;
            }

            $validData[$storeId] = $price * 100;
        }

        return $validData;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
        if (!$omitMandatoryCheck && $this->getMandatory() && $this->isEmpty($data)) {
            throw new Model\Element\ValidationException('Empty mandatory field [ ' . $this->getName() . ' ]');
        }

        if (!is_array($data)) {
            $data = [];
        }

        foreach ($data as $storeData) {
            $priceValue = $storeData['price'];

            if (!$this->isEmpty($priceValue) && !is_numeric($priceValue)) {
                throw new Model\Element\ValidationException('invalid numeric data [' . $priceValue . ']');
            }

            if (!$this->isEmpty($priceValue) && !$omitMandatoryCheck) {
                $priceValue = $this->toNumeric($priceValue);

                if ($priceValue >= PHP_INT_MAX) {
                    throw new Model\Element\ValidationException('Value exceeds PHP_INT_MAX please use an input data type instead of numeric!');
                }

                if (strlen($this->getMinValue()) && $this->getMinValue() > $priceValue) {
                    throw new Model\Element\ValidationException('Value in field [ ' . $this->getName() . ' ] is not at least ' . $this->getMinValue());
                }

                if (strlen($this->getMaxValue()) && $priceValue > $this->getMaxValue()) {
                    throw new Model\Element\ValidationException('Value in field [ ' . $this->getName() . ' ] is bigger than ' . $this->getMaxValue());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getForCsvExport($object, $params = [])
    {
        return 'NOT SUPPORTED';
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
        return 'NOT SUPPORTED';
    }

    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty($data)
    {
        return is_null($data);
    }

    /**
     * @param $value
     *
     * @return float|int
     */
    protected function toNumeric($value)
    {
        if (strpos((string)$value, '.') === false) {
            return (int)$value;
        }

        return (float)$value;
    }

    /**
     * @return StoreRepositoryInterface
     */
    protected function getStoreRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.store');
    }
}

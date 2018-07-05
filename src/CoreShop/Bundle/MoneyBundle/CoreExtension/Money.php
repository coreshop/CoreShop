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

namespace CoreShop\Bundle\MoneyBundle\CoreExtension;

use Pimcore\Model;

class Money extends Model\DataObject\ClassDefinition\Data
{
    /**
     * Static type of this element.
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
     * Type for the column to query.
     *
     * @var string
     */
    public $queryColumnType = 'bigint(20)';

    /**
     * Type for the column.
     *
     * @var string
     */
    public $columnType = 'bigint(20)';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = 'int';

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
        if (null !== $this->defaultValue) {
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
    public function getDataForResource($data, $object = null, $params = [])
    {
        if (is_numeric($data) && !is_int($data)) {
            $data = (int) $data;
        }

        if (is_int($data)) {
            return $data;
        }

        return null;
    }

    /**
     * @see Model\DataObject\ClassDefinition\Data::getDataFromResource
     *
     * @param float                                $data
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if (is_numeric($data)) {
            return $this->toNumeric($data);
        }

        return $data;
    }

    /**
     * @see Model\DataObject\ClassDefinition\Data::getDataForQueryResource
     *
     * @param float                                $data
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        return $this->getDataForResource($data, $object, $params);
    }

    /**
     * @see Model\DataObject\ClassDefinition\Data::getDataForEditmode
     *
     * @param float                                $data
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return doubleval(sprintf('%0.2f', $data / 100));
    }

    /**
     * @see Model\DataObject\ClassDefinition\Data::getDataFromEditmode
     *
     * @param float                                $data
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (is_numeric($data)) {
            return (int) round((round($data, 2) * 100), 0);
        }

        return $data;
    }

    /**
     * @see Model\DataObject\ClassDefinition\Data::getVersionPreview
     *
     * @param float                                $data
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * Checks if data is valid for current data field.
     *
     * @param mixed $data
     * @param bool  $omitMandatoryCheck
     *
     * @throws \Exception
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
        if (!$omitMandatoryCheck && $this->getMandatory() && $this->isEmpty($data)) {
            throw new Model\Element\ValidationException('Empty mandatory field [ '.$this->getName().' ]');
        }

        if (!$this->isEmpty($data) && !is_numeric($data)) {
            throw new Model\Element\ValidationException('invalid numeric data ['.$data.']');
        }

        if (!$this->isEmpty($data) && !$omitMandatoryCheck) {
            $data = $this->toNumeric($data);

            if ($data >= PHP_INT_MAX) {
                throw new Model\Element\ValidationException('Value exceeds PHP_INT_MAX please use an input data type instead of numeric!');
            }

            if (strlen($this->getMinValue()) && $this->getMinValue() > $data) {
                throw new Model\Element\ValidationException('Value in field [ '.$this->getName().' ] is not at least '.$this->getMinValue());
            }

            if (strlen($this->getMaxValue()) && $data > $this->getMaxValue()) {
                throw new Model\Element\ValidationException('Value in field [ '.$this->getName().' ] is bigger than '.$this->getMaxValue());
            }
        }
    }

    /**
     * converts object data to a simple string value or CSV Export.
     *
     * @abstract
     *
     * @param Model\DataObject\AbstractObject $object
     * @param array                           $params
     *
     * @return string
     */
    public function getForCsvExport($object, $params = [])
    {
        $data = $this->getDataFromObjectParam($object, $params);

        return strval($data);
    }

    /**
     * fills object field data values from CSV Import String.
     *
     * @param string                               $importValue
     * @param null|Model\DataObject\AbstractObject $object
     * @param mixed                                $params
     *
     * @return float
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
        $value = $this->toNumeric(str_replace(',', '.', $importValue));

        return $value;
    }

    /** True if change is allowed in edit mode.
     * @param string $object
     * @param mixed  $params
     *
     * @return bool
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return true;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function isEmpty($data)
    {
        return strlen($data) < 1;
    }

    /**
     * @param $value
     *
     * @return float|int
     */
    protected function toNumeric($value)
    {
        if (false === strpos((string) $value, '.')) {
            return (int) $value;
        }

        return (float) $value;
    }
}

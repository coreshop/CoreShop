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

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model;

class SerializedData extends Model\DataObject\ClassDefinition\Data
{
    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = 'SerializedData';

    /**
     * Type for the column to query
     *
     * @var array
     */
    public $queryColumnType = null;

    /**
     * Type for the column
     *
     * @var array
     */
    public $columnType = 'LONGBLOB';

    /**
     * Type for the generated phpdoc
     *
     * @var string
     */
    public $phpdocType;

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
        return unserialize($data) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromGridEditor($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * @return string
     */
    public function getQueryColumnType()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getColumnType()
    {
        return 'LONGBLOB';
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
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
     * {@inheritdoc}
     */
    public function getForWebserviceExport($object, $params = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromWebserviceImport($value, $object = null, $params = [], $idMapper = null)
    {
        // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForGrid($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getForCsvExport($object, $params = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getFilterCondition($value, $operator, $params = [])
    {
        return null;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setValues($data = [])
    {
        foreach ($data as $key => $value) {
            $method = 'set'.$key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }
}

class_alias(SerializedData::class, 'CoreShop\Bundle\ResourceBundle\CoreExtension\SerializedData');
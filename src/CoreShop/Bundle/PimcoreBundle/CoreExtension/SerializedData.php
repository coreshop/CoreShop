<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model;

class SerializedData extends Model\DataObject\ClassDefinition\Data implements Model\DataObject\ClassDefinition\Data\ResourcePersistenceAwareInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopSerializedData';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType;

    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
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
        return (is_string($data) ? unserialize($data) : $data) ?: null;
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
     * @return null
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
     * @return string
     */
    public function getForCsvExport($object, $params = [])
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
    }

    /**
     * @return string
     */
    public function getFilterCondition($value, $operator, $params = [])
    {
        return '';
    }
}

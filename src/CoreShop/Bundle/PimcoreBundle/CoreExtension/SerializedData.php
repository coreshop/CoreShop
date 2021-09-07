<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function getParameterTypeDeclaration(): ?string
    {
        return null;
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return null;
    }

    public function getPhpdocInputType(): ?string
    {
        return null;
    }

    public function getPhpdocReturnType(): ?string
    {
        return null;
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function getDataForResource($data, $object = null, $params = [])
    {
        return serialize($data);
    }

    public function getDataFromResource($data, $object = null, $params = [])
    {
        return (is_string($data) ? unserialize($data) : $data) ?: null;
    }

    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return $data;
    }

    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

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

    public function checkValidity($data, $omitMandatoryCheck = false, $params = [])
    {
        return true;
    }

    public function isEmpty($data)
    {
        return is_null($data);
    }

    public function getForWebserviceExport($object, $params = [])
    {
        return null;
    }

    public function getFromWebserviceImport($value, $object = null, $params = [], $idMapper = null)
    {
        // not implemented
    }

    public function getDataForGrid($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

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

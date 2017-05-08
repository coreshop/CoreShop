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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model;
use Pimcore\Model\Object;

abstract class Select extends Model\Object\ClassDefinition\Data\Select
{
    /**
     * Type for the column to query.
     *
     * @var string
     */
    public $queryColumnType = 'int(11)';

    /**
     * Type for the column.
     *
     * @var string
     */
    public $columnType = 'int(11)';

    /**
     * @var bool
     */
    public $allowEmpty = false;

    /**
     * @return RepositoryInterface
     */
    abstract protected function getRepository();

    /**
     * @return string
     */
    abstract protected function getModel();

    /**
     * @param $object
     * @param $data
     * @param array $params
     *
     * @return string
     */
    public function preSetData($object, $data, $params = [])
    {
        if (is_int($data) || is_string($data)) {
            if (intval($data)) {
                return $this->getDataFromResource($data, $object, $params);
            }
        }

        return $data;
    }

    /**
     * @param $object
     * @param array $params
     *
     * @return string
     */
    public function preGetData($object, $params = [])
    {
        $data = $object->{$this->getName()};

        if ($data instanceof ResourceInterface) {
            //Reload from Database, but only if available
            $tmpData = $this->getRepository()->find($data->getId());

            if ($tmpData instanceof ResourceInterface) {
                return $tmpData;
            }
        }

        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForResource
     *
     * @param ResourceInterface                $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed                            $params
     *
     * @return string
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataFromResource
     *
     * @param string                           $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed                            $params
     *
     * @return string
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if (intval($data) > 0) {
            return $this->getRepository()->find($data);
        }

        return null;
    }

    /**
     * get data for query resource.
     *
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     *
     * @param ResourceInterface                $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed                            $params
     *
     * @return int|null
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    /**
     * get data for editmode.
     *
     * @see Object\ClassDefinition\Data::getDataForEditmode
     *
     * @param ResourceInterface                $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed                            $params
     *
     * @return int
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return $this->getDataForResource($data, $object, $params);
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     *
     * @param int                              $data
     * @param null|Model\Object\AbstractObject $object
     * @param array                            $params
     *
     * @return string
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * is empty.
     *
     * @param Model\Object\Concrete $data
     *
     * @return bool
     */
    public function isEmpty($data)
    {
        return !$data;
    }

    /**
     * get data for search index.
     *
     * @param $object
     * @param mixed $params
     *
     * @return int|string
     */
    public function getDataForSearchIndex($object, $params = [])
    {
        if ($object instanceof ResourceInterface) {
            return $object->getId();
        }

        return parent::getDataForSearchIndex($object, $params);
    }

    /**
     * @return bool
     */
    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * @param bool $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }
}

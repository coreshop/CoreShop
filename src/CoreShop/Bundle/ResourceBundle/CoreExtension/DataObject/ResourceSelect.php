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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model;

class ResourceSelect extends Model\DataObject\ClassDefinition\Data\Select
{
    use DISetStateTrait;

    /**
     * @var bool
     */
    public $allowEmpty = false;

    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param string              $type
     * @param string              $model
     * @param RepositoryInterface $repository
     * @param array               $params
     */
    public function __construct(string $type, string $model, RepositoryInterface $repository, array $params = [])
    {
        $this->fieldtype = $type;
        $this->phpdocType = $model;
        $this->model = $model;
        $this->params = $params;

        $this->repository($repository);
    }

    private function repository(RepositoryInterface $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    /**
     * @return string | array
     */
    public function getQueryColumnType()
    {
        return 'int(11)';
    }

    /**
     * @return string | array
     */
    public function getColumnType()
    {
        return 'int(11)';
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        if (is_int($data) || is_string($data)) {
            if ((int)$data) {
                return $this->getDataFromResource($data, $object, $params);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preGetData($object, $params = [])
    {
        if (!$object instanceof Model\AbstractModel) {
            return null;
        }

        $data = $object->getObjectVar($this->getName());

        if ($data instanceof ResourceInterface) {
            //Reload from Database, but only if available
            $tmpData = $this->repository()->find($data->getId());

            if ($tmpData instanceof ResourceInterface) {
                //Dirty Fix, Pimcore sometimes calls properties without getter
                //This could cause Problems with translations, therefore, we need to set
                //the value here
                $object->setValue($this->getName(), $tmpData);

                return $tmpData;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->model)) {
            return $data->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if ((int)$data > 0) {
            return $this->repository()->find($data);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->model)) {
            return $data->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return $this->getDataForResource($data, $object, $params);
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
    public function isEmpty($data)
    {
        return !$data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForSearchIndex($object, $params = [])
    {
        if ($object instanceof ResourceInterface) {
            return $object->getId();
        }

        return parent::getDataForSearchIndex($object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getForWebserviceExport($object, $params = [])
    {
        if ($object instanceof ResourceInterface) {
            return $object->getId();
        }

        return parent::getForWebserviceExport($object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromWebserviceImport($value, $object = null, $params = [], $idMapper = null)
    {
        return $this->repository()->find($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }

}

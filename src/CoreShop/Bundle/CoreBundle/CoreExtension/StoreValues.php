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

namespace CoreShop\Bundle\CoreBundle\CoreExtension;

use CoreShop\Bundle\CoreBundle\Form\Type\Product\ProductStoreValuesType;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\ProductStoreValuesRepositoryInterface;
use CoreShop\Component\Pimcore\BCLayer\CustomResourcePersistingInterface;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use JMS\Serializer\SerializationContext;
use Pimcore\Model;

class StoreValues extends Model\DataObject\ClassDefinition\Data implements CustomResourcePersistingInterface
{
    /**
     * @var string
     */
    public $fieldtype = 'coreShopStoreValues';

    /**
     * @var float
     */
    public $width;

    /**
     * @var int
     */
    public $defaultValue;

    /**
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
    public function getQueryColumnType()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnType()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Get ' . str_replace(['/**', '*/', '//'], '', $this->getName()) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '*' . "\n";
        $code .= '* @param \CoreShop\Component\Store\Model\StoreInterface $store' . "\n";
        $code .= '*' . "\n";
        $code .= '* @return null|' . $this->getPhpdocType() . '|\CoreShop\Component\Core\Model\ProductStoreValuesInterface' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function get' . ucfirst($key) . ' (\CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . '$this->' . $key . ' = $this->getClass()->getFieldDefinition("' . $key . '")->preGetData($this);' . "\n";
        $code .= "\t" . 'if (is_null($store)) {' . "\n";
        $code .= "\t\t" . 'return $this->' . $key . ";\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . '$data = $this->' . $key . ";\n";
        $code .= "\t" . 'if (is_array($data) && array_key_exists($store->getId(), $data) && $data[$store->getId()] instanceof \CoreShop\Component\Core\Model\ProductStoreValuesInterface) {' . "\n";
        $code .= "\t\t" . 'return $data[$store->getId()];' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\treturn null;" . "\n";
        $code .= "}\n\n";

        return $code;
    }

    public function getSetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Set ' . str_replace(['/**', '*/', '//'], '', $key) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '*' . "\n";
        $code .= '* @param array|\CoreShop\Component\Core\Model\ProductStoreValuesInterface $storeValues' . "\n";
        $code .= '* @param null|\CoreShop\Component\Store\Model\StoreInterface $store' . "\n";
        $code .= '*' . "\n";
        $code .= '* @return static' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function set' . ucfirst($key) . ' ($storeValues, \CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . "\n";
        $code .= "\t" . 'if (is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . ' = $' . $key . ';' . "\n";
        $code .= "\t" . '} else if (!is_null($store)) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . '[$store->getId()] = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n\n";
        $code .= "\t" . '$this->' . $key . ' = ' . '$this->getClass()->getFieldDefinition("' . $key . '")->preSetData($this, $this->' . $key . ');' . "\n";
        $code .= "\t" . 'return $this;' . "\n";
        $code .= "}\n\n";

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function preGetData($object, $params = [])
    {
        //TODO: Remove once CoreShop requires min Pimcore 5.5
        if (method_exists($object, 'getObjectVar')) {
            $data = $object->getObjectVar($this->getName());
        } else {
            $data = $object->{$this->getName()};
        }

        if (!in_array($this->getName(), $object->getO__loadedLazyFields())) {
            $data = $this->load($object, ['force' => true]);

            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($object, $setter)) {
                $object->$setter($data);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        if (!in_array($this->getName(), $object->getO__loadedLazyFields())) {
            $object->addO__loadedLazyField($this->getName());
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getProductStoreValuesRepository()->findForProduct($object);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $params = [])
    {
        if (!$object instanceof ProductInterface) {
            return;
        }

        //TODO: Remove once CoreShop requires min Pimcore 5.5
        if (method_exists($object, 'getObjectVar')) {
            $productStoreValues = $object->getObjectVar($this->getName());
        } else {
            $productStoreValues = $object->{$this->getName()};
        }

        if (!is_array($productStoreValues)) {
            return;
        }

        $validStoreValues = [];
        $availableStoreValues = $this->load($object, ['force' => true]);

        foreach ($productStoreValues as $storeId => $storeData) {
            $this->getEntityManager()->persist($storeData);
            $validStoreValues[] = $storeData->getId();
        }

        foreach ($availableStoreValues as $availableStoreValuesEntity) {
            if (!in_array($availableStoreValuesEntity->getId(), $validStoreValues)) {
                $this->getEntityManager()->remove($availableStoreValuesEntity);
            }
        }

        $this->getEntityManager()->flush();

    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        if (!$object instanceof ProductInterface) {
            return;
        }

        $availableStoreValues = $this->load($object, ['force' => true]);
        foreach ($availableStoreValues as $availableStoreValuesEntity) {
            $this->getEntityManager()->remove($availableStoreValuesEntity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $stores = $this->getStoreRepository()->findAll();
        $storeValues = $this->getProductStoreValuesRepository()->findForProduct($object);
        $storeData = [];

        if (!$object instanceof ProductInterface) {
            return $storeData;
        }

        foreach ($storeValues as $storeValuesEntity) {
            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);
            $serializedData = $this->getSerializer()->serialize($storeValuesEntity, 'json', $context);
            $values = json_decode($serializedData, true);

            $storeData[$storeValuesEntity->getStore()->getId()] = [
                'name'           => $storeValuesEntity->getStore()->getName(),
                'currencySymbol' => $storeValuesEntity->getStore()->getCurrency()->getSymbol(),
                'values'         => $values
            ];
        }

        /**
         * @var StoreInterface $store
         */
        foreach ($stores as $store) {

            if (array_key_exists($store->getId(), $storeData)) {
                continue;
            }

            //Fill missing stores with empty values
            $storeData[$store->getId()] = [
                'name'           => $store->getName(),
                'currencySymbol' => $store->getCurrency()->getSymbol(),
                'values'         => ['price' => 0],
            ];
        }

        return $storeData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $errors = [];
        $storeValues = [];

        foreach ($data as $storeId => $storeData) {
            if ($storeId === 0) {
                continue;
            }

            $storeValuesEntity = null;
            $storeValuesId = isset($storeData['id']) && is_numeric($storeData['id']) ? $storeData['id'] : null;

            if ($storeValuesId !== null) {
                $storeValuesEntity = $this->getProductStoreValuesRepository()->find($storeValuesId);
            }

            $form = $this->getFormFactory()->createNamed('', ProductStoreValuesType::class, $storeValuesEntity);

            $parsedData = $this->expandDotNotationKeys($storeData);
            $parsedData['storeId'] = $storeId;
            $parsedData['objectId'] = $object->getId();

            $form->submit($parsedData);

            if ($form->isValid()) {
                $storeValues[] = $form->getData();
            } else {
                foreach ($form->getErrors(true, true) as $e) {
                    $errorMessageTemplate = $e->getMessageTemplate();
                    foreach ($e->getMessageParameters() as $key => $value) {
                        $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
                    }

                    $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
                }

                throw new \Exception(implode(PHP_EOL, $errors));
            }
        }

        return $storeValues;
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
    public function getForCsvExport($object, $params = [])
    {
        $data = $this->getDataFromObjectParam($object, $params);

        if (!is_array($data) || empty($data)) {
            return '{}';
        }

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
        if (!$object) {
            throw new \Exception('This version of Pimcore is not supported for store values import.');
        }

        $data = $importValue == '' ? [] : json_decode($importValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(sprintf('Error decoding Store Price JSON `%s`: %s', $importValue, json_last_error_msg()));
        }

        if (is_array($data) && !empty($data)) {
            foreach ($data as $storeId => $newPrice) {
                $store = $this->getStoreRepository()->find($storeId);
                if (!$store instanceof StoreInterface) {
                    throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $storeId));
                }
            }
        }

        $oldStoreValues = $this->getProductStoreValuesRepository()->findForProduct($object);

        if (is_array($oldStoreValues)) {
            foreach ($oldStoreValues as $oldStoreValuesEntity) {
                $storeId = $oldStoreValuesEntity->getStore()->getId();

                if (!array_key_exists($storeId, $data)) {
                    $data[$storeId] = $oldStoreValuesEntity;
                }
            }
        }

        return $data;
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
     * @param mixed $value
     *
     * @return float|int
     */
    protected function toNumeric($value)
    {
        if (strpos((string) $value, '.') === false) {
            return (int) $value;
        }

        return (float) $value;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function expandDotNotationKeys(array $array)
    {
        $result = [];

        while (count($array)) {
            $value = reset($array);
            $key = key($array);
            unset($array[$key]);

            if (strpos($key, '.') !== false) {
                list($base, $ext) = explode('.', $key, 2);
                if (!array_key_exists($base, $array)) {
                    $array[$base] = [];
                }
                $array[$base][$ext] = $value;
            } elseif (is_array($value)) {
                $result[$key] = $this->expandDotNotationKeys($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return \Pimcore::getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactory()
    {
        return \Pimcore::getContainer()->get('form.factory');
    }

    /**
     * @return StoreRepositoryInterface
     */
    protected function getStoreRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.store');
    }

    /**
     * @return ProductStoreValuesRepositoryInterface
     */
    protected function getProductStoreValuesRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_store_values');
    }

    /**
     * @return ProductUnitRepositoryInterface
     */
    protected function getProductUnitRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_unit');
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    private function getSerializer()
    {
        return \Pimcore::getContainer()->get('jms_serializer');
    }

}

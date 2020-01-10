<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\CoreExtension;

use CoreShop\Component\Core\Model\ProductStorePriceInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\ProductStorePriceRepositoryInterface;
use CoreShop\Component\Pimcore\BCLayer\CustomResourcePersistingInterface;
use CoreShop\Component\Pimcore\BCLayer\LazyLoadedFields;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Model;

class StorePrice extends Model\DataObject\ClassDefinition\Data implements CustomResourcePersistingInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopStorePrice';

    /**
     * @var float
     */
    public $width;

    /**
     * @var int
     */
    public $defaultValue;

    /**
     * Type for the generated phpdoc.
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
        $code .= '* @return ' . $this->getPhpdocType() . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function get' . ucfirst($key) . ' (\CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . '$this->' . $key . ' = $this->getClass()->getFieldDefinition("' . $key . '")->preGetData($this);' . "\n";
        $code .= "\t" . 'if (is_null($store)) {' . "\n";
        $code .= "\t\t" . 'return $this->' . $key . ";\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . '$data = $this->' . $key . ";\n";
        $code .= "\t" . 'if (is_array($data) && array_key_exists($store->getId(), $data) && is_numeric($data[$store->getId()])) {' . "\n";
        $code .= "\t\t" . 'return (int)$data[$store->getId()];' . "\n";
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
        $code .= "\t" . '$fd = $this->getClass()->getFieldDefinition("' . $key . '");' . "\n";
        $code .= "\t" . '$currentData = $this->get' . ucfirst($this->getName()) . '();' . "\n";
        $code .= "\t" . 'if (is_null($' . $key . ')) {' . "\n";
        $code .= "\t\t" . 'return $this;' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . "\n";
        $code .= "\t" . 'if (!is_int($' . $key . ') && !is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . 'throw new \InvalidArgumentException(sprintf(\'Expected value to either be an array or an int, "%s" given\', gettype($storePrice)));' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'if (is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . '$currentData = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'else if (!is_null($store)) {' . "\n";
        $code .= "\t\t" . '$currentData[$store->getId()] = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";

        //TODO: Remove interface_exists once CoreShop requires min Pimcore 5.5
        if (interface_exists(Model\DataObject\DirtyIndicatorInterface::class)) {
            $code .= "\t" . '$isEqual = $fd->isEqual($currentData, $' . $key . ');' . "\n";
            $code .= "\t" . 'if (!$isEqual) {' . "\n";
            $code .= "\t\t" . '$this->markFieldDirty("' . $key . '", true);' . "\n";
            $code .= "\t" . '}' . "\n";
        }

        $code .= "\t" . '$this->' . $key . ' = ' . '$this->getClass()->getFieldDefinition("' . $key . '")->preSetData($this, $currentData);' . "\n";
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

        if ($object instanceof Model\DataObject\Concrete) {
            if (!LazyLoadedFields::hasLazyKey($object, $this->getName())) {
                $data = $this->load($object, ['force' => true]);

                //TODO: Remove once CoreShop requires min Pimcore 5.5
                if (method_exists($object, 'setObjectVar')) {
                    $object->setObjectVar($this->getName(), $data);
                } else {
                    $object->{$this->getName()} = $data;
                }

                $this->markAsLoaded($object);

                //TODO: Remove interface_exists once CoreShop requires min Pimcore 5.5
                if (interface_exists(Model\DataObject\DirtyIndicatorInterface::class)) {
                    if ($object instanceof Model\DataObject\DirtyIndicatorInterface) {
                        $object->markFieldDirty($this->getName(), false);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        $this->markAsLoaded($object);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            $prices = $this->getProductStorePriceRepository()->findForProductAndProperty($object, $this->getName());
            $data = [];

            /**
             * @var ProductStorePriceInterface $price
             */
            foreach ($prices as $price) {
                $data[$price->getStore()->getId()] = $price->getPrice();
            }

            return $data;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $params = [])
    {
        $em = \Pimcore::getContainer()->get('coreshop.manager.product_store_price');
        $factory = \Pimcore::getContainer()->get('coreshop.factory.product_store_price');
        $repo = $this->getProductStorePriceRepository();
        $storeRepo = $this->getStoreRepository();

        $data = $this->getDataFromObjectParam($object, $params);

        $storePrices = $repo->findForProductAndProperty($object, $this->getName());

        if (is_array($data) && !empty($data)) {
            foreach ($data as $storeId => $price) {
                $store = $storeRepo->find($storeId);

                if (!$store instanceof StoreInterface) {
                    throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $storeId));
                }

                /**
                 * @var ProductStorePriceInterface $storePrice
                 */
                $storePrice = null;

                /**
                 * @var ProductStorePriceInterface $searchStorePrice
                 */
                foreach ($storePrices as $searchStorePrice) {
                    if ($searchStorePrice->getStore()->getId() === $storeId) {
                        $storePrice = $searchStorePrice;

                        break;
                    }
                }

                if (null === $storePrice) {
                    $storePrice = $factory->createNew();
                }

                $storePrice->setProperty($this->getName());
                $storePrice->setProduct($object);
                $storePrice->setPrice($price);
                $storePrice->setStore($store);

                $em->persist($storePrice);
            }
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        $em = \Pimcore::getContainer()->get('coreshop.manager.product_store_price');
        $repo = $this->getProductStorePriceRepository();

        $storePrices = $repo->findForProductAndProperty($object, $this->getName());

        if (is_array($storePrices) && !empty($storePrices)) {
            foreach ($storePrices as $price) {
                $em->remove($price);
            }
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $stores = $this->getStoreRepository()->findAll();
        $prices = $this->getProductStorePriceRepository()->findForProductAndProperty($object, $this->getName());
        $storeData = [];

        /**
         * @var ProductStorePriceInterface $price
         */
        foreach ($prices as $price) {
            $priceValue = $price->getPrice();
            $priceValue = round($priceValue / 100, 2);

            $storeData[$price->getStore()->getId()] = [
                'name' => $price->getStore()->getName(),
                'currencySymbol' => $price->getStore()->getCurrency()->getSymbol(),
                'price' => $priceValue,
            ];
        }

        //Fill missing stores with null values
        /**
         * @var StoreInterface $store
         */
        foreach ($stores as $store) {
            if (array_key_exists($store->getId(), $storeData)) {
                continue;
            }

            $storeData[$store->getId()] = [
                'name' => $store->getName(),
                'currencySymbol' => $store->getCurrency()->getSymbol(),
                'price' => 0,
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

            $validData[$storeId] = (int) round((round($price, 2) * 100), 0);
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

        foreach ($data as $priceValue) {
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
            throw new \Exception('This version of Pimcore is not supported for storePrice import.');
        }
        $repo = $this->getProductStorePriceRepository();
        $storeRepo = $this->getStoreRepository();

        $data = $importValue == '' ? [] : json_decode($importValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(sprintf('Error decoding Store Price JSON `%s`: %s', $importValue, json_last_error_msg()));
        }

        $this->checkValidity($data, true);

        if (is_array($data) && !empty($data)) {
            foreach ($data as $storeId => $newPrice) {
                $store = $storeRepo->find($storeId);

                if (!$store instanceof StoreInterface) {
                    throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $storeId));
                }
            }
        }

        $oldStorePrices = $repo->findForProductAndProperty($object, $this->getName());

        if (is_array($oldStorePrices) && !empty($oldStorePrices)) {
            foreach ($oldStorePrices as $oldStorePrice) {
                $storeId = $oldStorePrice->getStore()->getId();

                if (!array_key_exists($storeId, $data)) {
                    $data[$storeId] = $oldStorePrice->getPrice();
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
    public function isEmpty($data)
    {
        return is_null($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyLoading()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDirtyDetection()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqual($oldValue, $newValue)
    {
        if (!is_array($oldValue) || !is_array($newValue)) {
            return false;
        }

        return $oldValue === $newValue;
    }

    /**
     * @param Model\DataObject\Concrete $object
     */
    protected function markAsLoaded($object)
    {
        if (!$object instanceof Model\DataObject\Concrete) {
            return;
        }

        LazyLoadedFields::addLazyKey($object, $this->getName());
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
     * @return StoreRepositoryInterface
     */
    protected function getStoreRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.store');
    }

    /**
     * @return ProductStorePriceRepositoryInterface
     */
    protected function getProductStorePriceRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_store_price');
    }
}

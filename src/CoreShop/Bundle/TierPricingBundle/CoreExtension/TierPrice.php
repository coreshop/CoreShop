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

namespace CoreShop\Bundle\TierPricingBundle\CoreExtension;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Pimcore\BCLayer\CustomResourcePersistingInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRange;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Repository\ProductTierPriceRepositoryInterface;
use Pimcore\Model;
use Pimcore\Model\DataObject;

class TierPrice extends Model\DataObject\ClassDefinition\Data implements CustomResourcePersistingInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopTierPrice';

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
    public $phpdocType = ProductTierPriceInterface::class;

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
        $code .= '* @return \\' . $this->getPhpdocType() . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function get' . ucfirst($key) . ' (\CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . '$this->' . $key . ' = $this->getClass()->getFieldDefinition("' . $key . '")->preGetData($this);' . "\n";
        $code .= "\t" . '$data = $this->' . $key . ";\n";

        // insert this line if inheritance from parent objects is allowed
        if ($class instanceof DataObject\ClassDefinition && $class->getAllowInherit() && $this->supportsInheritance()) {
            $code .= "\t" . 'if(\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("' . $key . '")->isEmpty($data)) {' . "\n";
            $code .= "\t\t" . 'return $this->getValueFromParent("' . $key . '", $store);' . "\n";
            $code .= "\t" . '}' . "\n";
        }

        $code .= "\t" . 'if (is_null($store)) {' . "\n";
        $code .= "\t\t" . 'return $this->' . $key . ";\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'if (is_array($data) && array_key_exists($store->getId(), $data) && $data[$store->getId()] instanceof \\' . ProductTierPriceInterface::class . ') {' . "\n";
        $code .= "\t\t" . 'return $data[$store->getId()];' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t return null;" . "\n";
        $code .= "}\n\n";

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Get ' . str_replace(['/**', '*/', '//'], '', $this->getName()) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '* @return static' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function set' . ucfirst($key) . ' ($' . $key . ', \CoreShop\Component\Store\Model\StoreInterface $store = null) {' . "\n";
        $code .= "\t" . 'if (is_null($' . $key . ')) {' . "\n";
        $code .= "\t\t" . 'return $this;' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . "\n";
        $code .= "\t" . 'if (!is_int($' . $key . ') && !is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . 'throw new \InvalidArgumentException(sprintf(\'Expected value to either be an array or an int, "%s" given\', gettype($' . $key . ')));' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'if (is_array($' . $key . ')) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . ' = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'else if (!is_null($store)) {' . "\n";
        $code .= "\t\t" . '$this->' . $key . '[$store->getId()] = $' . $key . ';' . "\n";
        $code .= "\t" . '}' . "\n";
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

            $tierPrices = $this->getProductTierPriceRepository()->findForProductAndProperty($object, $this->getName());
            $data = [];

            /**
             * @var ProductTierPriceInterface $tierPrice
             */
            foreach ($tierPrices as $tierPrice) {
                $data[$tierPrice->getStore()->getId()] = $tierPrice;
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
        $storeRepo = $this->getStoreRepository();

        $data = $this->getDataFromObjectParam($object, $params);

        if (!is_array($data) || empty($data)) {
            return;
        }

        foreach ($data as $storeId => $tierPrice) {
            $store = $storeRepo->find($storeId);

            if (!$store instanceof StoreInterface) {
                throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $storeId));
            }

            if (!$tierPrice instanceof ProductTierPriceInterface) {
                continue;
            }

            $em->persist($tierPrice);
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        $em = \Pimcore::getContainer()->get('coreshop.manager.product_store_price');
        $repo = $this->getProductTierPriceRepository();

        $tierPrices = $repo->findForProductAndProperty($object, $this->getName());

        if (is_array($tierPrices) && !empty($tierPrices)) {
            foreach ($tierPrices as $tierPrice) {
                $em->remove($tierPrice);
            }
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $tierPrices = $this->getProductTierPriceRepository()->findForProductAndProperty($object, $this->getName());
        $storeData = [];

        $populatedStores = [];

        /**
         * @var ProductTierPriceInterface $tierPrice
         */
        foreach ($tierPrices as $tierPrice) {
            $populatedStores[] = $tierPrice->getStore()->getId();
            $storeData[$tierPrice->getStore()->getId()] = [
                'currencySymbol' => $tierPrice->getStore()->getCurrency()->getSymbol(),
                'id'             => $tierPrice->getId(),
                'ranges'         => []
            ];

            foreach ($tierPrice->getRanges() as $range) {
                $storeData[$tierPrice->getStore()->getId()]['ranges'][] = $this->getTierPriceRangeAsArray($range);
            }

        }

        // fill up empty stores
        foreach ($this->getStoreRepository()->findAll() as $store) {
            if (in_array($store->getId(), $populatedStores)) {
                continue;
            }

            $storeData[$store->getId()] = [
                'currencySymbol' => $store->getCurrency()->getSymbol(),
                'id'             => null,
                'ranges'         => []
            ];
        }

        return $storeData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (empty($data)) {
            return [];
        }

        $validData = [];

        $factory = \Pimcore::getContainer()->get('coreshop.factory.product_tier_price');
        $rangeFactory = \Pimcore::getContainer()->get('coreshop.factory.product_tier_price_range');
        $em = \Pimcore::getContainer()->get('coreshop.manager.product_store_price');
        $repo = $this->getProductTierPriceRepository();
        $storeRepo = $this->getStoreRepository();

        foreach ($data as $storeId => $tierPriceData) {

            if ($storeId === 0) {
                continue;
            }

            if (!is_array($tierPriceData)) {
                continue;
            }

            $tierPriceId = $tierPriceData['id'];
            $tierPricesRanges = $tierPriceData['ranges'];

            $tierPrice = null;
            $isNew = false;

            if (is_numeric($tierPriceId)) {
                $tierPrice = $repo->find((int)$tierPriceId);
            }

            if (empty($tierPricesRanges)) {
                if ($tierPrice instanceof ProductTierPriceInterface) {
                    $em->remove($tierPrice);
                }
                continue;
            }

            if (!$tierPrice instanceof ProductTierPriceInterface) {
                $isNew = true;
                $store = $storeRepo->find($storeId);
                $tierPrice = $factory->createNew();
                $tierPrice->setProperty($this->getName());
                $tierPrice->setStore($store);
                $tierPrice->setProduct($object);
            }

            $tierPrice->setActive(true);

            $existingRanges = clone $tierPrice->getRanges();
            $tierPrice->removeAllRanges();

            foreach ($tierPricesRanges as $priceRange) {

                $priceRangeId = !empty($priceRange['tier_range_id']) && is_numeric($priceRange['tier_range_id']) ? (int)$priceRange['tier_range_id'] : null;

                $storeRangeExists = false;
                $filteredRangeCollection = null;
                if ($isNew === false && $priceRangeId !== null) {
                    $filteredRangeCollection = $existingRanges->filter(function (ProductTierPriceRange $range) use ($priceRangeId) {
                        return $range->getId() === $priceRangeId;
                    });

                    if ($filteredRangeCollection->isEmpty() === false) {
                        $storeRangeExists = true;
                    }
                }

                if ($storeRangeExists === true) {
                    $range = $filteredRangeCollection->first();
                } else {
                    /** @var ProductTierPriceRangeInterface $range */
                    $range = $rangeFactory->createNew();
                }

                $range->setRangeFrom((int)$priceRange['tier_range_from']);
                $range->setRangeTo((int)$priceRange['tier_range_to']);
                $range->setPrice((int)round((round($priceRange['tier_price'], 2) * 100), 0));
                $range->setPercentageDiscount((float)$priceRange['tier_percentage_discount']);
                $range->setHighlighted((bool)$priceRange['tier_highlight']);
                $tierPrice->addRange($range);
            }

            $validData[$storeId] = $tierPrice;
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

        /**
         * @var int                       $storeId
         * @var ProductTierPriceInterface $tierPrice
         */
        foreach ($data as $storeId => $tierPrice) {

            if (!$omitMandatoryCheck) {
                if (!$this->isEmpty($tierPrice) && $tierPrice->getRanges()->isEmpty()) {
                    throw new Model\Element\ValidationException('invalid tier pricing data for store [' . $storeId . ']');
                }
            }

            /**
             * @var int                            $rowIndex
             * @var ProductTierPriceRangeInterface $tierPricesRange
             */

            $lastEnd = -1;

            foreach ($tierPrice->getRanges() as $rowIndex => $tierPricesRange) {

                $realRowIndex = $rowIndex + 1;

                if (!$this->isEmpty($tierPricesRange) && !$omitMandatoryCheck) {
                    $priceValue = $this->toNumeric($tierPricesRange->getPrice());

                    if (empty($priceValue) || !is_numeric($priceValue)) {
                        throw new Model\Element\ValidationException('Field price cannot be empty!');
                    } elseif ($priceValue >= PHP_INT_MAX) {
                        throw new Model\Element\ValidationException('Value exceeds PHP_INT_MAX please use an input data type instead of numeric!');
                    }

                    if (strlen($this->getMinValue()) && $this->getMinValue() > $priceValue) {
                        throw new Model\Element\ValidationException('Value in field [ ' . $this->getName() . ' ] is not at least ' . $this->getMinValue());
                    }
                    if (strlen($this->getMaxValue()) && $priceValue > $this->getMaxValue()) {
                        throw new Model\Element\ValidationException('Value in field [ ' . $this->getName() . ' ] is bigger than ' . $this->getMaxValue());
                    }

                    if (!is_numeric($tierPricesRange->getRangeFrom())) {
                        throw new Model\Element\ValidationException('Field "range from" in row ' . $realRowIndex . ' needs to be numeric');
                    } elseif ((int)$tierPricesRange->getRangeFrom() < 0) {
                        throw new Model\Element\ValidationException('Field "range from" in row ' . $realRowIndex . '  needs to be greater or equal than 0');
                    } elseif ((int)$tierPricesRange->getRangeFrom() <= $lastEnd) {
                        throw new Model\Element\ValidationException('Field "range from" in row ' . $realRowIndex . '  needs to be greater than ' . $lastEnd);
                    }

                    if (!is_numeric($tierPricesRange->getRangeTo())) {
                        throw new Model\Element\ValidationException('Field "range to" in row ' . $realRowIndex . ' needs to be numeric');
                    } elseif ((int)$tierPricesRange->getRangeTo() <= $tierPricesRange->getRangeFrom()) {
                        throw new Model\Element\ValidationException('Field "range to" in row ' . $realRowIndex . '  needs to be greater than ' . $tierPricesRange->getRangeFrom());
                    }
                }

                $lastEnd = (int)$tierPricesRange->getRangeTo();
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
        throw new \Exception('csv export of tier pricing is currently not supported.');
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
        return is_null($data) || (is_array($data) && count($data) === 0);
    }

    /**
     * @param mixed $value
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

    /**
     * @return ProductTierPriceRepositoryInterface
     */
    protected function getProductTierPriceRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_tier_price');
    }

    /**
     * @param ProductTierPriceRangeInterface $range
     *
     * @return array
     */
    protected function getTierPriceRangeAsArray(ProductTierPriceRangeInterface $range)
    {
        return [
            'tier_range_id'            => $range->getId(),
            'tier_range_from'          => $range->getRangeFrom(),
            'tier_range_to'            => $range->getRangeTo(),
            'tier_price'               => round($range->getPrice() / 100, 2),
            'tier_percentage_discount' => $range->getPercentageDiscount(),
            'tier_highlight'           => $range->getHighlighted(),
        ];
    }

}

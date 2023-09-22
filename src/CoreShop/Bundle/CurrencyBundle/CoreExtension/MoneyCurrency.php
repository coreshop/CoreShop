<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CurrencyBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\Pimcore\CacheMarshallerInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\Money;
use Pimcore\Model;
use Pimcore\Model\DataObject\Concrete;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement, RedundantCondition
 */
class MoneyCurrency extends Model\DataObject\ClassDefinition\Data implements
    Model\DataObject\ClassDefinition\Data\ResourcePersistenceAwareInterface,
    Model\DataObject\ClassDefinition\Data\QueryResourcePersistenceAwareInterface,
    CacheMarshallerInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopMoneyCurrency';

    /**
     * @var int
     */
    public $width = 0;

    /**
     * @var float
     */
    public $minValue = 0;

    /**
     * @var float
     */
    public $maxValue = 0;

    public function getParameterTypeDeclaration(): ?string
    {
        return '?\\' . Money::class;
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return '?\\' . Money::class;
    }

    public function getPhpdocInputType(): ?string
    {
        return '?\\' . Money::class;
    }

    public function getPhpdocReturnType(): ?string
    {
        return '?\\' . Money::class;
    }

    public function getQueryColumnType()
    {
        return [
            'value' => 'bigint(20)',
            'currency' => 'int',
        ];
    }

    public function getColumnType()
    {
        return [
            'value' => 'bigint(20)',
            'currency' => 'int',
        ];
    }

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

    public function preGetData($object, $params = [])
    {
        /**
         * @var Concrete $object
         */
        $data = $object->getObjectVar($this->getName());

        if ($data instanceof Money) {
            if ($data->getCurrency()) {
                $currency = $this->getCurrencyById((int) $data->getCurrency()->getId());

                return new Money($data->getValue(), $currency);
            }
        }

        return $data;
    }

    public function getDataForResource($data, $object = null, $params = [])
    {
        if ($data instanceof \CoreShop\Component\Currency\Model\Money) {
            if ($data->getCurrency() instanceof CurrencyInterface) {
                return [
                    $this->getName() . '__value' => $data->getValue(),
                    $this->getName() . '__currency' => $data->getCurrency()->getId(),
                ];
            }
        }

        return [
            $this->getName() . '__value' => null,
            $this->getName() . '__currency' => null,
        ];
    }

    public function getDataFromResource($data, $object = null, $params = [])
    {
        $currencyIndex = $this->getName() . '__currency';

        if (is_array($data) && isset($data[$currencyIndex]) && null !== $data[$currencyIndex]) {
            $currency = $this->getCurrencyById($data[$this->getName() . '__currency']);

            if (null !== $currency) {
                return new \CoreShop\Component\Currency\Model\Money((int) ($data[$this->getName() . '__value'] ?? 0), $currency);
            }
        }

        return null;
    }

    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        return $this->getDataForResource($data, $object, $params);
    }

    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if ($data instanceof \CoreShop\Component\Currency\Model\Money) {
            if ($data->getCurrency() instanceof CurrencyInterface) {
                return [
                    'value' => $data->getValue() / $this->getDecimalFactor(),
                    'currency' => $data->getCurrency()->getId(),
                ];
            }
        }

        return [
            'value' => null,
            'currency' => null,
        ];
    }

    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (is_array($data)) {
            $currency = $this->getCurrencyById($data['currency']);

            if (null !== $currency) {
                return new \CoreShop\Component\Currency\Model\Money($this->toNumeric($data['value']), $currency);
            }
        }

        return null;
    }

    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    public function checkValidity($data, $omitMandatoryCheck = false, $params = [])
    {
        if (!$omitMandatoryCheck && $this->getMandatory() && $this->isEmpty($data)) {
            throw new Model\Element\ValidationException('Empty mandatory field [ ' . $this->getName() . ' ]');
        }

        if ($this->isEmpty($data)) {
            return;
        }

        if (!$this->isEmpty($data) && !$omitMandatoryCheck) {
            if ($data->getValue() >= \PHP_INT_MAX) {
                throw new Model\Element\ValidationException(
                    'Value exceeds PHP_INT_MAX please use an input data type instead of numeric!',
                );
            }

            if ((string) $this->getMinValue() !== '' && $this->getMinValue() > $data->getValue()) {
                throw new Model\Element\ValidationException(
                    'Value in field [ ' . $this->getName() . ' ] is not at least ' . $this->getMinValue(),
                );
            }

            if ((string) $this->getMaxValue() !== '' && $data->getValue() > $this->getMaxValue()) {
                throw new Model\Element\ValidationException(
                    'Value in field [ ' . $this->getName() . ' ] is bigger than ' . $this->getMaxValue(),
                );
            }
        }
    }

    public function getForCsvExport($object, $params = [])
    {
        $data = $this->getDataFromObjectParam($object, $params);

        return json_encode($this->getDataForResource($data, $object, $params));
    }

    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
        //TODO
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function isEmpty($data)
    {
        if ($data instanceof Money) {
            return false;
        }

        if (!is_array($data)) {
            return true;
        }

        if (strlen($data['value']) < 1) {
            return true;
        }

        if (empty($data['currency'])) {
            return true;
        }

        return false;
    }

    public function marshalForCache(Concrete $concrete, mixed $data): mixed
    {
        if (!$data instanceof Money) {
            return $data;
        }

        return [
            'value'    => $data->getValue(),
            'currency' => $data->getCurrency()->getId(),
        ];
    }

    public function unmarshalForCache(Concrete $concrete, mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        return new Money($data['value'], $this->getCurrencyById($data['currency']));
    }

    /**
     * @param int $currencyId
     *
     * @return CurrencyInterface|null
     */
    protected function getCurrencyById($currencyId)
    {
        return \Pimcore::getContainer()->get('coreshop.repository.currency')->find($currencyId);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return \Pimcore::getContainer()->get('coreshop.manager.currency');
    }

    protected function getDecimalFactor()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.currency.decimal_factor');
    }

    /**
     * @param mixed $value
     */
    protected function toNumeric($value): float|int
    {
        return (int) round($value * $this->getDecimalFactor());
    }
}

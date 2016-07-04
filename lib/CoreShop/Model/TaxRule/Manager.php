<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\TaxRule;

use CoreShop\Model\Configuration;
use CoreShop\Model\Plugin\TaxManager;
use CoreShop\Model\State;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\TaxRuleGroup;
use CoreShop\Model\User\Address;

class Manager implements TaxManager
{
    /**
     * @var TaxCalculator
     */
    protected $tax_calculator;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var int
     */
    protected $type;

    /**
     * Manager constructor.
     *
     * @param Address $address
     * @param $type
     */
    public function __construct(Address $address, $type)
    {
        $this->address = $address;
        $this->type = $type;
    }

    /**
     * Check if Manager is available for specific address.
     *
     * @param Address $address
     * @param string  $type
     *
     * @return bool
     */
    public static function isAvailableForThisAddress(Address $address, $type)
    {
        return true; // default manager, available for all addresses
    }

    /**
     * Return the tax calculator associated to this address.
     *
     * @return TaxCalculator
     */
    public function getTaxCalculator()
    {
        if ($this->tax_calculator instanceof TaxCalculator) {
            return $this->tax_calculator;
        }

        if(!Configuration::get('SYSTEM.BASE.TAX.ENABLED')) {
            return new TaxCalculator();
        }

        $cacheKey = $this->getCacheKey();

        if (!\Zend_Registry::isRegistered($cacheKey)) {
            $taxRuleGroup = TaxRuleGroup::getById($this->type);
            $taxRules = $taxRuleGroup->getForCountryAndState($this->address->getCountry(), $this->address->getState());
            $taxes = array();
            $firstRow = true;
            $behavior = false;

            foreach ($taxRules as $rule) {
                $tax = $rule->getTax();
                $taxes[] = $tax;

                //Tax behaviour will be applied from first rule
                if ($firstRow) {
                    $behavior = $rule->getBehavior();

                    $firstRow = false;
                }

                if ($rule->getBehavior() == TaxCalculator::DISABLE_METHOD) {
                    break;
                }
            }

            \Zend_Registry::set($cacheKey, new TaxCalculator($taxes, $behavior));
        }

        return \Zend_Registry::get($cacheKey);
    }

    /**
     * get CacheKey for Address.
     *
     * @return string
     */
    private function getCacheKey()
    {
        return 'coreshop_tax_calculator_'.$this->address->getCountry()->getId().
            ($this->address->getState() instanceof State ? $this->address->getState()->getId() : '').'_'.
            (int) $this->type;
    }
}

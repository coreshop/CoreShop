<?php

//Migrate all Rules: ProductRules, CartRules, SpecificPrices

$productRules = new \CoreShop\Model\Product\PriceRule\Listing();
$cartRules = new \CoreShop\Model\Cart\PriceRule\Listing();
$specificPrices = new \CoreShop\Model\Product\SpecificPrice\Listing();


/**
 * @param $conds
 * @return array
 */
function convert($conds) {
    $conditions = [];

    foreach ($conds as $cond) {
        if($cond instanceof \CoreShop\Model\PriceRule\Condition\Category) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Categories();
            $newCond->setCategories([$cond->getCategory()->getId()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Country) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Countries();
            $newCond->setCountries([$cond->getCountry()->getId()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Customer) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Customers();
            $newCond->setCustomers([$cond->getCustomer()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\CustomerGroup) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\CustomerGroups();
            $newCond->setCustomerGroups([$cond->getCustomerGroup()->getId()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Persona) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Personas();
            $newCond->setPersonas([$cond->getPersona()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Product) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Products();
            $newCond->setProducts([$cond->getProduct()->getId()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Shop) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Shops();
            $newCond->setShops([$cond->getShop()->getId()]);

            $conditions[] = $newCond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\Zone) {
            $newCond = new \CoreShop\Model\PriceRule\Condition\Zones();
            $newCond->setZones([$cond->getZone()->getId()]);
        } else {
            $conditions[] = $cond;
        }
    }

    return $conditions;
}

foreach($productRules->getData() as $rule) {
    if($rule instanceof \CoreShop\Model\Product\PriceRule) {
        $rule->setConditions(convert($rule->getConditions()));
        $rule->save();
    }
}

foreach($cartRules->getData() as $rule) {
    if($rule instanceof \CoreShop\Model\Cart\PriceRule) {
        $rule->setConditions(convert($rule->getConditions()));
        $rule->save();
    }
}

foreach($specificPrices->getData() as $rule) {
    if($rule instanceof \CoreShop\Model\Product\SpecificPrice) {
        $rule->setConditions(convert($rule->getConditions()));
        $rule->save();
    }
}
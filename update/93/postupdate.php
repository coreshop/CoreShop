<?php

$listing = \CoreShop\Model\CustomerGroup::getList();

foreach($listing->load() as $group) {
    if($group instanceof CoreShop\Model\CustomerGroup) {
        $gr = \CoreShop\Model\Customer\Group::create();
        $gr->setName($group->getName());
        $gr->setKey(\Pimcore\File::getValidFilename($group->getName()));
        $gr->setParent(\Pimcore\Model\Object\Service::createFolderByPath("/coreshop/customer-groups"));
        $gr->setPublished(true);
        $gr->save();
    }
}

$listingUsers = \CoreShop\Model\User::getList();
$users = $listingUsers->load();

foreach($users as $user) {
    if($user instanceof \CoreShop\Model\User) {
        $groups = $user->getGroups();
        $customerGroups = [];

        if(is_array($groups)) {
            foreach ($groups as $gr) {
                $oldGroup = \CoreShop\Model\CustomerGroup::getById($gr);
                $listing = \CoreShop\Model\Customer\Group::getByName($oldGroup->getName());

                if (count($listing->getObjects()) > 0) {
                    foreach ($listing->getObjects() as $newGroup) {
                        $customerGroups[] = $newGroup;
                    }
                }
            }

            $user->setCustomerGroups($customerGroups);
            $user->save();
        }
    }
}

//Migrate all Rules: ProductRules, CartRules, SpecificPrices

$productRules = new \CoreShop\Model\Product\PriceRule\Listing();
$cartRules = new \CoreShop\Model\Cart\PriceRule\Listing();
$specificPrices = new \CoreShop\Model\Product\SpecificPrice\Listing();
$shippingRules = new \CoreShop\Model\Carrier\ShippingRule\Listing();

/**
 * @param $conds
 * @return array
 */
function convert($conds) {
    $conditions = [];

    foreach ($conds as $cond) {

        if($cond instanceof \CoreShop\Model\Carrier\ShippingRule\Condition\CustomerGroups) {
            $newGroups = getNewGroupsForOldGroups($cond->getCustomerGroups());
            $cond->setCustomerGroups($newGroups);

            $conditions[] = $cond;
        } else if($cond instanceof \CoreShop\Model\PriceRule\Condition\CustomerGroups) {
            $newGroups = getNewGroupsForOldGroups($cond->getCustomerGroups());
            $cond->setCustomerGroups($newGroups);

            $conditions[] = $cond;
        }
        else {
            $conditions[] = $cond;
        }
    }

    return $conditions;
}

/**
 * @param $groups
 * @return array
 */
function getNewGroupsForOldGroups($groups) {
    $newGroups = [];

    foreach($groups as $group) {
        $oldGroup = \CoreShop\Model\CustomerGroup::getById($group);

        if($oldGroup instanceof \CoreShop\Model\CustomerGroup) {
            $newGroup = \CoreShop\Model\Customer\Group::getByName($oldGroup->getName());

            foreach($newGroup->getObjects() as $newGroup) {
                $newGroups[] = $newGroup->getId();
            }
        }
    }

    return $newGroups;
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

foreach($shippingRules->getData() as $rule) {
    if($rule instanceof \CoreShop\Model\Carrier\ShippingRule) {
        $rule->setConditions(convert($rule->getConditions()));
        $rule->save();
    }
}
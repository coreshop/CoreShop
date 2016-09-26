<?php

$file = PIMCORE_TEMPORARY_DIRECTORY . "/addresses.tmp";

$list = \CoreShop\Model\User::getList();

$all = [];

foreach($list->getObjects() as $customer) {
    $customerArray = [];

    foreach($customer->getAddresses() as $address) {
        $customerArray[] = getArrayFromAddress($address);
    }

    $all[$customer->getId()] = $customerArray;
}

file_put_contents($file, serialize($all));

//ORDERS
$file2 = PIMCORE_TEMPORARY_DIRECTORY . "/order_addresses.tmp";
$list = \CoreShop\Model\Order::getList();
$orders = [];

foreach($list->getObjects() as $order) {
    $customerArray = [];
    $shippingAddress = null;
    $billingAddress = null;

    if($order->getShippingAddress() instanceof \Pimcore\Model\Object\Fieldcollection) {
        $address = $order->getShippingAddress()->getItems();

        if (count($address) > 0) {
            $shippingAddress = getArrayFromAddress($address[0]);
        }
    }

    if($order->getBillingAddress() instanceof \Pimcore\Model\Object\Fieldcollection) {
        $address = $order->getBillingAddress()->getItems();

        if (count($address) > 0) {
            $billingAddress = getArrayFromAddress($address[0]);
        }
    }

    $orders[$order->getId()] = [
        'shipping' => $shippingAddress,
        'billing' => $billingAddress
    ];
}

file_put_contents($file2, serialize($orders));

//CARTS
$file3 = PIMCORE_TEMPORARY_DIRECTORY . "/cart_addresses.tmp";
$list = \CoreShop\Model\Cart::getList();
$carts = [];

foreach($list->getObjects() as $cart) {
    $customerArray = [];
    $shippingAddress = null;
    $billingAddress = null;

    if($cart->getShippingAddress() instanceof \Pimcore\Model\Object\Fieldcollection) {
        $address = $cart->getShippingAddress()->getItems();

        if (count($address) > 0) {
            $shippingAddress = getArrayFromAddress($address[0]);
        }
    }

    if($cart->getBillingAddress() instanceof \Pimcore\Model\Object\Fieldcollection) {
        $address = $cart->getBillingAddress()->getItems();

        if (count($address) > 0) {
            $billingAddress = getArrayFromAddress($address[0]);
        }
    }

    $carts[$cart->getId()] = [
        'shipping' => $shippingAddress,
        'billing' => $billingAddress
    ];
}

file_put_contents($file3, serialize($carts));

function getArrayFromAddress($address) {
    $entry = get_object_vars($address);

    $entry['country'] = $address->getCountry() instanceof CoreShop\Model\Country ? $address->getCountry()->getId() : null;
    $entry['state'] = $address->getState() instanceof CoreShop\Model\State ? $address->getState()->getId() : null;

    unset($entry['object']);
    unset($entry['fieldname']);
    unset($entry['index']);
    unset($entry['type']);

    return $entry;
}

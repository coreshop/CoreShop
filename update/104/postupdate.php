<?php

$install = new \CoreShop\Plugin\Install();
$install->createClass('CoreShopUserAddress', true);
$install->createClass('CoreShopUser', true);
$install->createClass('CoreShopOrder', true);
$install->createClass('CoreShopCart', true);

if (file_exists(PIMCORE_TEMPORARY_DIRECTORY . "/addresses.tmp")) {
    try {
        $addressesSerialized = file_get_contents(PIMCORE_TEMPORARY_DIRECTORY . "/addresses.tmp");
        $addresses = unserialize($addressesSerialized);

        foreach ($addresses as $userId => $addressArray) {
            $user = \CoreShop\Model\User::getById($userId);

            if ($user instanceof \CoreShop\Model\User) {
                $newAddresses = [];
                foreach ($addressArray as $address) {
                    $newAddresses[] = createAddressFromArray($user->getPathForAddresses(), $address);
                }

                $user->setAddresses($newAddresses);
                $user->save();
            }
        }
    } catch (\Exception $ex) {
    }
}

if (file_exists(PIMCORE_TEMPORARY_DIRECTORY . "/order_addresses.tmp")) {
    try {
        $addressesSerialized = file_get_contents(PIMCORE_TEMPORARY_DIRECTORY . "/order_addresses.tmp");
        $addresses = unserialize($addressesSerialized);

        foreach ($addresses as $orderId => $addressArray) {
            $order = \CoreShop\Model\Order::getById($orderId);

            if ($order instanceof \CoreShop\Model\Order) {
                $shippingAddress = $addressArray['shipping'];
                $billingAddress = $addressArray['billing'];

                if ($shippingAddress) {
                    $shipping = createAddressFromArray(\Pimcore\Model\Object\Service::createFolderByPath($order->getFullPath() . "/addresses"), $shippingAddress, 'shipping');
                    $order->setShippingAddress($shipping);
                }

                if ($billingAddress) {
                    $billing = createAddressFromArray(\Pimcore\Model\Object\Service::createFolderByPath($order->getFullPath() . "/addresses"), $billingAddress, 'billing');
                    $order->setBillingAddress($billing);
                }

                $order->save();
            }
        }
    } catch (\Exception $ex) {
        throw $ex;
    }
}

if (file_exists(PIMCORE_TEMPORARY_DIRECTORY . "/cart_addresses.tmp")) {
    try {
        $addressesSerialized = file_get_contents(PIMCORE_TEMPORARY_DIRECTORY . "/cart_addresses.tmp");
        $addresses = unserialize($addressesSerialized);

        foreach ($addresses as $orderId => $addressArray) {
            $cart = \CoreShop\Model\Cart::getById($orderId);

            if ($cart instanceof \CoreShop\Model\Cart) {
                $shippingAddress = $addressArray['shipping'];
                $billingAddress = $addressArray['billing'];

                if ($shippingAddress) {
                    $shipping = createAddressFromArray(\Pimcore\Model\Object\Service::createFolderByPath($cart->getFullPath() . "/addresses"), $shippingAddress, 'shipping');
                    $cart->setShippingAddress($shipping);
                }

                if ($billingAddress) {
                    $billing = createAddressFromArray(\Pimcore\Model\Object\Service::createFolderByPath($cart->getFullPath() . "/addresses"), $billingAddress, 'billing');
                    $cart->setBillingAddress($billing);
                }

                $cart->save();
            }
        }
    } catch (\Exception $ex) {
        throw $ex;
    }
}

function createAddressFromArray($path, $address, $key = null)
{
    $newAddress = \CoreShop\Model\User\Address::create();
    $newAddress->setParent($path);
    $newAddress->setPublished(true);
    $newAddress->setKey($key ? $key : \Pimcore\File::getValidFilename($address['name'] ? $address['name'] : $address['firstname'] . " " . $address['lastname']));
    $newAddress->setValues($address);
    $newAddress->setKey(\Pimcore\Model\Object\Service::getUniqueKey($newAddress));
    $newAddress->save();

    return $newAddress;
};

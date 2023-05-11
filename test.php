<?php

$customer = Pimcore\Model\DataObject\CoreShopCustomer::getById(38357);
$customer->setFirstname('asdfasdfasdfasdf');
$customer->save();
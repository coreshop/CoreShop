<?php

$db = \Pimcore\Db::get();

$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_productSpecificPrices'");
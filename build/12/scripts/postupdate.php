<?php

$install = new \CoreShop\Plugin\Install();
$install->executeSQL("b-12");

$tax = new \CoreShop\Model\Tax();
$tax->getLocalizedFields()->createUpdateTable();

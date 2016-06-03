<?php

$install = new \CoreShop\Plugin\Install();
$install->installDocuments("documents");

\CoreShop\Model\Configuration::set("SYSTEM.MAIL.CONFIRMATION", "/shop/email/order-confirmation");
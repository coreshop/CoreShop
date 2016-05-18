<?php

$install = new \CoreShop\Plugin\Install();
$install->installObjectData("threadContacts", "Messaging\\");
$install->installMessagingContacts();
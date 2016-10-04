<?php

$configurationMail = \CoreShop\Model\Configuration::get("SYSTEM.MAIL.CONFIRMATION");

$languages = \Pimcore\Tool::getValidLanguages();

foreach($languages as $lang) {
    $path = "/" . $lang . "/" . $configurationMail;
    $document = \Pimcore\Model\Document::getByPath($path);

    if($document instanceof \Pimcore\Model\Document\Email) {
        \CoreShop\Model\Configuration::set("SYSTEM.MAIL.CONFIRMATION." . strtoupper($lang), $document->getFullPath());
    }
}

\CoreShop\Model\Configuration::remove("SYSTEM.MAIL.CONFIRMATION");
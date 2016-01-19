<?php if ($this->address instanceof \Pimcore\Model\Object\Fieldcollection\Data\CoreShopUserAddress) { ?>
    <strong><?=$this->address->getFirstname()?> <?=$this->address->getLastname() ?></strong><br/>

    <?=$this->address->getStreet()?> <?=$this->address->getNr() ?><br/>
    <?=$this->address->getZip()?> <?=$this->address->getCity()?><br/>
    <?=$this->address->getCountry() ?>
<?php } ?>
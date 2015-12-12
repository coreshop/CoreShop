<div class="address">
    <div class="name"><?=$this->address->getFirstname()?> <?=$this->address->getLastname() ?></div>
    <div class="street"><?=$this->address->getStreet()?> <?=$this->address->getNr() ?></div>
    <div class="city"><?=$this->address->getZip()?> <?=$this->address->getCity()?></div>
    <div class="country"><?=$this->translateCountry($this->address->getCountry()) ?></div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?php if ($this->billingAndShippingEqual) { ?>
            <div class="row">
                <div class="col-xs-12">
                    <strong><?=$this->translate("Shipping & Billing Address")?></strong>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?=$this->order->getCustomer()->getFirstname()?> <?=$this->order->getCustomer()->getLastname()?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?=$this->billingAddress->getStreet(); ?> <?=$this->billingAddress->getNr(); ?><br/>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <?=$this->billingAddress->getZip(); ?> <?=$this->billingAddress->getCity(); ?><br/>
                </div>
                <div class="col-xs-6 text-right">
                    <?=$this->order->getOrderDate()->format("d.m.Y")?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <?=$this->billingAddress->getCountry()->getName(); ?>
                </div>
                <div class="col-xs-6 text-right">
                    <?=$this->translate("Invoice")?> #<?=$this->order->getOrderNumber()?>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-xs-4">
                    <strong><?=$this->translate("Shipping")?></strong>
                </div>
                <div class="col-xs-4">
                    <strong><?=$this->translate("Billing")?></strong>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <?=$this->shippingAddress->getFirstname()?> <?=$this->shippingAddress->getLastname()?>
                </div>
                <div class="col-xs-4">
                    <?=$this->billingAddress->getFirstname()?> <?=$this->billingAddress->getLastname()?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <?=$this->shippingAddress->getStreet(); ?> <?=$this->shippingAddress->getNr(); ?><br/>
                </div>
                <div class="col-xs-4">
                    <?=$this->billingAddress->getStreet(); ?> <?=$this->billingAddress->getNr(); ?><br/>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <?=$this->shippingAddress->getZip(); ?> <?=$this->shippingAddress->getCity(); ?><br/>
                </div>
                <div class="col-xs-4">
                    <?=$this->billingAddress->getZip(); ?> <?=$this->billingAddress->getCity(); ?><br/>
                </div>
                <div class="col-xs-4 text-right">
                    <?=$this->order->getOrderDate()->format("d.m.Y")?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <?=$this->shippingAddress->getCountry()->getName(); ?>
                </div>
                <div class="col-xs-4">
                    <?=$this->billingAddress->getCountry()->getName(); ?>
                </div>
                <div class="col-xs-4 text-right">
                    <?=$this->translate("Invoice")?> #<?=$this->order->getOrderNumber()?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
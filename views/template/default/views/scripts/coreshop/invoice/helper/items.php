<div class="row">
    <div class="col-xs-12">
        <div class="invoice-table">
            <div class="row">
                <div class="col-xs-12">
                    <div class="row invoice-header">
                        <div class="col-xs-6">
                            <?=$this->translate("Product Details")?>
                        </div>
                        <div class="col-xs-2 text-right">
                            <?=$this->translate("Quantity")?>
                        </div>
                        <div class="col-xs-2 text-right">
                            <?=$this->translate("Price")?>
                        </div>
                        <div class="col-xs-2 text-right">
                            <?=$this->translate("Total")?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <?php foreach($this->order->getItems() as $item) { ?>
                        <div class="row invoice-item invoice-item-<?=$item->getId()?>">
                            <div class="col-xs-6">
                                <?=$item->getProduct()->getName()?> <?php if($item->getIsGiftItem()) { ?> <br/><span><?=$this->translate("Gift Item")?></span> <?php } ?>
                            </div>
                            <div class="text-right col-xs-2">
                                <span><?=$item->getAmount()?></span>
                            </div>
                            <div class="text-right invoice-item-price col-xs-2">
                                <?=\CoreShop\Tool::formatPrice($item->getPrice())?>
                            </div>
                            <div class="text-right invoice-item-total-price col-xs-2">
                                <?=\CoreShop\Tool::formatPrice($item->getAmount() * $item->getPrice())?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if($this->order->getPriceRule() instanceof \CoreShop\Model\PriceRule) { ?>
                        <div class="row">
                            <div class="col-xs-6">
                                <?=$this->order->getPriceRule()->getName()?>
                            </div>
                            <div class="col-xs-2">
                            </div>
                            <div class="text-right col-xs-2">
                                -<?=\CoreShop\Tool::formatPrice($this->order->getPriceRule()->getDiscount())?>
                            </div>
                            <div class="text-right col-xs-2">
                                -<?=\CoreShop\Tool::formatPrice($this->order->getPriceRule()->getDiscount())?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xs-12">
                    <?php
                    $shipping = $this->order->getShipping();
                    $discount = $this->order->getDiscount();
                    $payment = $this->order->getPaymentFee();

                    $rowspan = 5;

                    if($shipping == 0)
                        $rowspan--;

                    if($discount == 0)
                        $rowspan--;

                    if($payment == 0)
                        $rowspan--;
                    ?>
                    <div class="row invoice-summary">
                        <div class="col-xs-8">&nbsp;</div>
                        <div class="col-xs-2 text-right">
                            <strong><?=$this->translate("Subtotal")?>:</strong>
                        </div>
                        <div class="col-xs-2 text-right invoice-subtotal">
                            <?=\CoreShop\Tool::formatPrice($this->order->getSubtotal())?>
                        </div>
                    </div>
                    <?php if($shipping > 0) { ?>
                        <div class="row invoice-summary">
                            <div class="col-xs-8">&nbsp;</div>
                            <div class="col-xs-2 text-right">
                                <strong><?=$this->translate("Shipping")?>:</strong>
                            </div>
                            <div class="col-xs-2 text-right invoice-shipping">
                                <?=\CoreShop\Tool::formatPrice($shipping)?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if($payment > 0) { ?>
                        <div class="row invoice-summary">
                            <div class="col-xs-8"></div>
                            <div class="col-xs-2 text-right">
                                <strong><?=$this->translate("Payment Fee")?>:</strong>
                            </div>
                            <div class="col-xs-2 text-right invoice-discount">
                                <?=\CoreShop\Tool::formatPrice($payment)?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row invoice-summary">
                        <div class="col-xs-8">&nbsp;</div>
                        <div class="col-xs-2 text-right invoice-tax">
                            <strong><?=$this->translate("Tax (incl.)")?>:</strong>
                        </div>
                        <div class="col-xs-2 text-right invoice-tax">
                            <?=\CoreShop\Tool::formatPrice($this->order->getTotalTax())?>
                        </div>
                    </div>
                    <?php if($discount > 0) { ?>
                        <div class="row invoice-summary">
                            <div class="col-xs-8"></div>
                            <div class="col-xs-2 text-right">
                                <strong><?=$this->translate("Discount")?>:</strong>
                            </div>
                            <div class="col-xs-2 text-right invoice-discount">
                                -<?=\CoreShop\Tool::formatPrice($discount)?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row invoice-summary">
                        <div class="col-xs-8">&nbsp;</div>
                        <div class="col-xs-2 text-right invoice-sub-total">
                            <strong><?=$this->translate("Total")?>:</strong>
                        </div>
                        <div class="col-xs-2 text-right invoice-total">
                            <?=\CoreShop\Tool::formatPrice($this->order->getTotal())?>
                        </div>
                    </div>
                    <?php if($this->order->getPayedTotal() > 0) { ?>
                    <div class="row invoice-summary">
                        <div class="col-xs-8"></div>
                        <div class="col-xs-2 text-right invoice-payed">
                            <strong><?=$this->translate("Payed")?>:</strong>
                        </div>
                        <div class="col-xs-2 text-right invoice-payed">
                            <?=\CoreShop\Tool::formatPrice($this->order->getPayedTotal())?>
                        </div>
                    </div>
                    <?php }?>

                    <?php
                        $amountOpen = $this->order->getTotal() - $this->order->getPayedTotal();
                    ?>
                    <?php if($amountOpen > 0) { ?>
                    <div class="row invoice-summary">
                        <div class="col-xs-8">&nbsp;</div>
                        <div class="col-xs-2 text-right invoice-payment-open text-danger">
                            <strong><?=$this->translate("Open")?>:</strong>
                        </div>
                        <div class="col-xs-2 text-right invoice-payment-open text-danger">
                            <?=\CoreShop\Tool::formatPrice($amountOpen)?>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
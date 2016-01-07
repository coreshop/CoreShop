<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive invoice-table">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td class="text-center">
                        <?=$this->translate("Image")?>
                    </td>
                    <td class="text-center">
                        <?=$this->translate("Product Details")?>
                    </td>
                    <td class="text-center">
                        <?=$this->translate("Quantity")?>
                    </td>
                    <td class="text-center">
                        <?=$this->translate("Price")?>
                    </td>
                    <td class="text-center">
                        <?=$this->translate("Total")?>
                    </td>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($this->order->getItems() as $item) { ?>
                        <tr class="invoice-item invoice-item-<?=$item->getId()?>">
                            <td class="text-center">
                                <?php if($item->getProduct()->getImage() instanceof Pimcore\Model\Asset\Image) { ?>
                                    <?php
                                    echo $item->getProduct()->getImage()->getThumbnail("coreshop_productInvoice")->getHtml(array("class" => "img-responsive", "alt" => $item->getProduct()->getName(), "title" => $item->getProduct()->getName()));
                                    ?>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?=$item->getProduct()->getName()?> <?php if($item->getIsGiftItem()) { ?> <br/><span><?=$this->translate("Gift Item")?></span> <?php } ?>
                            </td>
                            <td class="text-center">
                                <span><?=$item->getAmount()?></span>
                            </td>
                            <td class="text-right invoice-item-price">
                                <?=\CoreShop\Tool::formatPrice($item->getPrice())?>
                            </td>
                            <td class="text-right invoice-item-total-price">
                                <?=\CoreShop\Tool::formatPrice($item->getAmount() * $item->getPrice())?>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if($this->order->getPriceRule() instanceof \CoreShop\Model\PriceRule) { ?>
                        <tr>
                            <td colspan="2" class="text-center">
                                <?=$this->order->getPriceRule()->getName()?>
                            </td>
                            <td class="text-center">
                            </td>
                            <td class="text-right">
                                -<?=\CoreShop\Tool::formatPrice($this->order->getPriceRule()->getDiscount())?>
                            </td>
                            <td class="text-right">
                                -<?=\CoreShop\Tool::formatPrice($this->order->getPriceRule()->getDiscount())?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <?php
                    $shipping = $this->order->getShipping();
                    $discount = $this->order->getDiscount();

                    $rowspan = 4;

                    if($shipping == 0)
                        $rowspan--;

                    if($discount == 0)
                        $rowspan--;
                    ?>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right">
                            <strong><?=$this->translate("Subtotal")?>:</strong>
                        </td>
                        <td colspan="1" class="text-right invoice-subtotal">
                            <?=\CoreShop\Tool::formatPrice($this->order->getSubtotal())?>
                        </td>
                    </tr>
                    <?php if($shipping > 0) { ?>
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right">
                                <strong><?=$this->translate("Shipping")?>:</strong>
                            </td>
                            <td colspan="1" class="text-right invoice-shipping">
                                <?=\CoreShop\Tool::formatPrice($shipping)?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($discount > 0) { ?>
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right">
                                <strong><?=$this->translate("Discount")?>:</strong>
                            </td>
                            <td colspan="1" class="text-right invoice-discount">
                                -<?=\CoreShop\Tool::formatPrice($discount)?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right invoice-sub-total">
                            <strong><?=$this->translate("Total")?>:</strong>
                        </td>
                        <td colspan="1" class="text-right invoice-total">
                            <?=\CoreShop\Tool::formatPrice($this->order->getTotal())?>
                        </td>
                    </tr>
                    <?php if($this->order->getPayedTotal() > 0) { ?>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right invoice-payed">
                            <strong><?=$this->translate("Payed")?>:</strong>
                        </td>
                        <td colspan="1" class="text-right invoice-payed">
                            <?=\CoreShop\Tool::formatPrice($this->order->getPayedTotal())?>
                        </td>
                    </tr>
                    <?php }?>

                    <?php
                        $amountOpen = $this->order->getTotal() - $this->order->getPayedTotal();
                    ?>
                    <?php if($amountOpen > 0) { ?>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right invoice-payment-open text-danger">
                            <strong><?=$this->translate("Open")?>:</strong>
                        </td>
                        <td colspan="1" class="text-right invoice-payment-open text-danger">
                            <?=\CoreShop\Tool::formatPrice($amountOpen)?>
                        </td>
                    </tr>
                    <?php }?>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div id="cart" class="btn-group btn-block">
    <button type="button" data-toggle="dropdown" class="btn btn-block btn-lg dropdown-toggle">
        <i class="fa fa-shopping-cart"></i>
        <span id="cart-overview-total"><span class="cart-badge"><?=count($this->cart->getItems()) ?></span> <?=$this->translate("item(s)") ?> - <span class="cart-total"><?=\CoreShop\Tool::formatPrice($this->cart->getTotal())?></span></span>
        <i class="fa fa-caret-down"></i>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <table class="table hcart cart-items">
                <?php foreach($this->cart->getItems() as $item) { ?>
                    <?php
                    $product = $item->getProduct();
                    ?>
                    <tr>
                        <td class="text-center">
                            <a href="<?=$this->url(array("lang" => $this->language, "name" => $product->getName(), "product" => $product->getId()), "coreshop_detail", false, false)?>">
                                <?php if($product->getImage() instanceof \Pimcore\Model\Asset\Image) {
                                    echo $product->getImage()->getThumbnail("coreshop_productCartPreview")->getHtml(array("class" => "img-thumbnail img-responsive", "title" => $product->getName(), "alt" => $product->getName()));
                                } ?>
                            </a>
                        </td>
                        <td class="text-left">
                            <a href="<?=$this->url(array("lang" => $this->language, "name" => $product->getName(), "product" => $product->getId()), "coreshop_detail", false, false)?>">
                                <?=$product->getName()?>
                            </a>
                        </td>
                        <td class="text-right">x <?=$item->getAmount()?></td>
                        <td class="text-right"><?=\CoreShop\Tool::formatPrice($item->getTotal())?></td>
                        <td class="text-center">
                            <?php if(!$item->getIsGiftItem()) { ?>
                                <a href="#" class="removeFromCart" data-id="<?=$item->getId()?>" data-refresh="true">
                                    <i class="fa fa-times"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </li>
        <li>
            <table class="table table-bordered total">
                <tbody>
                <tr>
                    <td class="text-right"><strong><?=$this->translate("Subtotal")?></strong></td>
                    <td class="text-left cart-subtotal"><?=\CoreShop\Tool::formatPrice($this->cart->getSubtotal())?></td>
                </tr>
                <tr>
                    <td class="text-right"><strong><?=$this->translate("Total")?></strong></td>
                    <td class="text-left cart-total"><?=\CoreShop\Tool::formatPrice($this->cart->getTotal())?></td>
                </tr>
                </tbody>
            </table>
            <p class="text-right btn-block1">
                <a href="<?=$this->url(array("lang" => $this->language, "action" => "list"), "coreshop_cart", false, false)?>">
                    <?=$this->translate("View Cart")?>
                </a>
                <a href="<?=$this->url(array("lang" => $this->language, "action" => "index"), "coreshop_checkout", false, false)?>">
                    <?=$this->translate("Checkout")?>
                </a>
            </p>
        </li>
    </ul>
</div>
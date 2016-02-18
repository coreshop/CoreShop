<div id="cart" class="btn-group btn-block">
    <button type="button" data-toggle="dropdown" class="btn btn-block btn-lg dropdown-toggle">
        <i class="fa fa-shopping-cart"></i>
        <span id="cart-overview-total"><span class="cart-badge"><?=count($this->cart->getItems()) ?></span> <?=$this->translate("item(s)") ?> - <span class="cart-total"><?=\CoreShop\Tool::formatPrice($this->cart->getTotal())?></span></span>
        <i class="fa fa-caret-down"></i>
    </button>
    <?php echo $this->partial("coreshop/cart/helper/minicart.php", array("cart" => $this->cart, "language" => $this->language) ); ?>
</div>
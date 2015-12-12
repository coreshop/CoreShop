<?php
    $href = $this->url(array("name" => $this->product->getName(), "product" => $this->product->getId(), "lang" => $this->language), "coreshop_detail");
    $uniqid = uniqid() . "-product-image-" . $this->product->getId();
?>
<div class="product-col">
    <div class="image">
        <?php if($this->product->getImage() instanceof Asset_Image) { ?>
            <?php if($this->product->getIsNew()) { ?>
                <div class="image-new-badge"></div>
            <?php } ?>

            <a href="<?=$href?>"><img id="<?=$uniqid ?>" src="<?=$this->product->getImage()->getThumbnail("coreshop_productList")?>" class="img-responsive" /></a>
            <hr/>
        <?php } ?>
    </div>
    <div class="caption">
        <h4><a href="<?=$href?>"><?=$this->product->getName()?></a></h4>
        <div class="description">
            <?=$this->product->getShortDescription()?>
        </div>
        <?php if($this->product->getAvailableForOrder()) { ?>
        <div class="price">
            <span class="price-new"><?=\CoreShop\Tool::formatPrice($this->product->getProductPrice())?></span>
        </div>
        <div class="cart-button">
            <button type="button" class="btn btn-cart" data-id="<?=$this->product->getId()?>" data-img="<?=$uniqid ?>">
                <?=$this->translate("Add to cart")?>
                <i class="fa fa-shopping-cart"></i>
            </button>
        </div>
        <?php } ?>
    </div>
</div>
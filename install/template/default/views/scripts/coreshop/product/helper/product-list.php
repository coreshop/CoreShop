<?php
    $uniqid = uniqid() . "-product-image-" . $this->product->getId();
?>
<div class="col-xs-12">
    <div class="product-col list clearfix">
        <div class="image">
            <?php if($this->product->getImage() instanceof \Pimcore\Model\Asset\Image) { ?>
                <?php if($this->product->getIsNew()) { ?>
                    <div class="image-new-badge"></div>
                <?php } ?>

                <img src="<?=$this->product->getImage()->getThumbnail("coreshop_productList")?>" alt="<?=$this->product->getName()?>" id="<?=$uniqid?>" class="img-responsive" />
            <?php } ?>
        </div>
        <div class="caption">
            <h4><a href="<?=$this->url(array("lang" => $this->language, "name" => $this->product->getName(), "product" => $this->product->getId()), "coreshop_detail")?>"><?=$this->product->getName()?></a></h4>
            <div class="description">
                <?=$this->product->getShortDescription()?>
            </div>
            <?php if($this->product->getAvailableForOrder()) { ?>
                <div class="price">
                    <span class="price-new"><?=\CoreShop\Tool::formatPrice($this->product->getProductPrice())?></span>
                </div>
                <div class="cart-button button-group">
                    <button type="button" title="" class="btn btn-wishlist" data-original-title="Wishlist">
                        <i class="fa fa-heart"></i>
                    </button>
                    <button type="button" title="" class="btn btn-compare" data-original-title="Compare">
                        <i class="fa fa-bar-chart-o"></i>
                    </button>
                    <button type="button" class="btn btn-cart" data-id="<?=$this->product->getId()?>" data-img="#<?=$uniqid?>">
                        Add to cart
                        <i class="fa fa-shopping-cart"></i>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
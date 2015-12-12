<div class="col-md-4 col-sm-6">
    <div class="product-col">
        <div class="image">
            <?php if($this->product->getImage() instanceof \Pimcore\Model\Asset\Image) { ?>
                <?php if($this->product->getIsNew()) { ?>
                    <div class="image-new-badge"></div>
                <?php } ?>

                <img src="<?=$this->product->getImage()->getThumbnail("coreshop_productGrid")?>" alt="<?=$this->product->getName()?>" id="product-image-<?=$this->product->getId()?>" class="img-responsive" />
            <?php } ?>
        </div>
        <div class="caption">
            <h4><a href="<?=$this->url(array("lang" => $this->language, "name" => $this->product->getName(), "product" => $this->product->getId()), "coreshop_detail")?>"><?=$this->product->getName()?></a></h4>
            <div class="description">
                <?=$this->product->getShortDescription();?>
            </div>
            <?php if($this->product->getAvailableForOrder()) { ?>
                <div class="price">
                    <span class="price-new"><?=\CoreShop\Tool::formatPrice($this->product->getProductPrice())?></span>
                    <!--<span class="price-old">$249.50</span>-->
                </div>
                <div class="cart-button button-group">
                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                        <i class="fa fa-heart"></i>
                    </button>
                    <button type="button" title="Compare" class="btn btn-compare">
                        <i class="fa fa-bar-chart-o"></i>
                    </button>
                    <button type="button" class="btn btn-cart" data-id="<?=$this->product->getId()?>" data-img="#product-image-<?=$this->product->getId()?>">
                        Add to cart
                        <i class="fa fa-shopping-cart"></i>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
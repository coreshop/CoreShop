<div id="main-container" class="container">
    <div class="row">

    <?=$this->template("coreshop/helper/left.php")?>

        <div class="col-md-9">

            <ol class="breadcrumb">
                <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
                <?php if(count($this->product->getCategories()) > 0) { ?>
                    <?php foreach($this->product->getCategories()[0]->getHierarchy() as $cat) { ?>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "name" => $cat->getName(), "category" => $cat->getId()), "coreshop_list", true)?>"><?=$cat->getName()?></a></li>
                    <?php } ?>
                <?php } ?>
                <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "name" => $this->product->getName(), "product" => $this->product->getId()), "coreshop_detail", true)?>"><?=$this->product->getName()?></a></li>
            </ol>


            <div class="row product-info">

                <div class="col-sm-5 images-block">
                    <?php if($this->product->getImage() instanceof \Pimcore\Model\Asset\Image) { ?>
                        <?php if($this->product->getIsNew()) { ?>
                            <div class="image-new-badge"></div>
                        <?php } ?>

                        <img src="<?=$this->product->getImage()->getThumbnail("coreshop_productDetail")?>?>" alt="<?=$this->product->getName()?>" id="product-image-<?=$this->product->getId()?>" class="img-responsive thumbnail" />
                    <?php } ?>
                    <?php if(count($this->product->getImages()) > 0) { ?>
                    <ul class="list-unstyled list-inline">
                        <?php foreach($this->product->getImages() as $image) { ?>
                        <li>
                            <?php
                                echo $image->getThumbnail("coreshop_productDetailThumbnail")->getHtml(array("class" => "img-responsive thumbnail", "alt" => $this->product->getName()));
                            ?>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </div>

                <div class="col-sm-7 product-details">

                    <h2><?=$this->product->getName()?></h2>
                    <hr />

                    <?php if(strlen($this->product->getShortDescription()) > 0) { ?>
                        <div class="description">
                            <?=$this->product->getShortDescription()?>
                        </div>
                        <hr />
                    <?php } ?>

                    <?php if($this->product->getAvailableForOrder()) { ?>
                        <div class="price">
                            <span class="price-head"><?=$this->translate("Price")?> :</span>
                            <span class="price-new"><?=\CoreShop\Tool::formatPrice($this->product->getPrice());?></span>
                        </div>
                        <hr />

                        <div class="options">
                            <?php if(!\CoreShop\Config::isCatalogMode()) { ?>
                            <div class="form-group">
                                <label class="control-label text-uppercase" for="input-quantity"><?=$this->translate("Qty")?>:</label>
                                <input type="text" name="quantity" value="1" size="2" id="input-quantity" class="form-control" />
                            </div>
                            <?php } ?>

                            <div class="cart-button button-group">
                                <button type="button" title="Wishlist" class="btn btn-wishlist">
                                    <i class="fa fa-heart"></i>
                                </button>
                                <button type="button" title="Compare" class="btn btn-compare">
                                    <i class="fa fa-bar-chart-o"></i>
                                </button>

                                <?php if(!\CoreShop\Config::isCatalogMode()) { ?>
                                    <button type="button" class="btn btn-cart" data-id="<?=$this->product->getId()?>" data-img="#product-image-<?=$this->product->getId()?>">
                                        <?=$this->translate("Add to cart")?>
                                        <i class="fa fa-shopping-cart"></i>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <hr />
                </div>
            </div>

            <?php if(strlen($this->product->getDescription()) > 0) {?>
                <div class="product-info-box">
                    <h4 class="heading"><?=$this->translate("Description")?></h4>
                    <div class="content panel-smart">
                        <?=$this->product->getDescription()?>
                    </div>
                </div>
            <?php } ?>

            <?=\CoreShop\Plugin::hook("product-detail-bottom", array("product" => $this->product))?>
        
        
        </div>
    </div>
</div>
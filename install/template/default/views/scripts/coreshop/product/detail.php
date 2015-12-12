<!-- Main Container Starts -->
<div id="main-container" class="container">
    <div class="row">

    <?=$this->template("coreshop/helper/left.php")?>
    <!-- Primary Content Starts -->
        <div class="col-md-9">
        <!-- Breadcrumb Starts -->
            <ol class="breadcrumb">
                <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
                <?php if(count($this->product->getCategories()) > 0) { ?>
                    <?php foreach($this->product->getCategories()[0]->getHierarchy() as $cat) { ?>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "name" => $cat->getName(), "category" => $cat->getId()), "coreshop_list", true)?>"><?=$cat->getName()?></a></li>
                    <?php } ?>
                <?php } ?>
                <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "name" => $this->product->getName(), "product" => $this->product->getId()), "coreshop_detail", true)?>"><?=$this->product->getName()?></a></li>
            </ol>
        <!-- Breadcrumb Ends -->
        <!-- Product Info Starts -->
            <div class="row product-info">
            <!-- Left Starts -->
                
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
                            <img src="<?=$image->getThumbnail("coreshop_productDetailThumbnail")?>?>" alt="<?=$this->product->getName()?>" class="img-responsive thumbnail" />
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </div>
            <!-- Left Ends -->
            <!-- Right Starts -->
                <div class="col-sm-7 product-details">
                <!-- Product Name Starts -->
                    <h2><?=$this->product->getName()?></h2>
                <!-- Product Name Ends -->
                    <hr />
                <!-- Manufacturer Starts -->
                    <!--<ul class="list-unstyled manufacturer">
                        <li>
                            <span>Brand:</span> Indian spices
                        </li>
                        <li><span>Reward Points:</span> 300</li>
                        <li>
                            <span>Availability:</span> <strong class="label label-success">In Stock</strong>
                        </li>
                    </ul>-->
                    <div class="description">
                        <?=$this->product->getShortDescription()?>
                    </div>
                <!-- Manufacturer Ends -->

                <!-- Price Starts -->
                    <?php if($this->product->getAvailableForOrder()) { ?>
                        <hr />
                        <div class="price">
                            <span class="price-head"><?=$this->translate("Price")?> :</span>
                            <span class="price-new"><?=\CoreShop\Tool::formatPrice($this->product->getProductPrice());?></span>
                        </div>

                <!-- Price Ends -->
                    <hr />
                <!-- Available Options Starts -->
                    <div class="options">
                        <div class="form-group">
                            <label class="control-label text-uppercase" for="input-quantity"><?=$this->translate("Qty")?>:</label>
                            <input type="text" name="quantity" value="1" size="2" id="input-quantity" class="form-control" />
                        </div>
                        <div class="cart-button button-group">
                            <button type="button" title="Wishlist" class="btn btn-wishlist">
                                <i class="fa fa-heart"></i>
                            </button>
                            <button type="button" title="Compare" class="btn btn-compare">
                                <i class="fa fa-bar-chart-o"></i>
                            </button>
                            <button type="button" class="btn btn-cart" data-id="<?=$this->product->getId()?>" data-img="#product-image-<?=$this->product->getId()?>">
                                <?=$this->translate("Add to cart")?>
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <?php } ?>
                <!-- Available Options Ends -->
                    <hr />
                </div>
            <!-- Right Ends -->
            </div>
        <!-- product Info Ends -->
            <?php if(strlen($this->product->getDescription()) > 0) {?>
        <!-- Product Description Starts -->
            <div class="product-info-box">
                <h4 class="heading"><?=$this->translate("Description")?></h4>
                <div class="content panel-smart">
                    <?=$this->product->getDescription()?>
                </div>
            </div>
        <!-- Product Description Ends -->
            <?php } ?>

            <?=\CoreShop\Plugin::hook("product-detail-bottom", array("product" => $this->product))?>
        
        
        </div>
    <!-- Primary Content Ends -->
    </div>
</div>
<!-- Main Container Ends -->
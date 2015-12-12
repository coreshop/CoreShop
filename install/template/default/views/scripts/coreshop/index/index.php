<!-- Main Container Starts -->
<div id="main-container-home" class="container">
    <div class="row">
        <?=$this->template("coreshop/helper/left.php")?>
        <!-- Primary Content Starts -->
        <div class="col-md-9">
            <!-- Slider Section Starts -->
            <div class="slider">
                <div id="main-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Wrapper For Slides Starts -->
                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="/static/images/slider-imgs/slide1-img.jpg" alt="Slider" class="img-responsive" />
                        </div>
                        <div class="item">
                            <img src="/static/images/slider-imgs/slide2-img.jpg" alt="Slider" class="img-responsive" />
                        </div>
                    </div>
                    <!-- Wrapper For Slides Ends -->
                    <!-- Controls Starts -->
                    <a class="left carousel-control" href="#main-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#main-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                    <!-- Controls Ends -->
                </div>
            </div>
            <!-- Slider Section Ends -->
            <!-- 2 Column Banners Starts -->
            <div class="col2-banners">
                <ul class="row list-unstyled">
                    <li class="col-sm-8">
                        <img src="/static/images/banners/2col-banner1.jpg" alt="banners" class="img-responsive" />
                    </li>
                    <li class="col-sm-4">
                        <img src="/static/images/banners/2col-banner2.jpg" alt="banners" class="img-responsive" />
                    </li>
                </ul>
            </div>
            <!-- 2 Column Banners Ends -->

            <?=$this->template("coreshop/product/helper/latest-products.php");?>

            <!-- Specials Products Starts -->
            <section class="products-list">
                <!-- Heading Starts -->
                <h2 class="product-head"><?=$this->translate("Specials Products")?></h2>
                <!-- Heading Ends -->
                <!-- Products Row Starts -->
                <div class="row">
                    <!-- Product #1 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-1")?>
                    </div>
                    <!-- Product #1 Ends -->
                    <!-- Product #2 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-2")?>
                    </div>
                    <!-- Product #2 Ends -->
                    <!-- Product #3 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-3")?>
                    </div>
                    <!-- Product #3 Ends -->
                </div>
                <!-- Products Row Ends -->
            </section>
            <!-- Specials Products Ends -->
        </div>
        <!-- Primary Content Ends -->
    </div>
</div>
<!-- Main Container Ends -->
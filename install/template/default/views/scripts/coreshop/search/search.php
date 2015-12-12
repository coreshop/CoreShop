<div id="main-container" class="container">
    <div class="row">
        <?=$this->template("coreshop/helper/left.php"); ?>
        <!-- Primary Content Starts -->
        <div class="col-md-9">
            <!-- Breadcrumb Starts -->
            <ol class="breadcrumb">
                <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
                <li class="active"><?=$this->translate("Search")?></li>
            </ol>
            <!-- Breadcrumb Ends -->
            <!-- Main Heading Starts -->
            <h2 class="main-heading2">
                <?=$this->translate("Search Results for") . ' "' . $this->searchText . '"'?>
            </h2>

            <div class="row">
                <?php
                foreach($this->paginator as $product) {
                    echo $this->template("coreshop/product/helper/product-list.php", array("product" => $product));
                }
                ?>
            </div>
            <!-- Product List Display Ends -->
            <?= $this->paginationControl($paginator, 'Sliding', 'coreshop/helper/paging.php', array(
                'appendQueryString' => true
            )); ?>
        </div>
        <!-- Primary Content Ends -->
    </div>
</div>
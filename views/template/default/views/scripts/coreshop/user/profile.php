<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "profile"), "coreshop_user")?>"><?=$this->translate("My Profile")?></a></li>
    </ol>
    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("My Account")?>
    </h2>
    <!-- Main Heading Ends -->

    <ul class="list list-unstyled myaccount-link-list">
        <li>
            <a href="<?=$this->url(array("lang" => $this->language, "action" => "orders"), "coreshop_user")?>" title="<?=$this->translate("Orders")?>">
                <i class="fa fa-list-ol"></i>
                <span><?=$this->translate("Order history and details")?></span>
            </a>
        </li>
        <li>
            <a href="<?=$this->url(array("lang" => $this->language, "action" => "addresses"), "coreshop_user")?>" title="<?=$this->translate("Addresses")?>">
                <i class="fa fa-building"></i>
                <span><?=$this->translate("My addresses")?></span>
            </a>
        </li>
        <li>
            <a href="<?=$this->url(array("lang" => $this->language, "action" => "settings"), "coreshop_user")?>" title="<?=$this->translate("Information")?>">
                <i class="fa fa-user"></i>
                <span><?=$this->translate("My personal information")?></span>
            </a>
        </li>
    </ul>
</div>
<div class="footer-links">
    <!-- Container Starts -->
    <div class="container">
        <!-- Information Links Starts -->
        <div class="col-md-2 col-sm-6">
            <h5><?=$this->input("head")?></h5>
            <ul>
                <li><?=$this->link("link1")?></li>
                <li><?=$this->link("link2")?></li>
                <li><?=$this->link("link3")?></li>
                <li><?=$this->link("link4")?></li>
            </ul>
        </div>
        <!-- Information Links Ends -->
        <!-- My Account Links Starts -->
        <?php if(!\CoreShop\Config::isCatalogMode()) { ?>
            <div class="col-md-2 col-sm-6">
                <h5><?=$this->translate("My Account")?></h5>
                <ul>
                    <?php if($this->session->user instanceof \CoreShop\Model\User) { ?>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "profile"), "coreshop_user")?>"><?=$this->translate("My Account")?></a></li>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "orders"), "coreshop_user")?>"><?=$this->translate("My orders")?></a></li>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "addresses"), "coreshop_user")?>"><?=$this->translate("My addresses")?></a></li>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "settings"), "coreshop_user")?>"><?=$this->translate("My personal info")?></a></li>
                    <?php } else { ?>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "register"), "coreshop_user")?>"><?=$this->translate("Register")?></a></li>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "login"), "coreshop_user")?>"><?=$this->translate("Login")?></a></li>
                        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "list"), "coreshop_compare")?>"><?=$this->translate("Product Compare")?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <!-- My Account Links Ends -->
        <!-- Customer Service Links Starts -->
        <div class="col-md-2 col-sm-6">
            <h5><?=$this->translate("Service")?></h5>
            <ul>
                <li><?=$this->link("link5")?></li>
            </ul>
        </div>
        <!-- Customer Service Links Ends -->
        <!-- Follow Us Links Starts -->
        <div class="col-md-2 col-sm-6">
            <h5>Follow Us</h5>
            <ul>
                <li><a href="https://www.facebook.com/dominik.pfaffenbauer" target="_blank">Facebook</a></li>
                <li><a href="https://www.linkedin.com/profile/view?id=341821585" target="_blank">LinkedIn</a></li>
            </ul>
        </div>
        <!-- Follow Us Links Ends -->
        <!-- Contact Us Starts -->
        <div class="col-md-4 col-sm-12 last">
            <h5><?=$this->translate("Contact Us")?></h5>
            <ul>
                <li><?=$this->input("companyName")?></li>
                <li>
                    <?=$this->input("companyAddress")?>
                </li>
                <li>
                    <?=$this->translate("Email:")?> <?=$this->link("companyMail")?>
                </li>
            </ul>
            <h4 class="lead">
                <?=$this->translate("Tel")?>: <span><?=$this->input("companyPhone")?></span>
            </h4>
        </div>
        <!-- Contact Us Ends -->
    </div>
    <!-- Container Ends -->
</div>
<!-- Footer Links Ends -->
<!-- Copyright Area Starts -->
<div class="copyright">
    <!-- Container Starts -->
    <div class="container">
        <!-- Starts -->
        <p class="pull-left">
            &copy; 2015 lineofcode Dominik Pfaffenbauer. Designed By <a href="http://sainathchillapuram.com">Sainath Chillapuram</a>
        </p>
        <!-- Ends -->
        <!-- Payment Gateway Links Starts -->
        <ul class="pull-right list-inline">
            <li>
                <img src="<?=CORESHOP_TEMPLATE_RESOURCES?>images/payment-icon/cirrus.png" alt="PaymentGateway" />
            </li>
            <li>
                <img src="<?=CORESHOP_TEMPLATE_RESOURCES?>images/payment-icon/paypal.png" alt="PaymentGateway" />
            </li>
            <li>
                <img src="<?=CORESHOP_TEMPLATE_RESOURCES?>images/payment-icon/visa.png" alt="PaymentGateway" />
            </li>
            <li>
                <img src="<?=CORESHOP_TEMPLATE_RESOURCES?>images/payment-icon/mastercard.png" alt="PaymentGateway" />
            </li>
            <li>
                <img src="<?=CORESHOP_TEMPLATE_RESOURCES?>images/payment-icon/americanexpress.png" alt="PaymentGateway" />
            </li>
        </ul>
        <!-- Payment Gateway Links Ends -->
    </div>
    <!-- Container Ends -->
</div>
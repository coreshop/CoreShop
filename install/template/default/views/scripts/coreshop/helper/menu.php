<!-- Main Menu Starts -->
<nav id="main-menu" class="navbar" role="navigation">
    <div class="container">
        <!-- Nav Header Starts -->
        <div class="navbar-header">
            <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-cat-collapse">
                <span class="sr-only"><?=$this->translate("Toggle Navigation")?></span>
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <!-- Nav Header Ends -->
        <!-- Navbar Cat collapse Starts -->
        <div class="collapse navbar-collapse navbar-cat-collapse">
            <ul class="nav navbar-nav">
                <?php
                    $categories = \CoreShop\Model\Category::getFirstLevel();

                    foreach($categories as $cat) {
                        $dropdown = count($cat->getChildCategories()) > 0;
                ?>
                   <li class="<?=$dropdown ? "dropdown" : ""?>">
                       <a href="<?=$this->url(array("lang" => $this->language, "name" => $cat->getName(), "category" => $cat->getId()), "coreshop_list", true)?>" <?=$dropdown ? 'class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10"' : '' ?>>
                           <?=$cat->getName()?>
                       </a>

                       <?php if(count($cat->getChildCategories()) > 0) { ?>
                       <ul class="dropdown-menu" role="menu">
                           <?php foreach($cat->getChildCategories() as $child) { ?>
                               <li><a tabindex="-1" href="<?=$this->url(array("lang" => $this->language, "name" => $child->getName(), "category" => $child->getId()), "coreshop_list", true)?>"><?=$child->getName()?></a></li>
                           <?php }?>
                           </ul>
                       <?php } ?>
                   </li>
                <?php } ?>
                <?php /*
                <li class="dropdown">
                    <a href="category-list.html" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10">Televisions </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-inner">
                            <ul class="list-unstyled">
                                <li class="dropdown-header">Sub Category</li>
                                <li><a tabindex="-1" href="#">item 1</a></li>
                                <li><a tabindex="-1" href="#">item 2</a></li>
                                <li><a tabindex="-1" href="#">item 3</a></li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="dropdown-header">Sub Category</li>
                                <li><a tabindex="-1" href="#">item 1</a></li>
                                <li><a tabindex="-1" href="#">item 2</a></li>
                                <li><a tabindex="-1" href="#">item 3</a></li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="dropdown-header">Sub Category</li>
                                <li><a tabindex="-1" href="#">item 1</a></li>
                                <li><a tabindex="-1" href="#">item 2</a></li>
                                <li><a tabindex="-1" href="#">item 3</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
                */ ?>
            </ul>
        </div>
        <!-- Navbar Cat collapse Ends -->
    </div>
</nav>
<!-- Main Menu Ends -->
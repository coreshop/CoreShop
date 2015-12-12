<?php

    $categories = \CoreShop\Model\Category::getFirstLevel();
?>

<h3 class="side-heading"><?=$this->translate("Categories")?></h3>
<div class="list-group categories">
    <?php foreach($categories as $cat) { ?>
        <a href="<?=$this->url(array("lang" => $this->language, "category" => $cat->getId(), "name" => $cat->getName()), "coreshop_list")?>" class="list-group-item">
            <i class="fa fa-chevron-right"></i>
            <?=$cat->getName()?>
        </a>
    <?php } ?>
</div>
<!-- Categories Links Ends -->
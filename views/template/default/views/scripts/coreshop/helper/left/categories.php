<?php

    $categories = \CoreShop\Model\Category::getFirstLevel();

    $currentActiveCategory = $this->category instanceof \CoreShop\Model\Category ? $this->category : false;
?>

<h3 class="side-heading"><?=$this->translate("Categories")?></h3>
<?php $this->template("coreshop/helper/left/sub-category.php", array()); ?>
<!-- Categories Links Ends -->
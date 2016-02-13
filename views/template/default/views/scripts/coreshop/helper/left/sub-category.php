<?php

$categories = $this->cat ? $this->cat->getChildCategories() : \CoreShop\Model\Category::getFirstLevel();

$currentActiveCategory = $this->category instanceof \CoreShop\Model\Category ? $this->category : false;
?>

<?php if(count($categories) > 0) { ?>
<div class="list-group categories">
    <?php foreach($categories as $cat) {
        $active = $currentActiveCategory ? $cat->inCategory($currentActiveCategory, $cat->getLevel()-1) : false;
        ?>
        <a href="<?=$this->url(array("lang" => $this->language, "category" => $cat->getId(), "name" => \Pimcore\File::getValidFilename($cat->getName())), "coreshop_list")?>" class="list-group-item <?=$active ? "active" : ""?>">
            <i class="fa fa-chevron-right"></i>
            <?=$cat->getName()?>
        </a>

        <?php if($active) {

            $this->template("coreshop/helper/left/sub-category.php", array("cat" => $cat));

        } ?>
    <?php } ?>
</div>
<?php } ?>
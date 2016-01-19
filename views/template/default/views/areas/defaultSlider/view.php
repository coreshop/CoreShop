<?php

    //Fix to show edit button
    if(\Pimcore\Tool::isFrontentRequestByAdmin()) {
        echo "<br/><br/><br/>";
    }

?>
<div class="slider">
    <div id="main-carousel" class="carousel slide" data-ride="carousel">
        <!-- Wrapper For Slides Starts -->
        <div class="carousel-inner">
            <?php
                $i = 0;

                foreach($this->multihref("images")->getElements() as $el) {
                    if($el instanceof \Pimcore\Model\Asset\Image) {
                        $class = array("item");

                        if($i === 0) {
                            $class[] = "active";
                        }

                        $class = implode(" ", $class);

                        echo '<div class="' . $class . '">';
                        echo $el->getThumbnail("coreshop_slider")->getHTML(array("class" => "img-responsive"));
                        echo '</div>';

                        $i++;
                    }
                }
            ?>
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
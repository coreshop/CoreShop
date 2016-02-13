<div class="container"
    <div class="row">

        <div class="col-xs-12 col-sm-3"></div>
        <div class="col-xs-12 col-sm-9">

            <h2 class="main-heading2">
                <?=$this->translate("Compare")?>
            </h2>

            <?php if( $this->error === TRUE ) { ?>

                <div class="alert alert-info">
                    <p><?=$this->message;?></p>
                </div>

            <?php } else { ?>

                <div class="row">

                    <?php

                    $compareValues = $this->compareValues;

                    foreach( $this->compareProducts as $product ) { ?>

                        <div class="col-xs-12 col-sm-4 compare-block">

                            <div class="block">

                                <?php

                                echo $this->template("coreshop/compare/helper/product.php",
                                    array(
                                        "product" => $product,
                                        "compareValues" => isset( $compareValues[$product->getId()] ) ? $compareValues[$product->getId()] : FALSE
                                    )
                                ); ?>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </div>

    </div>
</div>

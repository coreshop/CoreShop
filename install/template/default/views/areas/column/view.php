<?php
$store = array(
    array("c_twelve_12", "12er Spalte"),
    array("c_eleven_11", "11er Spalte"),
    array("c_ten_10", "10er Spalte"),
    array("c_nine_9", "9er Spalte"),
    array("c_eight_8", "8er Spalte"),
    array("c_seven_7", "7er Spalte"),
    array("c_six_6", "6er Spalte"),
    array("c_five_5", "5er Spalte"),
    array("c_four_4", "4er Spalte"),
    array("c_three_3", "3er Spalte"),
    array("c_two_2", "2er Spalte"),
    array("c_one_1", "1er Spalte")

);

if($this->editmode)
{
    ?><div class="col-xs-12"><?php
    if($this->select("type")->isEmpty()){
        $this->select("type")->setDataFromResource("c_one_1");
    }

    echo $this->select("type", array("reload" => true, "store" => $store));
    ?></div><?php
}

$type = $this->select("type")->getData();

if ($type)
{
    $type = explode("_", $type);
    $type_nr = $type[2];

    ?>

    <div class="col-xs-<?=$type_nr ? $type_nr : 1?>">
        <?=$this->wysiwyg("c");?>
    </div>

<?php } ?>
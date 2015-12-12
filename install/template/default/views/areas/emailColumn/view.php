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
    if($this->select("type")->isEmpty()){
        $this->select("type")->setDataFromResource("one");
    }

    echo $this->select("type", array("reload" => true, "store" => $store));
}

$type = $this->select("type")->getData();

if ($type)
{
    $type = explode("_", $type);
    $type = $type[1];
    $type_nr = $type[2];

    ?>
    <?php if(!$this->editmode) { ?>
    <td class="wrapper"><table class="<?=$type?> columns"><tr><td>
    <?php } else { ?>
    <div class="col-xs-<?=$type_nr?>">
    <?php } ?>

        <?=$this->wysiwyg("c");?>

    <?php if(!$this->editmode) { ?>
    </td></tr></table></td>
    <?php } else { ?>
    </div>
    <?php } ?>

<?php } ?>
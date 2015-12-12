<?php if(!$this->editmode) { ?>
<table class="row"><tr>
<?php } ?>
    <?php
        echo $this->areablock("row", array("allowed" => array("emailColumn")));
    ?>
<?php if(!$this->editmode) { ?>
</tr></table>
<?php } ?>
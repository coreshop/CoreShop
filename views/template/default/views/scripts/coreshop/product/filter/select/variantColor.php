<div class="list-group">
    <div class="list-group-item">
        <?=$this->translate($this->label)?>
    </div>
    <div class="list-group-item">
        <div class="filter-group">
            <?php
            foreach($this->values as $value) {
                ?>
                <label class="radio">
                    <input name="<?=$this->fieldname?>" type="radio" value="<?=$value['value']?>" <?=$this->currentValue === $value['value'] ? 'checked="checked"' : ''?>>
                    <?=$this->translate($value['value'] ? $value['value'] : 'empty')?>
                </label>
                <?php
            }
            ?>
        </div>

    </div>
</div>
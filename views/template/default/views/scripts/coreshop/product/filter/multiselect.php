<div class="list-group">
    <div class="list-group-item">
        <?=$this->translate($this->label)?>
    </div>

    <div class="list-group-item">
        <div class="filter-group">
            <?php
            foreach($this->values as $value) {
                if(!$value['value'])
                    continue;
                ?>
                <label class="checkbox">
                    <input name="<?=$this->fieldname?>[]" type="checkbox" value="<?=$value['value']?>" <?=is_array($this->currentValues)&&in_array($value['value'],$this->currentValues) ? 'checked="checked"' : ''?>>
                    <?=$this->translate($value['value'])?>
                </label>
                <?php
            }
            ?>
        </div>

    </div>
</div>
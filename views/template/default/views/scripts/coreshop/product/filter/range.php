<div class="list-group">
    <div class="list-group-item">
        <?=$this->translate($this->label)?>
    </div>

    <div class="list-group-item">
        <div class="filter-group">

            <div class="row">
                <div class="col-xs-12 col-sm-6">

                    <label>From: </label>
                    <select class="form-control" name="<?=$this->fieldname?>-min">
                        <?php foreach($this->values as $value) { ?>
                            <option value="<?=$value['value']?>" <?=$this->currentValueMin === $value['value'] ? 'selected="selected"' : ''?>><?=$this->translate($value['value'] ? $value['value'] : $this->translate('no selection'))?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <label>To: </label>
                    <select class="form-control" name="<?=$this->fieldname?>-max">
                        <?php foreach($this->values as $value) { ?>
                            <option value="<?=$value['value']?>" <?=$this->currentValueMax === $value['value'] ? 'selected="selected"' : ''?>><?=$this->translate($value['value'] ? $value['value'] : $this->translate('no selection'))?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

    </div>

</div>
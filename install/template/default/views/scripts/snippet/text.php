<?php $content = $this->wysiwyg("text_snippet", array("customConfig" => "/static/custom/js/ckeditor_config.js"))?>

<?=($this->editmode ? $content : $this->wysiwygText($content))?>

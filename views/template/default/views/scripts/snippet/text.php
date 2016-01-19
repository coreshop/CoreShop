<?php $content = $this->wysiwyg("text_snippet", array("customConfig" => CORESHOP_TEMPLATE_RESOURCES."custom/js/ckeditor_config.js"))?>

<?=($this->editmode ? $content : $this->wysiwygText($content))?>

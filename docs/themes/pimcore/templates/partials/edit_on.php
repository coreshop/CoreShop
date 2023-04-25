<?php
$edit_on = $params->getHTML()->getEditOn();
if ($edit_on) { ?>
    <span class="edit_on">
        <a href="<?= $edit_on['basepath'] ?>/<?= str_replace('_index.md', 'README.md', $page['relative_path']) ?>" target="_blank">Edit on <?= $edit_on['name'] ?></a>
    </span>
<?php } ?>

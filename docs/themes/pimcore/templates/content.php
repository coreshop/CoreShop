<?php $this->layout('theme::layout/05_page') ?>
<article class="Page">

    <div class="Page__header">
        <p class="breadcrumbs">
            <?= $page['breadcrumbs'] ? $this->get_breadcrumb_title($page, $base_page) : $page['title'] ?>
        </p>

        <?php if ($params['html']['date_modified']) { ?>
        <span style="float: left; font-size: 10px; color: gray;">
            <?= date("l, F j, Y g:i A", $page['modified_time']); ?>
        </span>
        <?php } ?>
    </div>

    <div class="action-box">
        <?php $this->insert('theme::partials/change_version', ['page' => $page, 'params' => $params]) ?>
        <?php $this->insert('theme::partials/edit_on', ['page' => $page, 'params' => $params]) ?>
    </div>

    <div class="s-content">
        <?= $page['content']; ?>
    </div>

    <?php
    $buttons = (!empty($page['prev']) || !empty($page['next']));
    $has_option = array_key_exists('jump_buttons', $params['html']);
    if ($buttons && (($has_option && $params['html']['jump_buttons']) || !$has_option)) {
    ?>
    <nav>
        <ul class="Pager">
            <?php if (!empty($page['prev'])) {
        ?><li class=Pager--prev><a href="<?= $base_url . $page['prev']->getUrl() ?>">Previous</a></li><?php

    } ?>
            <?php if (!empty($page['next'])) {
        ?><li class=Pager--next><a href="<?= $base_url . $page['next']->getUrl() ?>">Next</a></li><?php

    } ?>
        </ul>
    </nav>
    <?php

} ?>

<?php $this->insert('theme::partials/disqus', ['page' => $page, 'params' => $params]) ?>

</article>


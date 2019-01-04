<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/$this->layout('theme::layout/05_page') ?>
<article class="Page">

    <div class="Page__header">
        <?= $page['breadcrumbs'] ? $this->get_breadcrumb_title($page, $base_page) : $page['title'] ?>
        <?php if ($params['html']['date_modified']) {
    ?>
        <span style="float: left; font-size: 10px; color: gray;">
            <?= date('l, F j, Y g:i A', $page['modified_time']); ?>
        </span>
        <?php
} ?>
        <?php if (array_key_exists('edit_on_github', $params['html']) && $params['html']['edit_on_github']) {
    ?>
        <span style="float: right; font-size: 10px; color: gray;">
            <a href="https://github.com/<?= $params['html']['edit_on_github'] ?>/<?= $page['relative_path'] ?>" target="_blank">Edit on GitHub</a>
        </span>
        <?php
} ?>
    </div>


    <div class="s-content">
        <?= $page['content']; ?>
    </div>

    <?php if (!empty($page['prev']) || !empty($page['next'])) {
    ?>
    <nav>
        <ul class="Pager">
            <?php if (!empty($page['prev'])) {
        ?><li class=Pager--prev><a href="<?= $base_url.$page['prev']->getUrl() ?>">Previous</a></li><?php

    } ?>
            <?php if (!empty($page['next'])) {
        ?><li class=Pager--next><a href="<?= $base_url.$page['next']->getUrl() ?>">Next</a></li><?php

    } ?>
        </ul>
    </nav>
    <?php

} ?>
</article>


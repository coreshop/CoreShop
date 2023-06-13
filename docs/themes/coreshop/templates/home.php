<?php $this->layout('theme::layout/05_page') ?>

<?php $this->start('classes') ?>landingpage<?php $this->stop() ?>

<article class="Page">

    <div class="Page__header">
        <p class="breadcrumbs"></p>
    </div>

    <div class="action-box">
        <?php $this->insert('theme::partials/change_version', ['page' => $page, 'params' => $params]) ?>
        <?php $this->insert('theme::partials/edit_on', ['page' => $page, 'params' => $params]) ?>
    </div>

    <div class="s-content">
        <?= $page['content']; ?>

        <?php if (!empty($params['html']['linked_docs'] ?? [])) {  ?>

            <br/><br/>

            <h4>Also check out our Pimcore Extensions</h4>

            <div class="Columns__landing">

                <?php $index = 0; ?>
                <?php foreach ($params['html']['linked_docs'] as $name => $linked_doc) { ?>

                    <div class="column <?= $linked_doc['css_classes'] ?? '' ?>">

                        <div class="extension-card ribbon-box">
                            <?php if(isset($linked_doc['ribbon_text'])) { ?>
                                <div class="ribbon"><span><?= $linked_doc['ribbon_text'] ?></span></div>
                            <?php } ?>

                            <h3><?= $linked_doc['title'] ?></h3>
                            <p><?= $linked_doc['description'] ?></p>

                            <a class="button" target="_blank" href="<?= $linked_doc['link'] ?>">More Information</a>
                        </div>
                    </div>

                    <?php $index++; ?>
                <?php } ?>

            </div>
        <?php } ?>

        <?php if (!empty($params['html']['landingpage_links'] ?? [])): ?>

            <div class="Columns__landing">

                <?php foreach ($params['html']['landingpage_links'] as $title => $link): ?>

                    <div class="column">
                        <h2><?= $title ?></h2>
                        <?= $this->get_navigation($tree[$link], './' . $link, isset($params['request']) ? $params['request'] : '', $base_page, 0); ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>
    </div>

    <?php $this->insert('theme::partials/disqus', ['page' => $page, 'params' => $params]) ?>

</article>

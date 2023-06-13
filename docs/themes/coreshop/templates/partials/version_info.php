<?php
$versionInfo = $params['html']['version_info'] ?? [];
if (isset($params['build_versions']) && ($versionInfo['include'] ?? false)): ?>

    <div class="version-info">
        Built

        <?php
        if (isset($versionInfo['source_url']) && isset($versionInfo['source_name']) && $params['build_versions']['source']) {
            $replacements = [
                '{commit_hash}'       => $params['build_versions']['source'],
                '{short_commit_hash}' => substr($params['build_versions']['source'], 0, 6)
            ];

            $from = 'from ';
            $from .= sprintf(
                '<a href="%s">%s</a>',
                str_replace(array_keys($replacements), array_values($replacements), $versionInfo['source_url']),
                str_replace(array_keys($replacements), array_values($replacements), $versionInfo['source_name'])
            );

            echo $from;
        }
        ?>

        with

        <a href="https://github.com/pimcore/pimcore-docs/commit/<?= $params['build_versions']['docs'] ?>">
            pimcore-docs@<?= substr($params['build_versions']['docs'], 0, 6) ?>
        </a>
        .
    </div>

<?php endif ;?>

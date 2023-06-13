<!DOCTYPE html>
<!--[if lt IE 7]>       <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>          <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>          <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->  <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <title><?= $page['title']; ?> <?php if ($page['title'] != $params['title']) {
    echo '- ' . $params['title'];
} ?></title>
    <meta name="description" content="<?= $params['tagline']; ?>" />
    <meta name="author" content="<?= $params['author']; ?>">
    <meta charset="UTF-8">
    <link rel="icon" href="<?= $params['theme']['favicon']; ?>" type="image/x-icon">
    <!-- Mobile -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font -->
    <?php foreach ($params['theme']['fonts'] as $font) {
    echo "<link href='$font' rel='stylesheet' type='text/css'>";
} ?>

    <!-- CSS -->
    <?php foreach ($params['theme']['css'] as $css) {
    echo "<link href='$css' rel='stylesheet' type='text/css'>";
} ?>

    <?php if ($params['html']['search']) {
    ?>
        <!-- Tipue Search -->
        <link href="<?= $base_url; ?>tipuesearch/tipuesearch.css" rel="stylesheet">
    <?php

} ?>

    <!--[if lt IE 9]>
    <script src="<?= $base_url; ?>themes/pimcore/js/source/html5shiv-3.7.3.min.js"></script>
    <![endif]-->
</head>
<body class="<?= $params['html']['float'] ? 'with-float' : ''; ?> <?= $this->section('classes'); ?>">
    <?= $this->section('content'); ?>

    <?php
    if ($params['html']['google_analytics']) {
        $this->insert('theme::partials/google_analytics', ['analytics' => $params['html']['google_analytics'], 'host' => array_key_exists('host', $params) ? $params['host'] : '']);
    }
    if ($params['html']['piwik_analytics']) {
        $this->insert('theme::partials/piwik_analytics', ['url' => $params['html']['piwik_analytics'], 'id' => $params['html']['piwik_analytics_id']]);
    }
    ?>

    <!-- JS -->
    <?php foreach ($params['theme']['js'] as $js) {
        echo '<script src="' . $js . '"></script>';
    } ?>

    <script>hljs.initHighlightingOnLoad();</script>

    <script src="https://cdn.jsdelivr.net/combine/npm/lightgallery,npm/lg-autoplay,npm/lg-fullscreen,npm/lg-hash,npm/lg-pager,npm/lg-share,npm/lg-thumbnail,npm/lg-video,npm/lg-zoom" />
    <link type="text/css" rel="stylesheet" href="https://sachinchoolur.github.io/lightGallery/lightgallery/css/lightgallery.css" />


    <style>
        .smallimage { width: 50% }
    </style>

    <script>

        $(document).ready(function() {
            var divElement = document.querySelector(".lightbox1");
            console.log(divElement);
            var imageElement = document.querySelector(".lightbox1 + p > img");
            console.log(imageElement);
            console.log(imageElement.src);

            divElement.innerHTML = '<a href="' + imageElement.src + '"><img class="smallimage" src="' + imageElement.src + '" /></a>';
            imageElement.remove();

            $(".lightbox1").lightGallery();
        });

    </script>


    <?php if ($params['html']['search']) {
        ?>
        <!-- Tipue Search -->
        <script type="text/javascript" src="<?php echo $base_url; ?>tipuesearch/tipuesearch.js"></script>

        <script>
            window.onunload = function(){}; // force $(document).ready to be called on back/forward navigation in firefox
            $(function() {
                tipuesearch({
                    'base_url': '<?php echo $base_url?>',
                    'showURL': false
                });
            });
        </script>
    <?php

    }
    ?>

</body>
</html>

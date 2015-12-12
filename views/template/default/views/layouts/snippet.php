<!doctype html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php echo $this->headTitle($this->document->getProperty("title"))?>

    <!-- Google Web Fonts -->
    <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,400,300,700" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400" rel="stylesheet" type="text/css">

    <!-- CSS Files -->
    <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/owl.carousel.css" rel="stylesheet">
    <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/shop.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="js/ie8-responsive-file-warning.js"></script>
    <![endif]-->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/fav-144.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/fav-114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/fav-72.png">
    <link rel="apple-touch-icon-precomposed" href="/static/images/fav-57.png">
    <link rel="shortcut icon" href="/static/images/fav.png">

</head>
<body class="lang-<?=$this->language?>">
<!-- Header Section Starts -->

<?= $this->layout()->content ?>

<!-- Footer Section Ends -->
<!-- JavaScript Files -->
<script src="/static/vendor/jquery-1.11.1.min.js"></script>
<script src="/static/vendor/jquery-migrate-1.2.1.min.js"></script>
<script src="/static/vendor/bootstrap.min.js"></script>
<script src="/static/vendor/bootstrap-hover-dropdown.min.js"></script>
<script src="/static/vendor/bootstrapvalidator/bootstrapValidator.min.js"></script>
<script src="/static/vendor/jquery.magnific-popup.min.js"></script>
<script src="/static/vendor/owl.carousel.min.js"></script>
<script src="/static/vendor/purl.js"></script>
<script src="/static/js/shop.js"></script>
<script src="/static/js/custom.js"></script>
</body>
</html>
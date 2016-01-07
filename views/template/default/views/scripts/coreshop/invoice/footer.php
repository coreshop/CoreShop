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

        <title>Invoice <?=$this->order->getOrderNumber()?></title>

        <!-- Google Web Fonts -->
        <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,400,300,700" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400" rel="stylesheet" type="text/css">

        <!-- CSS Files -->
        <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/owl.carousel.css" rel="stylesheet">
        <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/invoice.css" rel="stylesheet">
    </head>
    <body onload="pageNumber()">
        <header>
            <div class="row">
                <div class="col-xs-10">
                    Phone: +00 000 000 000 | E-Mail: info@coreshop.org | www.coreshop.org
                </div>
                <div class="col-xs-2 text-right">
                    <span id="currentPage"></span>
                     /
                    <span id="totalPages"></span>
                </div>
            </div>
        </header>
        <script>
            var pdfInfo = {};
            var x = document.location.search.substring(1).split('&');
            for (var i in x) { var z = x[i].split('=',2); pdfInfo[z[0]] = unescape(z[1]); }

            function pageNumber() {
                var page = pdfInfo.page || 1;
                var pageCount = pdfInfo.topage || 1;
                document.getElementById('currentPage').textContent = page;
                document.getElementById('totalPages').textContent = pageCount;
            }
        </script>
    </body>
</html>
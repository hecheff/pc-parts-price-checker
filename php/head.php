<head>
    <title>PC Parts Price Checker | HARO PLANET</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="shortcut icon" href="./favicon.ico">   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">	<!-- Disable compatibility mode for IE browsing -->

    <link rel="stylesheet" type="text/css" href="./css/common.css?ver=<?php echo CSS_VERSION; ?>">
    <script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script type="text/javascript" src="/js/k_command.js"></script>
    <?php include("fancybox.php"); ?>

    <script>
        // Set display filters
        function toggle_products_display() {
            var brand_val   = $('#filter_brand')[0].value;
            var type_val    = $('#filter_type')[0].value;
            
            var products = [
                <?php 
                    foreach ($products as $product) {
                        echo '"'.$product['brand']."_".$product['type'].'", ';
                    }
                ?>
            ];
            products.forEach (product => {
                var split_val = product.split('_');

                if ((brand_val == split_val[0] || brand_val == "") && (type_val == split_val[1] || type_val == "")) {
                    $('[name='+product+']').css("display", "block");
                } else {
                    $('[name='+product+']').css("display", "none");
                }
            });
        }
    </script>
    <script data-ad-client="ca-pub-7564011937299425" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
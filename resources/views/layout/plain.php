<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($TITLE) ? $TITLE : 'Bidvest Logs'; ?> | BidVest Logs</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	    <link href="/assets/css/main.css" rel="stylesheet" />
</head>
<body>

    <section class="main">
        
        <?php echo $content; ?>

    </section>


    <script type="text/javascript" src='/assets/js/default.js'></script>
</body>
</html>
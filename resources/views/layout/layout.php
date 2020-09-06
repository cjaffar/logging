<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($TITLE) ? $TITLE : 'Bidvest Logs'; ?> | BidVest Logs</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" />
        <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet" />
	    <link href="/assets/css/main.css" rel="stylesheet" />
</head>
<body>

	<div class="page-wrapper h-100">

		<div class="page-content h-100">

			<?php include 'header.php'; ?>

			<div class="main-container container">

					<div class="page-header">
						
						<!-- Breadcrumb start -->
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><?php echo $title; ?></li>
						</ol>
						<!-- Breadcrumb end -->

						<!-- App actions start -->
						<div class="app-actions">

						<?php if($actions) : ?>
							<?php foreach($actions as $act) : ?>

								<?php echo $act; ?>

							<?php endforeach; ?>
						<?php endif; ?>
<!-- 							<button type="button" class="btn">Today</button>
							<button type="button" class="btn">Yesterday</button>
							<button type="button" class="btn">7 days</button>
							<button type="button" class="btn">15 days</button>
							<button type="button" class="btn active">30 days</button> -->
						</div>
						<!-- App actions end -->

					</div>

					<div class="row gutters">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

							<?php echo $content; ?>

						</div>

					</div>

			</div>

			<!-- Container fluid start -->
				<div class="container-fluid">
					<!-- Row start -->
					<div class="row gutters">
						<div class="col-12">
							<!-- Footer start -->
							<div class="footer">
								Copyright <?php echo date('Y'); ?> BidvestData Logs
							</div>
							<!-- Footer end -->
						</div>
					</div>
					<!-- Row end -->
				</div>

		</div>

	</div>


    <script type="text/javascript" src='/assets/js/default.js'></script>
    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>
</body>
</html>


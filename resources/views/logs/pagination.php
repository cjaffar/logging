
<div class="col col-12">
	<div class="card">
		<div class="card-body">
		
			<nav aria-label="Page navigation">
				<ul class="pagination rounded info justify-content-center">
				<li>
					<span class="p-2 d-block mr-2">Total results: <?php echo $total; ?></span>
				</li>
					<li class="page-item"><a class="page-link prev" href="#" data-href="/search/<?php echo $path.'/'; ?>"> <!--disabled-->
						<ion-icon name="chevron-back-outline"></ion-icon> &nbsp;</a>
					</li>
					<?php /* foreach(range(1, $num_pages) as $page) : ?>
						<li class="page-item page-number-<?php echo $page; ?> <?php echo ($page_number == $page) ? 'active' : ''; ?>">
							<a class="page-link" href="#" data-href="/search/<?php echo $path; ?>/<?php echo $page; ?>" data-number="<?php echo $page; ?>">
								<?php echo $page; ?>
							</a>
						</li>
					<?php endforeach; */ ?>
					
						<li class="page-item active">
							<a class="page-link page-number-<?php echo $page_number; ?>" href="#" data-number="<?php echo $page_number; ?>">
								<?php echo $page_number; ?>
							</a>
						</li>
					
					<li class="page-item"><a class="page-link next" href="#" data-href="/search/<?php echo $path.'/'; ?>">
						<ion-icon name="chevron-forward-outline"></ion-icon> &nbsp;
					</a></li>
					
				<li>
					<span class="ml-4 p-2 d-block"><?php echo $num_pages; ?> pages.</span>
				</li>
				</ul>
			</nav>
		</div>
	</div>
</div>
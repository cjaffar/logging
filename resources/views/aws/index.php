	<div class="card">

			<?php if($error) : ?>
			<div class="card-header pb-0">
				<div class="card-title text-center alert alert-warning">
				<?php echo $error; ?>
				</div>
			</div>
			<?php else: ?>
			<div class="card-body table-responsive">

				<table id="frmAWS" class="table datatable1">
					<thead>
						<tr>
							<th>Filename</th>
							<th>Last Modified</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($filelist as $k => $v) : ?>
						<tr>
							<td>
								<a href='#' data-href="<?php echo $v['filename'].'---'; ?>" class="view-aws-file">
									<?php echo basename($v['filename']); ?>
								</a>
							</td>
							<td><?php echo date('Y-m-d H:i:s', strtotime($v['modified'])); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

			</div>
			<?php endif; ?>

	</div>
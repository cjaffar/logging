<div class="card">

	<div class="card-body table-responsive">

		<table id="frmAWS" class="table datatable1">
			<thead>
				<tr>
					<th>System</th>
					<th>Name</th>
					<th>Slug</th>
					<th>Created</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($clients as $k => $v) : ?>
				<tr>
					<td>
						<?php echo $v['system']; ?>
					</td>
					<td><?php echo $v['name']; ?></td>
					<td><?php echo $v['slug']; ?></td>
					<td><?php echo date('Y-m-d', strtotime($v['created'])); ?></td>
					<td>
						<a href="/client/edit/<?php echo $v['slug']; ?>" class="edit-client">
						Edit
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>

</div>
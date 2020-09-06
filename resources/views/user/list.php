
<div class="table-container">
	<div class="t-header">Copy/CSV/Print</div>
	<div class="table-responsive">
		<table id="copy-print-csv" class="table custom-table">
			<thead>
				<tr>
				  <th>Username</th>
				  <th>First Name</th>
				  <th>Last Name</th>
				  <th>Admin</th>
				  <th>Last Login</th>
				  <th>Dirty</th>
				  <th class="text-center">Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach( $list as $userl ) : ?>
				<tr>
				  <td><?php echo $userl['username']; ?></td>
				  <td><?php echo $userl['firstname']; ?></td>
				  <td><?php echo $userl['lastname']; ?></td>
				  <td><?php echo ($userl['admin'] == 1) ? 'Admin' : ''; ?></td>
				  <td><?php echo (!$userl['lastlogin']) ? '' : date('d-M-Y H:i', strtotime($userl['lastlogin'])); ?></td>
				  <td><?php echo ($userl['dirty'] == 1) ? 'Dirty' : ''; ?></td>
				  <td class="text-center">
				  	
				  	<a href="<?php echo '/login/user-edit/'.$userl['username']; ?>" title="Edit" class='p-2'>
				  		<ion-icon name="pencil-sharp"></ion-icon>
				  	</a>
				  	<a href="#" title="Force Password Change" class='p-2'>
				  		<ion-icon name="remove-circle"></ion-icon>
				  	</a>
				  		<!-- <ion-icon name="heart"></ion-icon> -->
				  </td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>

<div class="card">

	<div class="card-header" <?php if($client) { ?>style="display:none"<?php } ?>>

		<div class="alert alert-warning">Client could not be found.</div>

	</div>
	<div class="card-body" <?php if(!$client) { ?>style="display:none"<?php } ?>>

	<form action="/client/edit/<?php echo $client['slug']; ?>" class="frm frmClientNew mb-4" method="post">		
		<div class="row gutters">

				
			<div class="col-12">
				<div class="form-group">
					<label for="Slug">Slug</label>
					<input type="hidden" class="form-control form-control-lg" id="slug" name="slug" placeholder="Enter Slug" required="required" value="<?php echo $client['slug']; ?>" />
					
					<?php if($client['slug']) :?>
						<p><?php echo $client['slug']; ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-12">
				<div class="form-group">
					<label for="system">System</label>
					<select name="system" id="system" class="form-control form-control-lg" required="required">
						<?php foreach($systems as $system): ?>
							<option value="<?php echo $system; ?>" <?php echo ($client['system'] == $system) ? 'selected="selected"' : ''; ?>><?php echo $system; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="Name">Name</label>
					<input type="text" class="form-control-lg form-control" id="name" name="name" placeholder="Enter Client Name" required="required" value="<?php echo $client['name']; ?>" />
				</div>
			</div>

			<div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12 mt-4">
				<div class="form-group">
					<input type="submit" value="Save" class="btn btn-primary btn-lg mr-4"> <a href="/client">Cancel</a>
				</div>
			</div>
			
		</div>
</form>
	</div>
</div>
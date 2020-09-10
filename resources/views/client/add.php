
<div class="card">
	<div class="card-body">

	<form action="/client/add" class="frm frmClientNew mb-4" method="post">		
		<div class="row gutters">

			<div class="col-12">
				<div class="form-group">
					<label for="system">System</label>
					<select name="system" id="system" class="form-control form-control-lg" required="required">
						<?php foreach($systems as $system): ?>
							<option value="<?php echo $system; ?>"><?php echo $system; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="Name">Name</label>
					<input type="text" class="form-control-lg form-control" id="name" name="name" placeholder="Enter Client Name" required="required" />
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="Slug">Slug</label>
					<input type="text" class="form-control form-control-lg" id="slug" name="slug" placeholder="Enter Slug" required="required" />
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
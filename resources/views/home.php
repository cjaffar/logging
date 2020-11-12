<div class='main-search'>

	<div class="card alert alert-info mb-0 justify-content-center">
		
		<div class="card-header pb-0">
				<div class="form-inline">
				
					<span class="mb-2 mr-2" for="inputSystem">System:</span>
					<select class="form-control mb-2 mr-4" id="inputSystem" name="system">
						<option value=''> - All - </option>
						<?php foreach($systems as $system) : ?>
							<option value="<?php echo $system['system']; ?>"><?php echo $system['system']; ?></option>
						<?php endforeach; ?>
					</select>
					
					<span class="mr-2" for="inlineFormInputName2">Client: </span>
					<select class="form-control mb-2 mr-4" id="inputClient" name="client">
					</select>
					
					<span class="mr-2" for="datepicker">Date Range: </span>
					<div class="input-group mb-2 mr-sm-2">
						<div class="input-group-prepend">
							<div class="input-group-text">
								<ion-icon name="calendar-outline"></ion-icon>
							</div>
						</div>
						<input type="text" class="form-control form-control-lg" id="datepicker" name="datepicker" placeholder="Dates" />
					</div>
					
					
<!-- 					<input type="text" class="form-control mb-2 mr-sm-2" id="datepicker" name="datepicker" placeholder="Dates" /> -->
					<input type="hidden" class="form-control mb-2 mr-sm-2" id="min_date" name="min_date" value="<?php echo $min_date; ?>" />
				</div>
		</div>

		<!-- <hr /> -->

	</div>

	<div class="card log-search" style="display:none;">

			<div class="card-header pb-0">
				<div class="card-title text-center">filter your search here</div>
			</div>
			<div class="card-body">

				<form class="frmSearch">

					<div class="form-inline justify-content-center">

						<label class="sr-only" for="inlineAddressFrom">From Address</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineAddressFrom" name="inlineAddressFrom" placeholder="From Email Address">
						
						<label class="sr-only" for="inlineAddressTo">From Address</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineAddressTo" name="inlineAddressTo" placeholder="To Email Address">
						
<!-- 						<label class="sr-only" for="inlineAddressReplyTo">ReplyTo Address</label> -->
<!-- 						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineAddressReplyTo" name="inlineAddressReplyTo" placeholder="ReplyTo Email Address"> -->

						<label class="sr-only" for="inlineSubject">Subject</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineSubject" name="inlineSubject" placeholder="Subject">

						<label class="sr-only" for="inlineDetail">Detail</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineDetail" name="inlineDetail" placeholder="Detail">

<!-- 						<label class="sr-only" for="dateTo">Subject</label>-->
						<input type="hidden" class="form-control mb-2 mr-sm-2" id="search_url" name="search_url" value="/search/" />


						<button type="submit" class="btn btn-primary mb-2">Submit</button>
						<button type="reset" class="btn mb-2 ml-4 btn-secondary">Reset</button> 
					</div>

				</form>

<!-- 				<hr> -->

			</div>

	</div>

	<div class="logs-table py-4 my-2 table-responsive">

		<div class="row logs-loading p-4 m-4 justify-content-center"  style="display:none">

			<button class="btn btn-warning " type="button">
				<span class="spinner-grow spinner-grow-lg"></span>
				Loading...
			</button>
		</div>

		<table id="copy-print-csv" class="table log-table table-hover" style="display:none">
		<thead>
			<tr>
				<th>Log Date</th>
				<!-- <th>System</th> -->
				<th>From</th>
				<th>To</th>
<!-- 				<th>Reply To</th> -->
				<th>Subject</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<!-- <td></td> -->
			<td></td>
			<td></td>
			<td></td>
<!-- 			<td></td> -->
			<td></td>
			<td></td>
		</tr>
		</tbody>
		</table>

		<div class="pages"></div>
	</div>
	
</div>
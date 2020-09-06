<div class='main-search'>

	<div class="card">

			<div class="card-header pb-0">
				<div class="card-title text-center">Search for your logs here</div>
			</div>
			<div class="card-body">

				<form class="frmSearch">

					<div class="form-inline justify-content-center">
						
						<label class="sr-only" for="inlineFormInputName2">System</label>
						<select class="form-control mb-2 mr-sm-2" id="inputClient" name="client">
							<option value='all'> - All - </option>
							<?php foreach($clients as $client) : ?>
								<option value="<?php echo $client['slug']; ?>"><?php echo $client['name']; ?></option>
							<?php endforeach; ?>
						</select>

						<label class="sr-only" for="inlineAddress">Address</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineAddress" name="inlineAddress" placeholder="Email Address">

						<label class="sr-only" for="inlineSubject">Subject</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="inlineSubject" name="inlineSubject" placeholder="Subject">

						<label class="sr-only" for="datepicker">Period</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="datepicker" name="datepicker" placeholder="Dates">

<!-- 						<label class="sr-only" for="dateTo">Subject</label>-->
						<input type="hidden" class="form-control mb-2 mr-sm-2" id="search_url" name="search_url" value="/search/" />
						<input type="hidden" class="form-control mb-2 mr-sm-2" id="min_date" name="min_date" value="<?php echo $min_date; ?>" />


						<button type="submit" class="btn btn-primary mb-2">Submit</button>
						<input type="reset" class="btn mb-2 ml-4 btn-secondary" value="Reset" />
					</div>

				</form>

				<hr>

			</div>

	</div>

	<div class="logs-table py-4 my-2 table-responsive">

		<div class="row logs-loading p-4 m-4 justify-content-center"  style="display:none">

			<button class="btn btn-warning " type="button">
				<span class="spinner-grow spinner-grow-lg"></span>
				Loading...
			</button>
		</div>

		<table id="copy-print-csv" class="table custom-table" style="display:none">
		<thead>
			<tr>
				<th>Log Date</th>
				<th>System</th>
				<th>From</th>
				<th>To</th>
				<th>Reply To</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		</tbody>
		</table>

		<div class="pages"></div>
	</div>
	
</div>
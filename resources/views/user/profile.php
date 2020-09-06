<?php 

switch($function) {
	
	case 'add' :
		$url_action = '/login/add-user';
		$success_url = '/login/users';
		break;

	case 'edit' :
		$url_action = '/login/user-edit';
		$success_url = '/login/users';
		break;

	default:
		$url_action = '/login/my-profile';
		$success_url = '/search';
		break;
}

?>

<div class="card">
	<div class="card-body">

<form action="<?php echo $function; ?>" class="frm profileFrm mb-4" action="post">		
		<div class="row gutters">

			<div class="col-12">
				<div class="form-group">
					<label for="inputReadOnly">Username</label>
					<input class="form-control form-control-lg" id="inputReadOnly" type="text" name="username" placeholder="Readonly input" readonly="" value="<?php echo $profile['username']; ?>">
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="firstName">First Name</label>
					<input type="text" class="form-control-lg form-control" id="firstName" name="firstName" placeholder="Enter First Name" required="required" value="<?php echo $profile['firstname']; ?>" />
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="lastName">Last Name</label>
					<input type="text" class="form-control form-control-lg" id="lastName" name="lastName" placeholder="Enter Last Name" required="required" value="<?php echo $profile['lastname']; ?>">
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="inputPwd">Password</label>
					<input type="password" class="form-control form-control-lg" id="inputPwd" name="inputPwd" placeholder="Password" />
					<small id="passwordHelpBlock" class="form-text text-muted">
						Password fields should be left blank should you wish not to change your Password!
					</small>
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label for="inputPwd2">Confirm Password</label>
					<input type="password" class="form-control form-control-lg" id="inputPwd2" name="inputPwd2" placeholder="Password"data-parsley-equalto="#inputPwd" />
				</div>
			</div>

			<?php if (isset($user['admin']) && $user['admin'] == 1) : ?>

			<div class="col-12">
				<div class="form-check mb-2 mr-sm-2">
					<input class="form-check-input" type="checkbox" id="isAdmin" name="isAdmin" <?php if($profile['admin'] == 1) { ?>checked="checked<?php } ?>>
					<label class="form-check-label" for="isAdmin">
						Is Admin
					</label>
				</div>
			</div>

			<?php endif; ?>

			<div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12 mt-4">
				<div class="form-group">
					<input type="submit" value="Save" class="btn btn-primary btn-lg mr-4">
					<a href='/login/users'>Cancel</a>
				</div>
			</div>
<input type="hidden" name="success_url" id="success_url" value="<?php echo $success_url; ?>" />
			
		</div>
</form>
	</div>
</div>
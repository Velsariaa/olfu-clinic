<!DOCTYPE html>
<html lang="en" dir="ltr">
{include "`$smarty.current_dir`/../base/head.tpl"}

<body>
	<!-- Page content -->
	<div class="page-content img-bg">
		<!-- Main content -->
		<div class="content-wrapper">
			<!-- Inner content -->
			<div class="content-inner">
				<!-- Content area -->
				<div class="content d-flex justify-content-center align-items-center">
					<!-- Login form -->
					<form class="flex-fill" action="{'register.create'|route}" method="post">
						<div class="row">
							<div class="col-lg-6 offset-lg-3">
								<div class="card mb-0">
									<div class="card-body">
										{if flashdata('error_message')}
											<div class="alert alert-danger border-0 alert-dismissible fade show">
												{flashdata('error_message')}
												<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
											</div>
										{/if}

										<div class="text-center mb-3">
											<div
												class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
												<img src="{$assets_url}/images/logo1.png" class="h-48px" alt=""
													style="height: 5rem !important">
											</div>
											<h5 class="mb-0">Create your account</h5>
											<span class="d-block text-muted">Enter your credentials below</span>
										</div>

										{$form_data = flashdata('form_data')|default:[]}

										<div class="row">
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">First Name</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="text" class="form-control px-2" placeholder="First name"
															name="first_name" value="{$form_data.first_name|default:''}">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">Last Name</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="text" class="form-control px-2" placeholder="Last name"
															name="last_name" value="{$form_data.last_name|default:''}">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">Email</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="email" class="form-control px-2" placeholder="Email"
															name="email" value="{$form_data.email|default:''}">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">Contact</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="text" class="form-control px-2" placeholder="Contact"
															name="contact" value="{$form_data.contact|default:''}">
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">Username</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="text" class="form-control px-2" placeholder="Username"
															name="username" value="{$form_data.username|default:''}">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="mb-3">
													<label class="form-label">Password</label>
													<div class="form-control-feedback form-control-feedback-start">
														<input type="password" class="form-control px-2"
															placeholder="•••••••••••" name="password">
													</div>
												</div>
											</div>
										</div>

										<div class="mb-3">
											<button type="submit" class="btn btn-success w-100">Register</button>
										</div>

										<div class="text-center text-muted content-divider mb-3">
											<span class="px-2">Already have an account?</span>
										</div>

										<div class="mb-3">
											<a href="{'login'|route}" class="btn btn-light w-100">Sign In</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
					<!-- /login form -->
				</div>
				<!-- /content area -->
			</div>
			<!-- /inner content -->
		</div>
		<!-- /main content -->
	</div>
	<!-- /page content -->
</body>

</html>

{block name='styles'}
	<style>
		.img-bg {
			background-image: url('{$assets_url}/images/antipolo.jpg'); /* Replace with your image path */
			background-size: cover;  /* Ensures the image covers the whole div */
			background-position: center;  /* Centers the image */
			background-repeat: no-repeat;  /* Prevents the image from repeating */
		}

	</style>
{/block}
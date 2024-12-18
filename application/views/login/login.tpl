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
					<form class="login-form" action="{'login.check'|route}" method="post">
						<div class="card mb-0">
							<div class="card-body">
								{if flashdata('success_message')}
								<div class="alert alert-success border-0 alert-dismissible fade show">
									{flashdata('success_message')}
									<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							    </div>
							    {/if}
								{if flashdata('error_message')}
								<div class="alert alert-danger border-0 alert-dismissible fade show">
									{flashdata('error_message')}
									<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							    </div>
							    {/if}

								<div class="text-center mb-3">
									<div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
										<img src="{$assets_url}/images/logo1.png" class="h-48px" alt="" style="height: 5rem !important">
									</div>
									<h5 class="mb-0">Login to your account</h5>
									<span class="d-block text-muted">Enter your credentials below</span>
								</div>

								{$form_data = flashdata('form_data')|default:[]}

								<div class="mb-3">
									<label class="form-label">Username</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="text" class="form-control px-2" placeholder="username" name="username" value="{$form_data.username|default:''}">
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Password</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="password" class="form-control px-2" placeholder="•••••••••••" name="password">
									</div>
								</div>
								
								<div class="mb-3">
									<button type="submit" class="btn btn-success w-100">Sign In</button>
								</div>

								<div class="text-center text-muted content-divider mb-3">
									<span class="px-2">Don't have an account?</span>
								</div>

								<div class="mb-3">
									<a href="{'register'|route}" class="btn btn-light w-100">Sign Up</a>
								</div>

								{* <div class="text-center">
									<a href="login_password_recover.html">Forgot password?</a>
								</div> *}
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
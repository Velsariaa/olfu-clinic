<!DOCTYPE html>
<html lang="en" dir="ltr">
{include "`$smarty.current_dir`/head.tpl"}
{assign var="js_captures" value=[]}
<body>
	{if $navbar_visible|default:true}
	{include "`$smarty.current_dir`/navbar.tpl"}
	{/if}

	<!-- Page content -->
	<div class="page-content">
		{if $sidebar_visible|default:false}
		{include "`$smarty.current_dir`/sidebar.tpl"}
		{/if}

		{block name='main-content'}
		<!-- Main content -->
		<div class="content-wrapper">
			<!-- Inner content -->
			<div class="content-inner">

				{if $header_visible|default:true and $header_title|default:false}
				{block name='header'}
				<!-- Page header -->
				<div class="page-header page-header-light shadow">
					<div class="page-header-content d-lg-flex">
						{block name='header-content'}
						<div class="d-flex">
							<h4 class="page-title mb-0">
								{$header_title}
							</h4>
						</div>
						{block name='header_right'}
						{/block}
						{/block}
					</div>
				</div>
				<!-- /page header -->
				{/block}
				{/if}

				{block name='content-area'}
				<!-- Content area -->
				<div class="content">
					{block name='content'}
					{/block}
				</div>
				<!-- /content area -->
				{/block}
				
				{if $footer_visible|default:true}
				{block name='footer'}
				{include "`$smarty.current_dir`/../base/footer.tpl"}
				{/block}
				{/if}
			</div>
			<!-- /inner content -->
		</div>
		<!-- /main content -->
		{/block}
	</div>
	<!-- /page content -->
	{block name='scripts'}
	{/block}
	{foreach from=$js_captures item=js_capture key=key name=name}
		{$js_capture}
	{/foreach}
	{if $complete_user_information|default:false}
		{#JS_JQUERY#|assets}
		{#JS_SWEETALERT#|assets}
		<script>
			$(() => {
				const swalInit = swal.mixin({
					buttonsStyling: false,
					customClass: {
						confirmButton: 'btn btn-success',
						cancelButton: 'btn btn-light',
						denyButton: 'btn btn-light',
						input: 'form-control'
					}
				});

				swalInit.fire({
					title: 'Please complete your information',
					text: "",
					icon: 'info',
					showCancelButton: false,
					confirmButtonText: 'Continue',
					buttonsStyling: false,
					customClass: {
						confirmButton: 'btn btn-success'
					}
				}).then(function(result) {
					window.location.href = '{$complete_user_information}';
				});
			});
		</script>
	{/if}
</body>
</html>
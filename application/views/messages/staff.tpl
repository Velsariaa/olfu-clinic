{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Message"}

{block name='content'}
	<div class="card">
		<div class="card-body">
			<div class="media-chat-scrollable mb-3">
				<div class="media-chat vstack gap-3">
					{foreach from=$messages item=message key=message_index}
						{if $message.user_type eq USER_TYPE_USER}
							<div class="media-chat-item">
								<div class="media-chat-message">{$message.message}</div>

								<div class="hstack gap-2 mt-2">
									<a href="#" class="d-inline-flex align-items-center justify-content-center bg-teal text-white lh-1 rounded-pill w-40px h-40px">
										<span class="letter-icon">{$message.icon}</span>
									</a>
									<a href="#" class="text-body fw-semibold letter-icon-title">{$message.name}</a>
									<div class="fs-sm text-muted">{$message.time_ago}</div>
								</div>
							</div>
						{else}
							<div class="media-chat-item media-chat-item-reverse me-3">
								<div class="media-chat-message bg-success">{$message.message}</div>

								<div class="hstack gap-2 flex-row-reverse mt-2">
									<a href="#" class="d-inline-flex align-items-center justify-content-center bg-teal text-white lh-1 rounded-pill w-40px h-40px">
										<span class="letter-icon">{$message.icon}</span>
									</a>
									<a href="#" class="text-body fw-semibold letter-icon-title">{$message.name}</a>
									<div class="fs-sm text-muted">
										{$message.time_ago}
									</div>
								</div>
							</div>
						{/if}
					{/foreach}
				</div>
			</div>

			<div id="txt-msg" class="form-control form-control-content mb-3" contenteditable="" data-placeholder="Type message here..."></div>

			<div class="d-flex align-items-center">
				<button id="btn-send" type="button" class="btn btn-success ms-auto">
					Send
					<i class="ph-paper-plane-tilt ms-2"></i>
				</button>
			</div>
		</div>
</div>
{/block}

{block name='styles'}
	<style type="text/css">

	</style>
{/block}

{block name='scripts'}
	{#JS_JQUERY#|assets}

	<script type="text/javascript">
		$(() => {
			$(document).on('click', '#btn-send', function ()
			{
				let msg = $('#txt-msg').html();

				var formData = new FormData();
				formData.append("message", msg);

				$.ajax({
					url: '{$save_url}',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: "json",
					success: function(result) {
						
					},
					error: function(result) {
						
					},
					complete: function() {
						window.location.reload();
					}
				});
			})
		});
	</script>
{/block}
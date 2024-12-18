{extends "`$smarty.current_dir`/profile.tpl"}

{block name='attributes'}
	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='first_name' value=$user.first_name|default:'' required=false disabled=true}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='last_name' value=$user.last_name|default:'' required=false disabled=true}
			</div>
		</div>
	</div>
{/block}
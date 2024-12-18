<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            {form_input enabled=true field='first_name' value=$profile_user.first_name|default:'' required=false disabled=$disable_first_name|default:true}
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {form_input enabled=true field='middle_name' value=$profile_user.middle_name|default:'' required=false disabled=$disable_middle_name|default:true}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            {form_input enabled=true field='last_name' value=$profile_user.last_name|default:'' required=false disabled=$disable_last_name|default:true}
        </div>
    </div>
</div>
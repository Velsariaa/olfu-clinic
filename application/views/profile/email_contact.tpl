<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            {form_input enabled=true field='email' value=$profile_user.email|default:'' required=false disabled=$disable_email|default:true}
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {form_input enabled=true field='contact' value=$profile_user.contact|default:'' required=false disabled=$disable_contact|default:true}
        </div>
    </div>
</div>
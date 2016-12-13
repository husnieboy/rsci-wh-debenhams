@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-user"></i>
    	<h3>{{ $heading_title_update }}</h3>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	{{ Form::model($user, array('url'=>'user/updateData', 'class'=>'form-horizontal', 'id'=>'form-users', 'role'=>'form', 'method' => 'post')) }}
		<div class="span3">&nbsp;</div>
		<div class="span7">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="username">{{ $entry_username }}</label>
					<div class="controls">
						{{ Form::text('username', $user->username, array('readonly' => 'readonly')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				<div class="control-group">
					<label class="control-label" for="firstname">{{ $entry_firstname }}</label>
					<div class="controls">
						{{ Form::text('firstname', null, array('maxlength'=>'50')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				<div class="control-group">
					<label class="control-label" for="lastname">{{ $entry_lastname }}</label>
					<div class="controls">
						{{ Form::text('lastname', null, array('maxlength'=>'50')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

			 

				<div class="control-group">
					<label class="control-label" for="role_name">{{ $entry_user_role }}</label>
					<div class="controls">
						{{ Form::select('role_id', $user_role_options, null, array('id'=>'user_role')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				 
					<div class="control-group">											
					<label class="control-label" for="filter_store">Store :</label>
					<div class="controls">
					{{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				 

				<div class="control-group">
					<label class="control-label" for=""></label>
					<div class="controls">
						<a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
						<a class="btn" href="{{ $url_cancel }}">{{ $button_cancel }}</a>
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
			</fieldset>
        </div>
        <div class="span2">&nbsp;</div>
        {{ Form::hidden('id', $user->id) }}
        {{ Form::hidden('filter_username', $filter_username) }} 
        {{ Form::hidden('filter_user_role', $filter_user_role) }}
        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}
        {{ Form::hidden('mode', 'user') }}

		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-users').submit();
    });

    $('#form-users input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-users').submit();
		}
	});

    toggleBarcodeElem();
	$('#user_role').click(function() {
    	toggleBarcodeElem();
    });

    function toggleBarcodeElem() {
    	var select_value = $('#user_role option:selected').text();
    	if(select_value == 'admin') $('#barcode-wrapper').hide();
    	else $('#barcode-wrapper').show();
    }
});
</script>
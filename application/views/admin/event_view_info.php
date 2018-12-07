<?php
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<!-- Page Content -->
<div class="right_col" role="main">
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
                        <h1 class="page-header"><?php echo $page_title;?></h1>
                        <?php if ( isset ( $event_details ) ) {?>
                        <div id="crud_output">
                        <div class='ui-widget-content ui-corner-all datatables'>
	<h3 class="ui-accordion-header ui-helper-reset ui-state-default form-title">
		<div class='floatL form-title-left'>
			<a href="#">Record Event</a>
		</div>
		<div class='clear'></div>
	</h3>
<div class='form-content form-div'>
	<form  method="post" id="crudForm"  enctype="multipart/form-data" accept-charset="utf-8">
		<div>
					<div class='form-field-box odd' id="title_field_box">
				<div class='form-display-as-box' id="title_display_as_box">
					Title :
				</div>
				<div class='form-input-box' id="title_input_box">
					<div id="field-title" class="readonly_label"><?php echo $event_details['title'];?></div>				</div>
				<div class='clear'></div>
			</div>
					<div class='form-field-box even' id="body_field_box">
				<div class='form-display-as-box' id="body_display_as_box">
					Body :
				</div>
				<div class='form-input-box' id="body_input_box">
					<div id="field-body" class="readonly_label"><?php echo $event_details['body'];?></div>				</div>
				<div class='clear'></div>
			</div>
					<div class='form-field-box odd' id="creating_user_id_field_box">
				<div class='form-display-as-box' id="creating_user_id_display_as_box">
					Creating user id :
				</div>
				<div class='form-input-box' id="creating_user_id_input_box">
					<div id="field-creating_user_id" class="readonly_label"><?php echo $event_details['created_time'];?></div>				</div>
				<div class='clear'></div>
			</div>
					<div class='form-field-box even' id="approved_field_box">
				<div class='form-display-as-box' id="approved_display_as_box">
					Approved :
				</div>
				<div class='form-input-box' id="approved_input_box">
					<div id="field-approved" class="readonly_label"><?php echo $event_details['approved'];?></div>				</div>
				<div class='clear'></div>
			</div>
					<div class='form-field-box odd' id="event_time_field_box">
				<div class='form-display-as-box' id="event_time_display_as_box">
					Event time :
				</div>
				<div class='form-input-box' id="event_time_input_box">
					<div id="field-event_time" class="readonly_label"><?php echo $event_details['event_time'];?></div>				</div>
				<div class='clear'></div>
			</div>
					<div class='form-field-box even' id="created_time_field_box">
				<div class='form-display-as-box' id="created_time_display_as_box">
					Created time :
				</div>
				<div class='form-input-box' id="created_time_input_box">
					<div id="field-created_time" class="readonly_label"><?php echo $event_details['created_time'];?></div>				</div>

			</div>
					<!-- Start of hidden inputs -->
							<!-- End of hidden inputs -->
							<div class='buttons-box'>
			<div class='form-button-box'>
				<input onclick="window.location = '<?php echo site_url('admin/events');?>' " type='button' value='Back to list' class='ui-input-button back-to-list' id="cancel-button" />
			</div>
			<div class='clear'></div>
		</div>
			<div id='report-error' class='report-div error'></div>
			<div id='report-success' class='report-div success'></div>
		</div>

	</form>
</div>
</div>

</div>
<?php }?>
					<?php if (isset ( $event_attendees )){?>
					<h2 class="page_header">Attendees</h2>
					<div> <?php echo $event_attendees->output;?></div>
					<?php }?>
					<?php if (isset ( $event_invitations )){?>
					<h2 class="page_header">Invitations</h2>

					<div> <?php echo $event_invitations->output;?></div>
					<?php }?>
					<?php if (isset ( $event_requests )){?>
					<h2 class="page_header">Requests</h2>

					<div> <?php echo $event_requests->output;?></div>
					<?php }?>

                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->

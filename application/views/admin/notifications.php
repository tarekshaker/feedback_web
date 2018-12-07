<!-- Page Content -->
 <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
 <link href="<?php echo base_url(); ?>assets/css/main.css" rel="stylesheet">
        <div id="page-wrapper">
         <?php //print_r($forms_status); ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                    
                        <h1 class="page-header">Push Notifications</h1>
                        <div id="crud_output">
                        	
        <div class="row flat">
            
<?php if($forms_status->app_config_status == '1'){?>
           <h5>App Config has Updates</h5><a href="<?php echo base_url()?>notifications/send_app_config_push">Send</a>
<?php }?> 
     
<?php if($forms_status->branch_status == '1'){?>
           <h5>Branch Details has Updates</h5><a href="<?php echo base_url()?>notifications/send_branch_push">Send</a>
<?php }?> 


<?php if($forms_status->default_categories_status == '1'){?>
           <h5>Default Categories have Updates</h5><a href="<?php echo base_url()?>notifications/send_default_cat_push">Send</a>
<?php }?> 


<?php if($forms_status->sub_categories_status == '1'){?>
           <h5>Sub Categories have Updates</h5><a href="<?php echo base_url()?>notifications/send_sub_cat_push">Send</a>
<?php }?> 

<?php if($forms_status->offers_status == '1'){?>
           <h5>Offers have Updates</h5><a href="<?php echo base_url()?>notifications/send_offers_push">Send</a>
<?php }?> 
 

<?php if($forms_status->new_items == '1'){?>
           <h5>New Items have Updates</h5><a href="<?php echo base_url()?>notifications/send_new_items_push">Send</a>
<?php }?> 


<?php if($forms_status->playstation_status == '1'){?>
           <h5>Playstation have Updates</h5><a href="<?php echo base_url()?>notifications/send_playstation_push">Send</a>
<?php }?>  

<?php if($forms_status->app_config_status == '0' &&  $forms_status->branch_status == '0' && $forms_status->default_categories_status == '0' &&  $forms_status->sub_categories_status == '0' && $forms_status->offers_status ==  '0' && $forms_status->new_items == '0' && $forms_status->playstation_status == '0'){?>
           <h5 style="color: green">No Push Notifications available</h5>
<?php }?>  


</div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
<?php

function DisplayLiveView(){

  $camera_settings_str = file_get_contents(RASPI_CAMERA_SETTINGS, true);
  $camera_settings_array = json_decode($camera_settings_str, true);

  $status = new StatusMessages();

  if ( $camera_settings_array['darkframe'] === '1'){
	$status->addMessage('Currently capturing dark frame. You can turn this off on the camera settings page.');
  }

  ?>
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-star fa-fw"></i>Local sky</div>
        <div class="panel-body">
			<p><?php $status->showMessages(); ?></p>
			<div id="live_container" style="background-color: black;">
      			<img id="current" class="current" src="current/liveview-<?php echo $camera_settings_array['filename'] ?>" 
				style="height:500; width:100%">
  			</div>
        </div><!-- /.panel-body -->
      </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->  
  <?php 
}

?>

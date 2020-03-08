<?php

function DisplaySkyMap(){

  $camera_settings_str = file_get_contents(RASPI_CAMERA_SETTINGS, true);
  $camera_settings_array = json_decode($camera_settings_str, true);

  $status = new StatusMessages();

  if ( $camera_settings_array['darkframe'] === '1'){
	$status->addMessage('Currently capturing dark frame. You can turn this off on the camera settings page.');
  }

  ?>

  <div class="row">
	<p><?php $status->showMessages(); ?></p>
	<div id="live_container" style="background-color: black;">
	  	<iframe width="75%" height="75%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 		src="https://virtualsky.lco.global/embed/index.html?longitude=-0.18&latitude=51&projection=polar&magnitude=18" allowTransparency="false"></iframe>
  	</div>
  </div>
  <?php 
}

?>

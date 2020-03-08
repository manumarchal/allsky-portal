<?php

include_once( 'includes/status_messages.php' );

function DisplaySkyMap(){
  $status = new StatusMessages();
?>
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-star fa-fw"></i>Local sky</div>
        <div class="panel-body">
	  	<iframe width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 		src="https://virtualsky.lco.global/embed/index.html?longitude=-0.18&latitude=51&projection=polar&magnitude=10" allowTransparency="false"></iframe>
        </div><!-- /.panel-body -->
      </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
<?php 
}

?>

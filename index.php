<?php

/**
 * AllSky Administration Panel
 *
 * Enables use of simple web interface rather than SSH to control a ZWO camera on the Raspberry Pi.
 * Uses code from RaspAP by Lawrence Yau <sirlagz@gmail.com> and Bill Zimmerman <billzimmerman@gmail.com>
 *
 * @author     Lawrence Yau <sirlagz@gmail.comm>
 * @author     Bill Zimmerman <billzimmerman@gmail.com>
 * @author     Thomas Jacquin <jacquin.thomas@gmail.com>
 * @license    GNU General Public License, version 3 (GPL-3.0)
 * @version    0.0.1
 * @link       https://github.com/thomasjacquin/allsky-portal
 */

define('RASPI_CONFIG', '/etc/raspap');
define('RASPI_ADMIN_DETAILS', RASPI_CONFIG.'/raspap.auth');
define('RASPI_CAMERA_SETTINGS', RASPI_CONFIG.'/settings.json');
define('RASPI_CAMERA_OPTIONS',  RASPI_CONFIG.'/camera_options.json');
define('RASPI_ALLSKY_DIR', 'RASPI_ALLSKY_DIR_PLACEHOLDER');

// Constants for configuration file paths.
// These are typical for default RPi installs. Modify if needed.
define('RASPI_DNSMASQ_CONFIG', '/etc/dnsmasq.conf');
define('RASPI_DNSMASQ_LEASES', '/var/lib/misc/dnsmasq.leases');
define('RASPI_HOSTAPD_CONFIG', '/etc/hostapd/hostapd.conf');
define('RASPI_WPA_SUPPLICANT_CONFIG', '/etc/wpa_supplicant/wpa_supplicant.conf');
define('RASPI_HOSTAPD_CTRL_INTERFACE', '/var/run/hostapd');
define('RASPI_WPA_CTRL_INTERFACE', '/var/run/wpa_supplicant');
define('RASPI_OPENVPN_CLIENT_CONFIG', '/etc/openvpn/client.conf');
define('RASPI_OPENVPN_SERVER_CONFIG', '/etc/openvpn/server.conf');
define('RASPI_TORPROXY_CONFIG', '/etc/tor/torrc');

// Optional services, set to true to enable.
define('RASPI_OPENVPN_ENABLED', false );
define('RASPI_TORPROXY_ENABLED', false );

include_once( RASPI_CONFIG.'/raspap.php' );
include_once( 'includes/functions.php' );
include_once( 'includes/dashboard.php' );
include_once( 'includes/liveview.php' );
include_once( 'includes/skymap.php' );
include_once( 'includes/authenticate.php' );
include_once( 'includes/admin.php' );
include_once( 'includes/dhcp.php' );
include_once( 'includes/hostapd.php' );
include_once( 'includes/system.php' );
include_once( 'includes/configure_client.php' );
include_once( 'includes/camera_settings.php' );
include_once( 'includes/days.php' );
include_once( 'includes/images.php' );
include_once( 'includes/videos.php' );
include_once( 'includes/keograms.php' );
include_once( 'includes/startrails.php' );

$output = $return = 0;
$page = $_GET['page'];

$camera_settings_str = file_get_contents(RASPI_CAMERA_SETTINGS, true);
$camera_settings_array = json_decode($camera_settings_str, true);

session_start();
if (empty($_SESSION['csrf_token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['csrf_token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Thomas Jacquin">

    <title>AllSky Admin Panel</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Font Awesome -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

    <!-- Custom CSS -->
    <link href="dist/css/custom.css" rel="stylesheet">

    <link rel="shortcut icon" type="image/png" href="img/allsky-favicon.png">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--><meta property="og:title" content="All Sky Camera" />
    <meta property="og:type" content="image/jpeg" />
    <meta property="og:url" content="http://www.thomasjacquin/allsky/" />
    <meta property="og:image" content="http://www.thomasjacquin.com/allsky/image.jpg" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=Ubuntu:400,300' rel='stylesheet' type='text/css'>-->
	<link rel="shortcut icon" type="image/png" href="allsky-favicon.png">
    <link rel="stylesheet" type="text/css" href="animate.min.css">
    <link rel="stylesheet" type="text/css" href="allsky.css">
    <link rel="stylesheet" type="text/css" href="allsky-font.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
            integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script src="virtualsky/virtualsky.js" type="text/javascript"></script>
    <script src="ng-lodash.min.js"></script>
    <script src="config.js"></script>
    <script src="controller.js"></script>
  </head>
  <body ng-app="allsky" ng-controller="AppCtrl">
    <div id="wrapper">
      <!-- Navigation -->
      <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">
		<img src="img/allsky-logo.png">
		<div class="navbar-title">AllSky Panel</div></a>
        </div>
        <!-- /.navbar-header -->

        <!-- Navigation -->
        <div class="navbar-default sidebar" role="navigation">
          <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
              <li><a href="index.php?page=live_view"><i class="fa fa-eye fa-fw"></i> Live</a></li>
              <li><a href="index.php?page=sky_map"><i class="fa fa-star fa-fw"></i> Sky</a></li>
	      	  <li><a href="index.php?page=list_days"><i class="fa fa-image fa-fw"></i> Images</a></li>
			  <li><a href="index.php?page=camera_conf"><i class="fa fa-camera fa-fw"></i> Camera</a></li>
              <li><a href="index.php?page=wlan0_info"><i class="fa fa-tachometer-alt fa-fw"></i> Connection</a></li>
              <li><a href="index.php?page=wpa_conf"><i class="fa fa-signal fa-fw"></i> Wifi</a></li>
              <li><a href="index.php?page=auth_conf"><i class="fa fa-lock fa-fw"></i> Password</a></li>
              <li><a href="index.php?page=system_info"><i class="fa fa-cube fa-fw"></i> System</a></li>
			  <!--<li><span onclick="switchTheme()"><i class="fa fa-moon fa-fw"></i> Light/Dark mode</span></li>
              <li><a href="index.php?page=hostapd_conf"><i class="fa fa-dot-circle-o fa-fw"></i> Configure hotspot</a></li>
              <li><a href="index.php?page=dhcpd_conf"><i class="fa fa-exchange fa-fw"></i> Configure DHCP</a></li>
              <?php if ( RASPI_OPENVPN_ENABLED ) : ?>
              <li><a href="index.php?page=openvpn_conf"><i class="fa fa-lock fa-fw"></i> Configure OpenVPN</a></li>
              <?php endif; ?>
              <?php if ( RASPI_TORPROXY_ENABLED ) : ?>
              <li><a href="index.php?page=torproxy_conf"><i class="fa fa-eye-slash fa-fw"></i> Configure TOR proxy</a></li>
              <?php endif; ?>-->
          </ul>
          </div><!-- /.navbar-collapse -->
        </div><!-- /.navbar-default -->
      </nav>

      <div id="page-wrapper">
	<div class="row right-panel">
		<div class="col-lg-12">
        <?php 
        // handle page actions
        switch( $page ) {
	  case "live_view":
            DisplayLiveView();
            break;
      case "sky_map":
            DisplaySkyMap();
            break;
  	  case "list_days":
	  		ListDays();
	        break;
	  case "wlan0_info":
            DisplayDashboard();
            break;
	  case "camera_conf":
            DisplayCameraConfig();
            break;
      case "wpa_conf":
            DisplayWPAConfig();
            break;
      case "auth_conf":
            DisplayAuthConfig($config['admin_user'], $config['admin_pass']);
            break;
      case "system_info":
            DisplaySystem();
            break;
	  case "list_images":
            ListImages();
            break;
      case "list_videos":
            ListVideos();
            break;
	  case "list_keograms":
            ListKeograms();
            break;
	  case "list_startrails":
            ListStartrails();
            break;
	  default:
            DisplayLiveView();
        }
        ?>
	</div>
	</div>
      </div><!-- /#page-wrapper --> 
    </div><!-- /#wrapper -->

    <!-- RaspAP JavaScript -->
    <script src="dist/js/functions.js"></script>

    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

	<script src="js/bigscreen.min.js"></script>

	<script type="text/javascript">
		function getImage(){
			var img = $("<img />").attr('src', 'current/liveview-<?php echo $camera_settings_array["filename"] ?>?_ts=' + new Date().getTime())
				.attr("id", "current")
				.attr("class", "current")
				.css("width", "100%")
				.on('load', function() {
				    if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) {
				        console.log('broken image!');
				        setTimeout(function(){
				            getImage();
				        }, 500);
				    } else {
				        $("#live_container").empty().append(img);
				    }
				});
		}

		$("#live_container").click(function(){
			if (BigScreen.enabled) {
				BigScreen.toggle(this, null, null, null);
			}
			else {
				console.log("Not Supported");
			}
		});

		setInterval(function(){
			getImage();
		}, <?php echo $camera_settings_array["exposure"] ?>);

		// Inititalize theme to dark
		if (!localStorage.getItem("theme")) {
			localStorage.setItem("theme", "dark")
		}

		$("body").attr("class", localStorage.getItem("theme"));

		function switchTheme() {
			if (localStorage.getItem("theme") === "light") {
				localStorage.setItem("theme", "dark");
			} else {
				localStorage.setItem("theme", "light");
			}
			$("body").attr("class", localStorage.getItem("theme"));
		}
	</script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>
  </body>
</html>

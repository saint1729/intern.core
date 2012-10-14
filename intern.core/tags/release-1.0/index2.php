<?php
  require_once("config/conf.inc.php");
  require_once("lib/user.php");
	$content = "login";
	if (isset($_GET["contenu"]))
	{
		$content = $_GET["contenu"];
	}
	if( is_readable( 'code/' . $content . '.php') ) require 'code/' . $content .'.php'; 
	include("./header.php");
	include("./title.php");
	if($_SESSION['ID'])
	{
		include("./menu.php");
    if (isAllowed($content))
      include("./content/$content.php");
    else
      include(userIndex());
	}
	else
	{
		include("./menu_client.php");
		include("./content_client/$content.php");
	}
	include("./foot.php");
?>

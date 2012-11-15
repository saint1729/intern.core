<?php
  require_once("lib/user.php");
  require_once("config/conf.inc.php");
	$content = "login";
	if (isset($_GET["contenu"]))
	{
		$content = $_GET["contenu"];
	}
	if( is_readable( 'code/' . $content . '.php') ) require 'code/' . $content .'.php'; 
	include("./header.php");
	include("./title.php");
	echo '</div><span class="clear">&nbsp;</span>';
	if($_SESSION['ID'])
	{
    if (isAllowed($content))
      include("./content/$content.php");
    else
      include(userIndex());
	}
	else
	{
		include("./content_client/$content.php");
	}
	include("./foot.php");

?>

<?php
	$cli_id=$_POST['cli_id'];
	$tab_affect = new liste_affect($cli_id);
	echo $tab_affect->display_menu($cli_id);
	echo $tab_affect->display();
?>

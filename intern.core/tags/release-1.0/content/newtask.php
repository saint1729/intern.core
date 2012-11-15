<?php
	// attribution des variable récupérer dans le formulaire
	$cli_id =$_POST['cli_id'];
	$use_id = $_POST['use_id'];
	$int_crea_use_id = $_POST['int_crea_use_id'];
	$int_description = $_POST['int_description'];
	$int_observation = $_POST['int_observation'];
	$int_date_butoire =  DateFrtoUs($_POST['int_date_butoire']);
	$int_type = $_POST['int_type'];
	$int_tps_passe = $_POST['int_tps_passe']*4;// on stocke le nb de demi-heure
	$int_status = $_POST['int_status'];
   // controle des variables   
   if(empty($int_description))
   {
   print("<center>La '<b>description</b>' est vide !</center>");
   exit();
   }
	// Conection à la base de donnée
	mysqlinforezo();
	//insertion des données dans la base
	$sql = "INSERT INTO T_INTERVENTION (INT_ID, CLI_ID, USE_ID, INT_CREA_USE_ID, INT_DESCRIPTION, INT_OBSERVATION, INT_DATE_BUTOIRE, INT_TYPE, INT_TPS_PASSE, INT_STATUS) VALUES('', '$cli_id', '$use_id', '$int_crea_use_id', '$int_description', '$int_observation', '$int_date_butoire', '$int_type', '$int_tps_passe', '$int_status')";
	$query = mysql_query($sql) or die( 'Erreur lors de l\'insertion des données la tâche n\'a pas été insérée' );
	// information pour l'utilisateur
	
	echo MakeTaskMenu().'La tâche vient d\'être insérée';
	
	mysql_close();
?>

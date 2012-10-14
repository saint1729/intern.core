<?php
	// attribution des variable récupérer dans le formulaire
	$fou_id =$_POST['fou_id'];
	$cli_id = $_POST['cli_id'];
	$fou_designation = $_POST['fou_designation'];
	$fou_status = $_POST['fou_status'];
	$dea_id = $_POST['dea_id'];
	$fou_reference =  $_POST['fou_reference'];
	$fou_qtt = $_POST['fou_qtt'];
	
   // controle des variables   
   if(empty($fou_designation))
   {
   print("<center>La '<b>designation</b>' est vide !</center>");
   exit();
   }
	// Conection à la base de donnée
	mysqlinforezo();
	//insertion des données dans la base
	$sql = "INSERT INTO T_FOURNITURE (FOU_ID, CLI_ID, FOU_DESIGNATION, FOU_STATUS, DEA_ID, FOU_REFERENCE, FOU_QTT) VALUES('', '$cli_id', '$fou_designation', '$fou_status', '$dea_id', '$fou_reference', '$fou_qtt')";
	$query = mysql_query($sql) or die( 'Erreur lors de l\'insertion des données la commande n\'a pas été insérée' );
	// information pour l'utilisateur
	
	echo 'La commande vient d\'être insérée'.$int_crea_use_id;
	mysql_close();
?>

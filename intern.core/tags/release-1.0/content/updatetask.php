<?php
	$int_id = $_POST['int_id'];
	$use_id = $_POST['use_id'];
	$int_description = $_POST['int_description'];
	$int_observation = $_POST['int_observation'];
	$int_date_butoire = DateFrToUs($_POST['int_date_butoire']);
	$int_status = $_POST['int_status'];
	$int_type = $_POST['int_type'];
	$int_tps_passe = $_POST['int_tps_passe']*4;// on repasse en nombre de demi-heures
   //connexion à la base de donnée 
   mysqlinforezo();
   
   if ($int_status== 'Terminé' ) $int_date_cloture=date('y-m-d');
	//récupération de l'ancienne valeur de rubrique
	$sql="UPDATE T_INTERVENTION SET USE_ID='$use_id', INT_DESCRIPTION='$int_description', INT_OBSERVATION = '$int_observation', INT_DATE_BUTOIRE = '$int_date_butoire', INT_STATUS='$int_status', INT_TYPE='$int_type', INT_DATE_CLOTURE='$int_date_cloture', INT_TPS_PASSE='$int_tps_passe' WHERE INT_ID=$int_id";
    $query = mysql_query($sql) or die( 'Erreur lors de la mise à jour des données'.$sql );
    echo "<div class=\"sumadmin\">Modification enregistrée</div>";
    mysql_close();
?>


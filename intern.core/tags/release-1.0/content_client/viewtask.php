<?php
	$vue = $_GET['vue'];
	$cli_id = $_SESSION['CLI_ID'];
	$table = new liste_inter;
	if ($vue!='formulaire')
	{
		$table->select($vue);
		echo $table->display_menu_inter_client();
		echo $table->display_liste_inter();
	}
	else
	{
		$temps=$_POST['TEMPS'];
		$tab_statut=$_POST['STATUS'];
		$date_debut=$_POST['date_debut'];
		$date_fin=$_POST['date_fin'];
		$User_liste = ListAllUsers();
		$table->select($vue, $temps, $tab_statut,$User_liste, $date_debut, $date_fin, $cli_id);
		echo $table->display_menu_inter_client($temps, $tab_statut, $date_debut, $date_fin);
		echo $table->display_liste_inter();
	}

/*
	if (isset($_GET["taches"]))
	{
		$taches=$_GET["taches"];
	}
	$use_id = $_SESSION['ID'];
	$use_login = $_SESSION['login'];
	// Connexion à la base de donnée
	mysqlinforezo();
   	// information pour l'utilisateur
   	if ($taches=='tout')
   	{
   		$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_CLIENT.CLI_SOCIETE, T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_ID, T_USER.USE_LOGIN, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID ORDER BY T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_DATE_CREA";
   		$caption='Toutes les tâches';
   	}
   	elseif ($taches =='afacturer')
   	{
   		$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_CLIENT.CLI_SOCIETE, T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_ID, T_USER.USE_LOGIN, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID  WHERE T_INTERVENTION.INT_STATUS='A facturer' ORDER BY T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_DATE_CREA";
   		$caption= 'Toutes les tâches à facturer';
   	}
   	elseif ($taches == 'toutesmestaches')
   	{
   		$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_CLIENT.CLI_SOCIETE, T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_ID, T_USER.USE_LOGIN, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID  WHERE T_INTERVENTION.USE_ID=$use_id ORDER BY T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_DATE_CREA";
   		$caption= 'Toutes mes tâches';
		}
   	else
   	{
   		$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_CLIENT.CLI_SOCIETE, T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_ID, T_USER.USE_LOGIN, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID  WHERE T_INTERVENTION.USE_ID=$use_id AND T_INTERVENTION.INT_STATUS!='Terminé' AND T_INTERVENTION.INT_STATUS!='A facturer' ORDER BY T_INTERVENTION.INT_DATE_BUTOIRE, T_INTERVENTION.INT_DATE_CREA";
   		$caption= 'Mes tâches à finir';
   	}
	$query = mysql_query($sql) or die( 'Erreur lors de la lecture des données'.$sql );
	
	echo $menu.'	
	<table border>
	<caption>'.$caption.'</caption>
		<tr>
			<th>Client</th>
			<th>Date</th>
			<th>Descrition</th>
			<th>Responsable</th>
			<th>Voir</th>
		</tr>
		';
	while ($dt=mysql_fetch_array($query))
	   {
	   		// Remplir le tableau des catégories	
					
					if ($dt['INT_STATUS']=='A traiter') echo '<tr><td bgcolor="#FFFFFF">';
					else echo '<tr> <td >';
					echo ($dt['CLI_SOCIETE']).'</td>
					<td>'.DateUsToFr($dt['INT_DATE_BUTOIRE']).'</td>
					<td>'.($dt['INT_DESCRIPTION']).'</td>
					<td>'.($dt['USE_LOGIN']).'</td>
					<td>
						<form method="post" action="./index.php?contenu=edittask">
                 			<input type="hidden" name="int_id" value ='.$dt['INT_ID'].'>
                 			<input type="submit" value="'.$dt['INT_STATUS'].'">
						</form>
				</tr>';
		}
	echo '</table>';
	mysql_close();
*/
?>

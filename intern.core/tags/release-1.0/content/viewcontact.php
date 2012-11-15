<?php
	$cli_id = $_POST['cli_id'];
	$action = $_POST['action'];
	$con_id = $_POST['con_id'];
	if ($action ==	'add_con')
	{
		$sql= "DELETE FROM L_CLI_CON WHERE CON_ID = '$con_id' AND CLI_ID='$cli_id';";
		$query= mysql_query ($sql) or die ("Suppression impossible: $sql");
		$sql= "INSERT INTO L_CLI_CON (CLI_CON_ID, CLI_ID, CON_ID) VALUES ('', '$cli_id', '$con_id');";
		$query= mysql_query ($sql) or die ("Insert impossible: $sql");
	}
	if ($action == 'rem_con')
	{
		$sql= "DELETE FROM L_CLI_CON WHERE CON_ID = '$con_id' AND CLI_ID='$cli_id';";
		$query= mysql_query ($sql) or die ("Suppression impossible: $sql");
	}
	
	$sql="SELECT * FROM (T_CONTACT INNER JOIN L_CLI_CON ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID) WHERE L_CLI_CON.CLI_ID=$cli_id order by CON_NOM;";
	$query= mysql_query ($sql) or die ("Select impossible: $sql");
	echo'<div class= "formadmin">
	<h2>Liste des responsables</h2>';
	while ($dt=mysql_fetch_array($query))
	{
		echo '<p> '.$dt['CON_CIVILITE'].' '.$dt['CON_PRENOM'].' '.$dt['CON_NOM'].' <br/>
						 Telephone: '.$dt['CON_TELEPHONE'].' <br/>
						 Fax: '.$dt['CON_TELECOPIE'].' <br/> 
						 Portable: '.$dt['PORTABLE'].'<br/>
						 <form method="post" action="./index.php?contenu=viewcontact">
								<input type = "hidden", name = "cli_id", value = "'.$cli_id.'"\>
								<input type = "hidden", name = "con_id", value = "'.$dt['CON_ID'].'"\>
								<input type = "hidden", name = "action", value = "rem_con";
								<input type="submit" value="Enlever">
						</form></p>';
	}
	echo'</div>';
	echo'<div class= "formadmin">
	<h2>Choisir un responsable</h2>';
	$sql = "SELECT T_CONTACT.CON_ID, CON_CIVILITE, CON_PRENOM, CON_NOM, CON_TELEPHONE, CON_TELECOPIE, CON_PORTABLE FROM (T_CONTACT LEFT JOIN L_CLI_CON ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID) WHERE L_CLI_CON.CLI_ID IS NULL OR L_CLI_CON.CLI_ID!=$cli_id ORDER by CON_NOM ;";
	$query= mysql_query ($sql) or die ("Select impossible: $sql");
	while ($dt=mysql_fetch_array($query))
	{
		echo '<p> '.$dt['CON_CIVILITE'].' '.$dt['CON_PRENOM'].' '.$dt['CON_NOM'].' <br/>
		 Telephone: '.$dt['CON_TELEPHONE'].' <br/>
		 Fax: '.$dt['CON_TELECOPIE'].' <br/>
		 Portable: '.$dt['CON_PORTABLE'].'<br/>
		 <form method="post" action="./index.php?contenu=viewcontact">
								<input type = "hidden", name = "cli_id", value = "'.$cli_id.'"\>
								<input type = "hidden", name = "con_id", value = "'.$dt['CON_ID'].'"\>
								<input type = "hidden", name = "action", value = "add_con";
								<input type="submit" value="Ajouter">
							</form></p>';
		 
	}
	echo'</div>';
	
	
	
	
	
?>

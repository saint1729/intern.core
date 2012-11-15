<?php
  if (isset($_GET['search'])) {
    $lettre = $_GET['search'];
  } else {
    $lettre = ''; 
  }
	$liste_client = new liste_client;
	$liste_client->select($lettre);
	echo'
	<div class="action">
		<form method = "get" action = "index.php">
			<input type = "hidden" name = "contenu" value = "adnewcustomer">
			<input type = "submit" value = "Nouveau Client">
		</form>
	</div>';
	echo $liste_client->display('$letter');

/*	// connexion à la base de donnée
	mysqlinforezo();
	// information pour l'utilisateur
	$sql = "SELECT * FROM T_CLIENT WHERE CLI_TYPE != 'Client perdu' ORDER BY CLI_SOCIETE;";
	$query = mysql_query($sql) or die( 'Erreur lors de la lecture des clients page wiew customer'.$sql );
	mysql_close();
	<table border>
	'.TITRE_CLIENT;	
		while ($dt=mysql_fetch_array($query))
		{
			// Remplir le tableau des catégories
			$client=new client($dt['CLI_ID']);
			echo $client->affiche_ligne();
		}
	echo'</table>';*/
?>

<?php
	if (isset($_GET["ID"]))
    {
    	$UID = $_GET["ID"];
    	echo"ok";
    	echo $UID;
    }
    else
    {
    echo'pas ok';
    }
   //connexion à la base de donnée 
   $serveur='localhost';
   $user='adminguidance';
   $pass='test';
   $db='guidance';
   mysql_connect($server, $user, $pass) or die('Erreur de connexion');
	mysql_select_db($db) or die('Base inexistante');
	//récupération de l'ancienne valeur de rubrique
	$sql="SELECT * FROM User WHERE UID='$UID';";
	$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données' );
	$list = mysql_fetch_array( $query );
	$oldname = $list['Ulogin'];
	echo"$sql <br> $oldname <br>";
	$sql="DELETE FROM User WHERE UID=$UID";
	echo"$sql";
    $query = mysql_query($sql) or die( 'Erreur lors de la mise à jour des données' );
    echo "l'utilisateur $oldname vient d'être supprimé";
    mysql_close();
?>


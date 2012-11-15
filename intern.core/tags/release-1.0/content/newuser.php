<?php
	// attribution des variable récupérer dans le formulaire
	$login = $_POST['login'];
	$password =md5( $_POST['password']);
	$password2 =md5( $_POST['password2']);
	$id_orga = $_POST['organisme'];

	// controle des variables   
	if(empty($login))
	{
		print("<center>Vous n'avez pas entré de login</center>");
		exit();
	}
	if(empty($password))
	{
		echo"<center>Vous n'avez pas entré de mot de passe</center>";
		exit();
	}
	if(empty($password2))
	{
		echo"<center>Vous devez répétez le nouveau mot de passe, pour éviter les faute de frappes </center>";
		exit();
	}
	if ($password != $password2)
	{
		echo"<center>Les 2 mots de passes ne sont pas identique, il y a probablement une faute de frappe</center>";
		exit();
	}

	//connexoin à la base de donnée
	$serveur='localhost';
	$user='adminguidance';
	$pass='test';
	$db='guidance';
	mysql_connect($server, $user, $pass) or die('Erreur de connexion');
	mysql_select_db($db) or die('Base inexistante');

	//vérification : est ce que le login existe déja?
	$sql="SELECT * FROM User WHERE (Ulogin='$login')";
	$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données' );
	$list = mysql_fetch_array( $query );
	if ($list['Ulogin']==$login)
	{
		Print("Ce login est déja pris");
	}
	else
	{
		 // insertion dans la base de donnée
		$sql = "INSERT INTO User (UID, Ulogin, Upwd, UOID) VALUES ('', '$login', '$password', '$id_orga')";
		$query = mysql_query($sql) or die( 'Erreur lors de l\'insertion des données' );
			
		// information pour l'utilisateur
		$id_user = mysql_insert_id();
		$sql = "SELECT User.UID, User.Ulogin, User.UOID, Organisme.ONom FROM User, Organisme WHERE UID=$id_user AND Organisme.OID=User.UOID";
		$query = mysql_query($sql) or die( 'Erreur lors de la lecture des données' );
		$list = mysql_fetch_array( $query );
		echo 'L\'utilisateur suivant vient d\'être insérée: <br> Identifiant utilisateur:'.$list['UID'].'<br> Login:'.$list['Ulogin'].'<br> Organisme: '.$list['ONom'];
	}
	mysql_close();
	
	include "adnewuser.php"
	
	
	
	
?>

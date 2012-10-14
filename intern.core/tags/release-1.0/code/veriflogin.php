<?php
	include "./lib/client.php";
	mysqlinforezo();
	if(isset($_POST) && !empty($_POST['login']) && !empty($_POST['pass']))
	{
		extract($_POST);
		// on recupère le password de la table qui correspond au login du visiteur
		$sql = "select * from T_USER where USE_LOGIN='".$login."'";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		$data = mysql_fetch_assoc($req);
		$ID = $data['USE_ID'];
		if($data['USE_PWD'] != md5($pass))	// l'utilisateur ne travaille pas chez inforezo
		{
			$sql = "select * from T_CLIENT where CLI_SOCIETE='".$login."'";
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			$data = mysql_fetch_assoc($req);
			if($data['CLI_PWD'] != md5($pass))	//l'utilisateur n'est un inconu
			{
				include("./header.php");
				include("./title.php");
				print("<p class=\"errlog\">Erreur d'identifiant ou de mot de passe</p>");
				include "./content/login.php";
				include("./foot.php");
		   		exit;
			}
			else //utilisateur est un client
			{
				session_start();
				$_SESSION['societe'] = $login;
				$_SESSION['pwd'] = md5($pass);
				$_SESSION['CLI_ID'] = $data['CLI_ID'];
				$login = $_SESSION['societe'];
			}
		}
		else //utilisateur travaille chez inforezo
		{
			session_start();
			$_SESSION['login'] = $login;
			$_SESSION['pwd'] = md5($pass);
			$_SESSION['ID'] = $ID;
			$_SESSION['prenom'] = $data['USE_PRENOM'];
			$_SESSION['nom'] = $data['USE_NOM'];
      $_SESSION['rights'] = $data['USER_RIGHTS'];
			$login = $_SESSION['login'];
		}   
	}
	else
	{
	include("./header.php");
	include("./title.php");
	print ("<p class=\"errlog\">Vous n'avez pas entré de mot de passe ou de nom d'utilisateur</p>");
	include "./content/login.php";
	include("./foot.php");
	exit;
	}
?>

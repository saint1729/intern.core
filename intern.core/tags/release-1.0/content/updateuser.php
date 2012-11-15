<?php
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$login = $_SESSION['login'];
	$pass = $_SESSION['pwd'];
	$ID = $_SESSION['ID'];
	
	if (!veriflogin ($login, $pass)) exit();

	// controle des variables   
	if(empty($login))
	{
		print("<center>Le '<b>Le login est vide</b>' est vide !</center>");
		exit();
	}
	if ($password != $password2)
	{
		echo'<center>Les 2 mots de passe ne sont pas iddentiques, il y a sans doute une faute de frappe</center>';	
		exit();
	}
	//connexion à la base de donnée 
	mysqlinforezo(); 
	//récupération de l'ancienne valeur de rubrique
	$sql="SELECT * FROM T_USER WHERE USE_ID='$userID';";
	$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données' );
	$list = mysql_fetch_array( $query );
	$oldlogin = $list['USE_LOGIN'];
	if(empty($password))
	{
		$password = $list['USE_PWD'];
		echo"le mot de passe n'a pas changé";
	}
	else
	{
		$password = md5($password);
		echo"le mot de passe a changé";
		$sql="UPDATE T_USER SET USE_PWD='$password'  WHERE USE_ID=$ID";
		$query = mysql_query($sql) or die( 'Erreur lors de la mise à jour des données '.$sql );
		echo "<div class=\"sumadmin\">le login $oldlogin vient d'être modifié, ses nouveau paramètres sont: <br>
		Login: $login ";
	}
	mysql_close();
?>

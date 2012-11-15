<?php
	$login = $_SESSION['login'];
	$pass = $_SESSION['pwd'];
    if (!veriflogin ($login, $pass)) exit();
	// Connexion à la base MYSQL
	mysqlinforezo(); 
	echo'
			<table border>
			<caption>Cette section vous permet de modifier votre login ou votre mot de passe</caption>
			<tr>
				<th>Login</th>
				<th>Modifier</th>
			</tr>';
			$sql = "SELECT USE_LOGIN, USE_PWD FROM T_USER WHERE USE_LOGIN ='$login'";
			$query = mysql_query($sql) or die( 'Erreur lors de la lecture des données' );
			$dt=mysql_fetch_array($query);
		    echo '
					<tr>
						<td>'.$dt['USE_LOGIN'].'</td>
						<td><a href="./index.php?contenu=edituser">Modifier </a>
					</tr>';
		echo'</table>';
		mysql_close();
?>

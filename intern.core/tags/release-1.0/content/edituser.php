<?php
	$login = $_SESSION['login'];
	$ID = $_SESSION['ID'];
	$pass = $_SESSION['pwd'];
	if (!veriflogin ($login, $pass, $UOID))
	{
		echo "tentative de triche";
		 exit();
	}
	//connexion à la base de donnée 
	mysqlinforezo();   

	
	//vérification : est ce que l'utilisateur existe?
	$sql="SELECT * FROM T_USER WHERE USE_ID='$ID';";
	echo "id: $ID";
	$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données' );
	$list = mysql_fetch_array( $query );
	$login = $list['USE_LOGIN'];
	$password ='';
	echo 
		'<div class="formadmin">
			<form method="post" action="./index.php?contenu=updateuser">
				<h2>Editer l\'utilisateur</h2>
				<p>Login : '. $login.'</p>
				<p>Mot de passe: <input type="password" name="password" size="12" value ="" maxlength=50></p>
			    <p>Répétez le mot de passe: <input type="password" name="password2" size="12" value = ""maxlength=50></p>
			    <input type="hidden" name="ID" value ='.$ID.'>
			    <input type="submit" value="OK">
			</form>
		</div> ';
	mysql_close();
?>



    

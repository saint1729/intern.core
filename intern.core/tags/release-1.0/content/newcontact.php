<?php
	$con_prenom = $_POST['con_prenom'];
	$con_nom =$_POST['con_nom'];
	$con_civilite =$_POST['con_civilite'];
	$con_email =$_POST['con_email'];
	$con_telephone =$_POST['con_telephone'];
	$con_telecopie =$_POST['con_telecopie'];
	$con_portable = $_POST['con_portable'];
	echo '<div class="sumadmin">';
   if(empty($con_nom))
   {
   print("<center>Le '<b>Nom du Contact</b>' est vide !</center></div>");
   exit();
   }
   if(empty($con_telephone))
   {
   print("<center>Avertissement: Le '<b>numéro de telephone </b>' est vide !</center>");
   }
   if(empty($con_telecopie))
   {
   print("<center>Avertissement: Le '<b>numéro de fax</b>' est vide !</center>");
   }
	if(empty($con_portable))
   {
   print("<center>Avertissement: Le '<b>numéro de portable</b>' est vide !</center>");
   }
   if(empty($con_email))
   {
   print("<center>Avertissement: L' '<b>email</b>' est vide !</center>");
   }
  echo '</div>';
  
  // connexion à la base de donnée
	mysqlinforezo();
	//vérification : est ce que la sous rubrique existe déja?
	$sql="SELECT Count(*) AS NbContact FROM T_CONTACT WHERE (CON_NOM='$con_nom') AND (CON_PRENOM='$con_prenom');";
	$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données' );
	$list = mysql_fetch_array( $query );
	if ($list['NbContact']!=0)
	{
		Print("Ce contact existe déja");
	}
	else
	{
		$sql = "INSERT INTO T_CONTACT (CON_ID,CON_NOM, CON_PRENOM, CON_TELEPHONE, CON_TELECOPIE, CON_CIVILITE, CON_EMAIL, CON_PORTABLE) VALUES('', '$con_nom', '$con_prenom', '$con_telephone', '$con_telecopie', '$con_civilite', '$con_email', '$con_portable')";
		$query = mysql_query($sql) or die( 'Erreur lors de l\'insertion des données'.$sql );
		// information pour l'utilisateur 
		$cli_id = mysql_insert_id();
	echo '<div class="sumadmin">Le contact suivant vient d\'être inséré: <br> Identifiant contact:'.$con_id.'<br/> Nom'.$con_nom.'<br/>Prenom: '.$con_prenom.'<br/>Téléphone: '.$con_telephone.'<br>Fax: '.$con_telecopie.' <br>Civilité: '.$con_civilite.'</div>';
	mysql_close();
	}
	
	
		include "adnewcustomer.php"
?>

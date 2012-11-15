<div class="formadmin">
   <form method="post" action="./index.php?contenu=newuser">
      <h2>Nouvel utilisateur</h2>
      <p>Login : <input type="text" name="login" size="12" maxlength=50></p>
      <p>Mot de passe: <input type="password" name="password" size="12" maxlength=50></p>
      <p>Répétez le mot de passe: <input type="password" name="password2" size="12" maxlength=50></p>
      <p>
            Organisme associé: 
            <select name="organisme">
               <?php
					$serveur='localhost';
					$user='adminguidance';
					$pass='test';
					$db='guidance';
					mysql_connect($server, $user, $pass) or die('Erreur de connexion');
					mysql_select_db($db) or die('Base inexistante');
					$sql = 'SELECT OID, ONom FROM Organisme order by ONom;';
					$query = mysql_query($sql) or die( 'Erreur' );
					$nb = mysql_num_rows($query);
					if ( $nb=0 )
					{
						echo '<option>Aucun organisme</option>';
					}
					else
					{  
						while ( $list = mysql_fetch_array( $query ) )
						{
							echo '<option value="'.$list['OID'].'">'.$list['ONom'].'</option>';
						}
					}
					mysql_close();
				?>
				<option value=0>Super Utilisateur </option>
			</select>
		</p>   
		<input type="submit" value="OK">
	</form>
</div> 
 

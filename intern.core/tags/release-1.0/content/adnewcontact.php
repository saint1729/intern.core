<div class="formadmin">
  	<form method="post" action="./index.php?contenu=newcontact">
    <h2>Nouveau contact</h2>
     <p>Prénom: <input type="text" name="con_prenom" size="12" maxlength=20></p>
     <p>Nom: <input type="text" name="con_nom" size="12" maxlength=20></p>
     <p>Telephone: <input type="text" name="con_telephone" size="12" maxlength=20></p>
     <p>Portable: <input type="text" name="con_portable" size ="12" maxlenght=20</p>
     <p>Fax : <input type="text" name="con_telecopie" size="12" maxlength=20></p>
     <p>Email : <input type="text" name="con_email" size="30"maxlength=200></p>
     <p>Civilité 		
		<?php
			$listetypes = funcEnumList("T_CONTACT", "CON_CIVILITE");
			$formselect = funcMakeFormList( 'con_civilite', $listetypes, 'Monsieur');
			echo $formselect; 
		?>
		</p>
   	<input type="submit" value="OK">
   </form>
</div>

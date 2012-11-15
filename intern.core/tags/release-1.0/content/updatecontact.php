<?php
	if (!empty($_POST))
	{
    if (isset($_POST['con_id'])) {
      $contact = new contact($_POST['con_id']);
    } else {
      $contact = new contact('');
    }
		$contact->con_responsable = $_POST['con_responsable'];
		$contact->con_nom = $_POST['con_nom'];
		$contact->con_prenom = $_POST['con_prenom'];
		$contact->con_email = $_POST['con_email'];
		$contact->con_portable = $_POST['con_portable'];
		$contact->con_autre = $_POST['con_autre'];
		
		if (isset($_POST['Supprimer']))
		{
			$contact->delete();
			$h1 = 'Contact supprimé';
      $sumary='';
		}
		elseif (isset($_POST['Modifier']))
		{
			if ($contact->test4enreg())
			{
				$contact->enreg();
				$h1= 'Contact modifié';
			}
			else
			{
				$h1= 'Mise a jour impossible';
			}
      $sumary='';
		}
		elseif (isset($_POST['Ajouter']))
		{
			$cli_id = $_POST['cli_id'];
			$contact->enreg($cli_id);
			relier($cli_id, $contact->con_id);
			$h1 = 'Contact Ajouté';
			$sumary = $contact->warning();
		}
		else
		{
			$h1= 'Erreur';
		}
		
	}
	echo '<div class="bandeau"><h1>'.$h1.'</h1></div><div class="sumadmin">'.$sumary.'</div>';
	
?>

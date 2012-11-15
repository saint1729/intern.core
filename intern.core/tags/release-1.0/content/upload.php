<?php
$cli_id = $_POST['cli_id'];
if( isset($_POST['upload']) ) // si formulaire soumis
{
	$content_dir = 'files/'.$cli_id.'/'; // dossier où sera déplacé le fichier

	$tmp_file = $_FILES['fichier']['tmp_name'];

	if( !is_uploaded_file($tmp_file) )
	{
		exit("Le fichier est introuvable");
	}

// on vérifie maintenant l'extension
	$type_file = $_FILES['fichier']['type'];

// on copie le fichier dans le dossier de destination
	$name_file = $_FILES['fichier']['name'];
	if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
	{
		exit("Impossible de copier le fichier dans $content_dir");
	}
	echo "Le fichier a bien été uploadé (atttention la sécurité des fichier stocké ici est plutôt mauvaise)";
}
?>

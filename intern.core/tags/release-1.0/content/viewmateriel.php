<?php
if (isset($_GET["status"]))
{
	$status=$_GET["status"];
}
else
{
	$status='recu';
}


if ($status== 'recu')
{
	$status = 'Reçu';
	$link='<a href="./index.php?contenu=viewmateriel&status=livre">Voir le materiel livré</a>';
}
elseif ($status =='livre')
{
	$status = "Livré";
	$link='<a href="./index.php?contenu=viewmateriel&status=recu">Voir le materiel reçu</a>';
}


echo'
	<div class="nouveau">
		<a href="./index.php?contenu=adnewfourniture">Nouvelle commande</a>
		'.$link.'
	</div>';

	echo ShowFourniture($status);
?>

<?php
if (isset($_GET["status"]))
{
	$status=$_GET["status"];
}
else
{
	$status='acommander';
}


if ($status== 'acommander')
{
	$status = 'A commander';
	$link='<a href="./index.php?contenu=viewfourniture&status=commande">Commandes en attente</a>';
}
elseif ($status =='commande')
{
	$status = "Commandé";
	$link='<a href="./index.php?contenu=viewfourniture&status=acommander">A commander</a>';
}


echo'
	<div class="nouveau">
		<a href="./index.php?contenu=adnewfourniture">Nouvelle commande</a>
		'.$link.'
	</div>';

	echo ShowFourniture($status);
?>

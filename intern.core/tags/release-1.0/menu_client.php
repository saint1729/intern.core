<?php
	$id = $_SESSION['CLI_ID'];
	echo"
		<div id=\"menu\">
			<ul id=\"$content\">
				<li id=\"li-customer\"><a href=\"./index2.php?contenu=viewcustomers\">Profil</a></li>
				<li id=\"li-task\"><a href=\"./index2.php?contenu=viewtask&vue=client&cli_id=$id\">Interventions</a></li>
				<li id=\"li-devis\"><a href=\"./index2.php?contenu=view_devis&vue=client&cli_id=$id\">Devis</a></li>
				<li id=\"li-facture\"><a href=\"./index2.php?contenu=view_factures&vue=client&cli_id=$id\">Factures</a></li>
				<li id=\"li-quitter\"><a href=\"./index.php?contenu=quitter\">Quitter</a></li>
			</ul>
		</div>
	</div>
	<span class='clear'>&nbsp</span>
	";
?>

<?php

echo"
	<div id=\"menu\">
		<ul id=\"$content\">";
      if ($_SESSION['rights'] > 0)
        echo "<li id=\"li-customer\"><a href=\"./index2.php?contenu=viewcustomers\">Clients</a></li>";
			echo "<li id=\"li-task\"><a href=\"./index2.php?contenu=viewtask\">Interventions</a></li>";
      if ($_SESSION['rights'] > 1)
        echo "<li id=\"li-devis\"><a href=\"./index2.php?contenu=view_devis\">Devis</a></li>";
      if ($_SESSION['rights'] > 2)
        echo "<li id=\"li-facture\"><a href=\"./index2.php?contenu=view_factures\">Factures</a></li>";
    //  echo "<li id=\"li-affectation\"><a href=\"./index2.php?contenu=view_affect\">Affectation</a></li>";
      if ($_SESSION['rights'] > 3)
        echo " <li id=\"li-admin\"><a href=\"./index2.php?contenu=admin\">Administration</a></li>";
      echo "
			<li id=\"li-quitter\"><a href=\"./index.php?contenu=quitter\">Quitter</a></li>
		</ul>
	</div>
</div>
<span class='clear'>&nbsp</span>\n ";
?>

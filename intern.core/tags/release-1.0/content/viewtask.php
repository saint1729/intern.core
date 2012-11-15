<SCRIPT LANGUAGE="JavaScript">
	var checkflag = "false";

function check(checkboxname) 
{
	var tab = document.getElementsByName(checkboxname);
	if (checkflag == "false") 
	{
		for (i = 0; i<tab.length; ++i) 
		{
			tab[i].checked = true;
		}
		checkflag = "true";
		return "Tout décocher"; 
	}
	else
	{
		for (i = 0; i < tab.length; ++i) 
		{
			tab[i].checked = false; 
		}
		checkflag = "false";
	       return "Tout cocher"; 
	}
}
</script>
<?php
	if (isset($_GET['vue'])) {
    $vue = $_GET['vue']; 
  } else {
    $vue ='';
  }
	$table = new liste_inter;
	if ($vue!='formulaire')
	{
		$table->select($vue);
		echo $table->display_menu_inter();
		echo $table->display_liste_inter();
    $table->createCalendar();
	}
	else
	{
		$temps=$_POST['TEMPS'];
		$tab_statut=$_POST['STATUS'];
		$tab_technicien=$_POST['TECHNICIEN'];
		$date_debut= $_POST['date_debut'];
		$date_fin= $_POST['date_fin'];
		$cli_id = $_POST['cli_id'];
		$table->select($vue, $temps, $tab_statut, $tab_technicien, $date_debut, $date_fin, $cli_id);
		echo $table->display_menu_inter($temps,$tab_statut, $tab_technicien, $date_debut, $date_fin, $cli_id);
		echo $table->display_liste_inter();
	}
?>

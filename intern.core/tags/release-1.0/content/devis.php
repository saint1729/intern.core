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
	if (empty($_POST))
	{
		if (isset($_GET['cli_id']))
		{
			$cli_id = $_GET['cli_id'];
			$devis = new devis(array("CLI_ID" => "$cli_id", "DEV_ACOMPTE" => "30"));
			$devis->tab_lde[] = new ligne_devis;
		}
		elseif (isset($_GET['dev_id']))
		{
			$dev_id = intval($_GET['dev_id']);
			$devis = new devis($dev_id);
	    $table = new liste_inter;
      $table->select('devis','','','','','','',$dev_id);
      $dispInters = $table->display_liste_inter(false);
		}
	}
	else
	{
		$param = stripslashes($_POST['objet_devis']);
		$devis = unserialize($param);

		if(!$_POST['back_from_preview'] && !$_POST['copy'])
			$devis->set_form_values($_POST);
	  if ($_POST['copy']) {
      unset($devis->dev_id);
      $devis->client = new client($_POST['cli_id']);
      $devis->client->init_all_contact();
    }
		if($_POST['switch'])
		{
			$devis->switch = !$devis->switch;
		}
		elseif($_POST['new_line'])
		{
			$devis->tab_lde[] = new ligne_devis;
		}
		elseif($_POST['save'])
		{
			echo $devis->enreg();
		}
		elseif($_POST['back'])
		{
			include('content/view_devis.php');
			exit;
		}
		elseif($_POST['preview'])
		{
			echo $devis->preview();
			echo $devis->write_pdf();
			exit;
		}
		elseif($_POST['print'])
		{
			exit;
		}
		else
		{
			for($i=0; $i <= count($devis->tab_lde) ; $i++)
			{
				if($_POST['suppr'.$i]) {
					unset($devis->tab_lde[$i]);
					$devis->tab_lde = array_values($devis->tab_lde);
				}
        if ($_POST['insert'.$i]) {
          $line = new ligne_devis;
          $devis->tab_lde = insertArrayIndex($devis->tab_lde, $line, $i);
        }
			} 
		}
	}
	echo $devis->edit();
  echo $dispInters;
  echo $devis->assignForm();
  echo $devis->copyForm();
?>

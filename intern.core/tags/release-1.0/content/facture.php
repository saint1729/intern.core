<?php
	if (empty($_POST))
	{
		if (isset($_GET['cli_id']))
		{
			$cli_id = $_GET['cli_id'];
			$facture = new facture(array("CLI_ID" => "$cli_id"));
			$facture->tab_lfa[] = new ligne_facture;
		}
		elseif (isset($_GET['fac_id']))
		{
			$fac_id = intval($_GET['fac_id']);
			$facture = new facture($fac_id);
		}
	}
	else
	{
		$param = stripslashes($_POST['objet_facture']);
		$facture = unserialize($param);

		if(!$_POST['back_from_preview'])
			$facture->set_form_values($_POST);
	
		if($_POST['new_line'])
		{
			$facture->tab_lfa[] = new ligne_facture;
		}
		elseif($_POST['save'])
		{
			echo $facture->enreg();
		}
		elseif($_POST['back'])
		{
			include('content/view_factures.php');
			exit;
		}
		elseif($_POST['preview'])
		{
			echo $facture->preview();
			echo $facture->write_pdf();
			exit;
		}
		elseif($_POST['print'])
		{
			exit;
		}
		else
		{
			for($i=0; $i <= count($facture->tab_lfa) ; $i++)
			{
				if($_POST['suppr'.$i])
				{
					unset($facture->tab_lfa[$i]);
					$facture->tab_lfa = array_values($facture->tab_lfa);
				}
			} 
		}
	}
	echo $facture->edit();
?>

<?php
	class affectation
	{
		var $type;
		var $qtt;
		var $SN;
		var $designation;
		var $no_cmd;
		var $client;
		
		function affectation($cli_id=0)
		{
			if ($cli_id)
			{
				$this->client = new client($cli_id);
			}
		}
		
		function init_from_liste($liste)
		{
			$this->type=$liste["FOU_TYPE"];
			$this->qtt=$liste["FOU_QTT"];
			$this->SN=$liste["FOU_SN"];
			$this->designation=$liste["FOU_DESIGNATION"];
			$this->no_cmd=$liste["FOU_NO_CMD"];
			$this->client=new client();
			$this->client->cli_id=$liste["CLI_ID"];
			$this->client->cli_societe=$liste["CLI_SOCIETE"];
		}

		function get_type()
		{
			if (empty($this->type)) return '&nbsp;';
			else return $this->type;
		}
		function get_qtt()
		{
			if (empty($this->qtt)) return '&nbsp;';
			else return $this->qtt;
		}
		function get_SN()
		{
			if (empty($this->SN)) return '&nbsp;';
			else return $this->SN;
		}
		function get_designation()
		{
			if (empty($this->designation)) return '&nbsp;';
			else return $this->designation;
		}
		function get_no_cmd()
		{
			if (empty($this->no_cmd)) return '&nbsp;';
			else return $this->no_cmd;
		}

		function add_new()
		{
			$result='
<div class="bandeau"><h1>Affectation</h1><h2>Nouvelle</h2> </div>
<span class="clear"> &nbsp;</span>
<div class="formadmin">
	<form name="formAffect" method="post" action="./index2.php?contenu=new_affect">
		<h2>'.$this->client->cli_societe.'</h2>
		<div><label for="designation">Désignation</label><input type = "text" name = "designation" value="" id="designation" size="100"> </div>
		<div><label for="SN">Numéro de Série</label><input type = "text" name = "SN" value="" id="SN"> </div>
		<div><label for="no_cmd">Numéro de commande</label><input type = "text" name = "no_cmd" value="" id="no_cmd"> </div>
		<div>
			<label for="materiel">Materiel</label><input type="radio" name="type" value="Materiel" id="materiel" checked onClick="document.formAffect.qtt.disabled = true;">
			<label for="logiciel">Logiciel</label><input type="radio" name="type" value="Logiciel" id="logiciel" onClick="document.formAffect.qtt.disabled = true;">
			<label for="licence">Licence</label><input type="radio" name="type" value="Licence" id="licence" onClick="document.formAffect.qtt.disabled = false;">
		</div>
		<div><label for="qtt">Quantité</label><input type = "text" name = "qtt" value="1" id="qtt" disabled> </div>
		<input type = "hidden" name = "cli_id" value = "'.$this->client->cli_id.'">
		<div><input style="float: right;" type="submit" value="OK"></div>
	</form>
	<form method="post"action="./index2.php?contenu=showcustomer&cli_id='.$this->client->cli_id.'">
		<input style="float: left;" type="submit" value="Retour">
	</form>
	<span class="clear">&nbsp;</span>
</div>
			';
			return $result;
		}
		function enreg()
		{
			$raison="";
			if (empty($this->client->cli_id)) $raison= "<p>Pas d'identifiant client pour l'affectation</p>";
			if (empty($this->designation)) $raison.= "<p>Pas de désignation du produit</p>";
			if (empty($this->SN)) $avertissement.= "<p>Pas de numéro de série pour le produit</p>";
			if (empty($this->no_cmd)) $raison.= "<p>Pas de numéro commande pour le produit</p>";
			if (empty($this->qtt)) $this->qtt=1;
			if (empty($this->type)) $raison .="<p>Pas de type de produit</p>";
			if (!empty ($raison)) return "<div class=\"formadmin\"><h1>Affectation non enregistrée</h1> $raison</div>";
			$cli_id = $this->client->cli_id;
			$sql = "INSERT INTO T_FOURNITURE (FOU_ID, CLI_ID, FOU_DESIGNATION, FOU_SN, FOU_QTT, FOU_TYPE, FOU_NO_CMD) VALUES ('', '$cli_id', '$this->designation', '$this->SN', '$this->qtt', '$this->type', '$this->no_cmd');";
			mysqlinforezo();
			$query = mysql_query($sql) or die(sql_error('affectation', 'enreg', 1, $sql));
			return "<div class=\"formadmin\"><h1>Affectation enregistrée</h1> <h2>$Avertissement.</h2></div>";
		}
		function display_line()
		{
			$DISPLAY = '<tr><td>'.$this->client->get_cli_societe().'</td><td>'.$this->get_qtt().'</td><td>'.$this->get_type().'</td><td>'.$this->get_no_cmd().'</td><td>'.$this->get_SN().'</td><td>'.$this->get_designation().'</td></tr>';
		return $DISPLAY;
		}
	}
	
	class liste_affect
	{
		var $tab_affectation;
		
		function liste_affect($cli_id='')
		{
			if ($cli_id != '')
			{
			$where = "WHERE T_FOURNITURE.CLI_ID=$cli_id";
			}
			$sql="SELECT FOU_ID, FOU_DESIGNATION, FOU_SN, FOU_QTT, FOU_TYPE, FOU_NO_CMD, T_CLIENT.CLI_SOCIETE from T_FOURNITURE INNER JOIN T_CLIENT ON T_FOURNITURE.CLI_ID=T_CLIENT.CLI_ID $where ORDER BY T_CLIENT.CLI_SOCIETE;";
			
			mysqlinforezo();
			$query = mysql_query($sql) or die (sql_error('liste_affect', 'liste_affect', 1, $sql));
			while ($liste = mysql_fetch_array($query))
			{
				$affect = new affectation;
				$affect->init_from_liste($liste);
				$this->tab_affectation[] = $affect;
			}
		}

		function display()
		{
			$result='
	<table border>
		<caption>
			Affectations	
		</caption>
		<tr><th>Client</th><th>Qtt</th><th>type</th><th>N° cmd Techdata</th><th>Numéro Série</th><th>Designation</th></tr>
		';
			if (empty($this->tab_affectation))
			{
				$result= 'Aucune Affectation pour ce client';
			}
			else
			{
				foreach ($this->tab_affectation as $affect)
				{
					$result.= $affect->display_line();
				}
				$result.= '</table>';
			}
			return $result;
		}
		function display_menu($cli_id='')
		{
			$DISPLAY='
	<div class="nouveau">
		<form method="post" action="./index2.php?contenu=view_affect">
			<div>	
				'.MakeSelectCustomers($cli_id).'
			</div>
			<div>
				<input type="submit" value="Chercher">
			</div>
		</form>
	</div>';
	return $DISPLAY;
		}
	}
?>

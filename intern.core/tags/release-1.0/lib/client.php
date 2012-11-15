<?php
// cette classe necessite la classe contact
include("contact.php");
//include("user.php");
include("interventions.php");

function MakeSelectCustomers($Default_Customer_ID='tous')
{
	mysqlinforezo();
	$sql = 'SELECT CLI_ID, CLI_SOCIETE FROM T_CLIENT WHERE CLI_TYPE != "Client perdu" order by CLI_SOCIETE;';
	$query = mysql_query($sql) or die(sql_error('client','MakeSelectCustomers('.$Default_Customer_ID.')', 1, $sql));
	$nb = mysql_num_rows($query);
	$SELECT_USERS='<select name="cli_id">';
	if ($Default_Customer_ID=='tous') {
    $u="selected";
  } else {
    $u = "";
  }
		$SELECT_USERS=$SELECT_USERS.'<option value="tous" '.$u.'>Tous les clients</option>';
	while ( $list = mysql_fetch_array( $query ) )
	{
		$u="";
	 	if ($list['CLI_ID'] == $Default_Customer_ID) $u="selected";
		$SELECT_USERS=$SELECT_USERS.'<option value="'.$list['CLI_ID'].'" '.$u.'>'.$list['CLI_SOCIETE'].'</option>';
	}
	$SELECT_USERS=$SELECT_USERS.'</select>';
	mysql_close();
	return $SELECT_USERS;
}


class client
{
// déclaration des variables de classe
	var $cli_id;
	var $cli_societe;
	var $cli_adresse;
	var $cli_code_postal;
	var $cli_ville;
	var $cli_telephone;
	var $cli_telecopie;
	var $cli_siret;
	var $cli_type;
	var $cli_tva;
	var $cli_maintenance;
	var $cli_echeances;
	var $cli_echeances_abo;
  var $cli_premiere_echeance;
	var $cli_tech_id;
	var $cli_mtt_maintenance;
  var $cli_rezo_box;
  var $cli_rezo_backup;
	var $cli_nb_facture;
	var $cli_pwd;
  var $cli_nb_poste;
	var $contact;
	var $all_contact;
//methodes de classe
//constructeur: si l'id est 0 on crée un objet vide
//		si l'id est différente de 0 on se connecte à la base de donné pour récupérer les valeurs de l'objet
	function client($cli_id=0)
	{
		if ($cli_id==0)
		{
			$this->cli_id = 0;
		}
		else
		{
			mysqlinforezo();
			$sql = "SELECT CLI_ID, CLI_SOCIETE, CLI_ADRESSE, CLI_CODE_POSTAL, CLI_VILLE, CLI_TELEPHONE, CLI_TELECOPIE, CLI_TYPE, CLI_NO_TVA, CLI_MAINTENANCE, CLI_ECHEANCES, CLI_TECH_ID, CLI_NB_FACTURE, CLI_REZO_BOX, CLI_REZO_BACKUP, CLI_MTT_MAINTENANCE, CLI_ECHEANCES_ABO, CLI_SIRET, CLI_PREMIERE_ECHEANCE, CLI_NB_POSTE FROM T_CLIENT WHERE CLI_ID = '$cli_id';";
			$query=mysql_query($sql) or die (sql_error('client','client (constructeur)','1',$sql));
			mysql_close();
			$list=mysql_fetch_array($query);
			$this->cli_id = $cli_id;
			$this->cli_societe= $list['CLI_SOCIETE'];
			$this->cli_adresse= $list['CLI_ADRESSE'];
			$this->cli_code_postal= $list['CLI_CODE_POSTAL']; 
			$this->cli_ville= $list['CLI_VILLE'];
			$this->cli_telephone= $list['CLI_TELEPHONE'];
			$this->cli_telecopie= $list['CLI_TELECOPIE'];
			$this->cli_type= $list['CLI_TYPE'];
			$this->cli_tva= $list['CLI_NO_TVA'];
			$this->cli_maintenance= $list['CLI_MAINTENANCE'];
			$this->cli_echeances= $list['CLI_ECHEANCES'];
			$this->cli_tech_id= $list['CLI_TECH_ID'];
			$this->cli_nb_facture= $list['CLI_NB_FACTURE'];
			$this->cli_rezo_box= $list['CLI_REZO_BOX'];
			$this->cli_rezo_backup= $list['CLI_REZO_BACKUP'];
			$this->cli_mtt_maintenance= $list['CLI_MTT_MAINTENANCE'];
      $this->cli_rezo_box = $list['CLI_REZO_BOX'];
      $this->cli_rezo_backup = $list['CLI_REZO_BACKUP'];
			$this->cli_echeances_abo= $list['CLI_ECHEANCES_ABO'];
      $this->cli_premiere_echeance = $list['CLI_PREMIERE_ECHEANCE'];
			$this->cli_siret= $list['CLI_SIRET'];
      $this->cli_nb_poste = $list['CLI_NB_POSTE'];
		}
		$this->contact_liste=array();
		$this->all_contact=array();
	}
	
	function init_from_liste($liste)
	{
		$this->cli_id = $liste["CLI_ID"];
		$this->cli_societe = $liste["CLI_SOCIETE"];
		$this->cli_adresse = $liste["CLI_ADRESSE"];
		$this->cli_ville = $liste["CLI_VILLE"];
		$this->cli_telephone = $liste["CLI_TELEPHONE"];
		$this->cli_telecopie = $liste["CLI_TELECOPIE"];
		$this->cli_type = $liste["CLI_TYPE"];
		$this->cli_tva = $liste["CLI_NO_TVA"];
		$this->cli_maintenance = $liste["CLI_MAINTENANCE"];
		$this->cli_echeances = $liste["CLI_ECHEANCES"];
		$this->cli_echeances_abo = $liste["CLI_ECHEANCES_ABO"];
    $this->cli_premiere_echeance = $liste['CLI_PREMIERE_ECHEANCE'];
		$this->cli_tech_id = $liste["CLI_TECH_ID"];
		$this->cli_mtt_maintenance = $liste["CLI_MTT_MAINTENANCE"];
    $this->cli_rezo_box = $liste['CLI_REZO_BOX'];
    $this->cli_rezo_backup = $liste['CLI_REZO_BACKUP'];
		$this->cli_nb_facture = $liste["CLI_NB_FACTURE"];
		$this->cli_pwd = $liste["CLI_PWD"];
    $this->cli_nb_poste = $liste["CLI_NB_POSTE"];
		$this->contact = new contact;
		$this->contact->con_id = $liste['CON_ID'];
		$this->contact->con_nom = $liste['CON_NOM'];
		$this->contact->con_prenom = $liste['CON_PRENOM'];
		$this->contact->con_email = $liste['CON_EMAIL'];
		$this->contact->con_portable = $liste['CON_PORTABLE'];
		$this->contact->con_responsable = $liste['CON_RESPONSABLE'];
	}

	function init_all_contact()
	{
		$sql="Select CON_ID from L_CLI_CON WHERE CLI_ID='$this->cli_id'";
		mysqlinforezo();
		$query=mysql_query($sql) or die (sql_error('client', 'init_all_contact', 1, $sql));
		mysql_close();
		if (mysql_num_rows($query)==0)
		{
			 $this->all_contact = NULL;
		}
		else
		{
			while($liste=mysql_fetch_array($query))
			{
				$this->all_contact[]= new contact($liste['CON_ID']);
			}
		}
		
	}
	
	function get_cli_id()
	{
		return $this->cli_id;
	}
	
	function get_cli_societe()
	{
		if(empty($this->cli_societe))
			$result='&nbsp;';
		else
			$result=$this->cli_societe;
		return $result;
	}

	function get_cli_adresse()
	{
		if(empty($this->cli_adresse))
			$result='&nbsp;';
		else
			$result=$this->cli_adresse;
		return $result;
	}

	function get_cli_code_postal()
	{
		if(empty($this->cli_code_postal))
			$result='&nbsp;';
		else
			$result=$this->cli_code_postal;
		return $result;
	}

	function get_cli_ville()
	{
		if(empty($this->cli_ville))
			$result='&nbsp;';
		else
			$result=$this->cli_ville;
		return $result;
	}
	
	function get_cli_telephone()
	{
		if(empty($this->cli_telephone))
			$result='&nbsp;';
		else
			$result=$this->cli_telephone;
		return $result;
	}

	function get_cli_siret()
	{
		if(empty($this->cli_siret))
			$result='&nbsp;';
		else
			$result=$this->cli_siret;
		return $result;
	}

	function get_cli_telecopie()
	{
		if(empty($this->cli_telecopie))
			$result='&nbsp;';
		else
			$result=$this->cli_telecopie;
		return $result;
	}
	
	function get_cli_type()
	{
		if(empty($this->cli_type))
			$result='&nbsp;';
		else
			$result=$this->cli_type;
		return $result;
	}

	function get_cli_tva()
	{
		if(empty($this->cli_tva))
			$result='&nbsp;';
		else
			$result=$this->cli_tva;
		return $result;
	}

	function get_cli_maintenance()
	{
		if(empty($this->cli_maintenance))
			$result='&nbsp;';
		else
			$result=$this->cli_maintenance;
		return $result;
	}
	
	function get_cli_rezo_box()
	{
		if(empty($this->cli_rezo_box) or $this->cli_rezo_box==0)
			$result='Non';
		else
			$result=$this->cli_rezo_box.' € / ans';
		return $result;
	}

	function get_cli_rezo_backup()
	{
		if(empty($this->cli_rezo_backup) or $this->cli_rezo_backup==0)
			$result='Non';
		else
			$result=$this->cli_rezo_backup.' € / ans';
		return $result;
	}

	function get_cli_mtt_maintenance()
	{
		if(empty($this->cli_mtt_maintenance) or $this->cli_mtt_maintenance==0)
			$result='&nbsp';
		else
			$result=$this->cli_mtt_maintenance.' € / ans';
		return $result;
	}
	
	function get_cli_echeances_abo()
	{
		if(empty($this->cli_echeances_abo))
			$result='&nbsp;';
		else
			$result=$this->cli_echeances_abo;
		return $result;
	}

  function get_cli_premiere_echeance()
  {
    if (empty($this->cli_premiere_echeance))
      $result = '&nbsp;';
    else
      $result = $this->cli_premiere_echeance;
    return ($result);
  }

	function get_cli_echeances()
	{
		if(empty($this->cli_echeances))
			$result='&nbsp;';
		else
			$result=$this->cli_echeances;
		return $result;
	}
	
	function get_cli_technicien()
	{
		if(empty($this->cli_tech_id))
			$result='aucun technicien par défaut';
		else
		{
			mysqlinforezo();
			$sql = "SELECT USE_PRENOM, USE_NOM FROM T_USER WHERE USE_ID='$this->cli_tech_id'; ";
			$query = mysql_query($sql) or die(sql_error('client','get_cli_technicien','1',$sql) );
			$liste = mysql_fetch_array($query);
			$result = $liste['USE_PRENOM'].' '.$liste['USE_NOM'];
			mysql_close();
		}
		return $result;
		
	}
	
	function get_cli_tech_id()
	{
		if(empty($this->cli_tech_id))
			$result='&nbsp;';
		else
			$result=$this->cli_tech_id;
		return $result;
	}
	
	function get_cli_nb_facture()
	{
		if(empty($this->cli_nb_facture))
			$result='0';
		else
			$result=$this->cli_nb_facture;
		return $result;
	}

	function get_cli_a_savoir($cle='')
	{
		if (empty($cle))
			$result='NO_KEY';
		else
		{
			$sql="select AES_DECRYPT(CLI_A_SAVOIR,'$cle') as CLI_A_SAVOIR from T_CLIENT where CLI_ID=$this->cli_id;";
			mysqlinforezo();
			$query = mysql_query($sql) or die (sql_error('client','get_cli_a_savoir', 1, $sql));
			$liste = mysql_fetch_array($query);
			$a_savoir = $liste['CLI_A_SAVOIR'];
			if ($a_savoir)
				$result=$a_savoir;
			else
				$result='NO_PASS';
			mysql_close();
		}
		return $result;
	}
	
	function show_a_savoir($cle='')
	{
		$a_savoir=$this->get_cli_a_savoir($cle); 
		if ($a_savoir == 'NO_KEY')
		{
			$SHOW_A_SAVOIR = 'Pas de clé entrée';
		}
		elseif($a_savoir == 'NO_PASS')
		{
			$SHOW_A_SAVOIR = 'Mauvaise clé';
		}
		else
		{
			$text= $a_savoir;
			$SHOW_A_SAVOIR='
			<div class="formadmin">
				<h2>A savoir</h2>
				<form method="post" action="./index2.php?contenu=update_a_savoir">
					<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
					<textarea rows="15" cols="100" name = "a_savoir">'.$text.'</textarea>
					<span class="clear">&nbsp;</span>
					<label for="cle1">Clé: </label><input type="password" name="cle" id="cle1">
					<input type="submit" value="Valider les modifications">
				</form>
				<form method="post" action="./index2.php?contenu=showcustomer">
					<input type="hidden" name = "cli_id" value = "'.$this->cli_id.'">
					<input type="submit" value="Retour">
				</form>
				<form method="get" action="./files/'.$this->cli_id.'">
					<input type="submit" value="Fichiers client">
				</form>
				<form method="post" enctype="multipart/form-data" action="index2.php?contenu=upload">
				<p>
				<input type="file" name="fichier" size="30">
				<input type="hidden" name="cli_id" value="'.$this->cli_id.'">
				<input type="submit" name="upload" value="Uploader">
				</p>
				</form>
			</div>
			';
			
		}
		return $SHOW_A_SAVOIR;
	}
	
	function set_a_savoir($a_savoir, $cle)
	{
		mysqlinforezo();
		$sql="select AES_DECRYPT(KEY_KEY,'$cle') as CLI_A_SAVOIR from T_KEY;";
		$query = mysql_query($sql) or die (sql_error('client','set_cli_a_savoir', 1, $sql));
		$liste = mysql_fetch_array($query);
		$test_cle = $liste['CLI_A_SAVOIR'];
		if ($test_cle)
		{
			$sql="update T_CLIENT set CLI_A_SAVOIR=AES_ENCRYPT('$a_savoir', '$cle') WHERE CLI_ID=$this->cli_id";
			$query = mysql_query($sql) or die (sql_error('client','set_cli_a_savoir', 2, $sql));
			$result= "Données mises à jour";
		}
		else
		{
			$result="Données non mises à jours (sans doute que la clé n'est pas la bonne";
		}
		return $result;
	}
	
	function enreg($vue='')
	{
		mysqlinforezo();
		if ($this->cli_id!="")	// update
		{
			if($vue=='')
			{
				$sql = "UPDATE T_CLIENT SET CLI_SOCIETE='$this->cli_societe', CLI_ADRESSE='$this->cli_adresse', CLI_CODE_POSTAL='$this->cli_code_postal', CLI_VILLE='$this->cli_ville', CLI_TELEPHONE='$this->cli_telephone', CLI_TELECOPIE='$this->cli_telecopie', CLI_SIRET='$this->cli_siret', CLI_TYPE='$this->cli_type', CLI_NO_TVA='$this->cli_tva', CLI_MAINTENANCE='$this->cli_maintenance', CLI_ECHEANCES='$this->cli_echeances', CLI_TECH_ID='$this->cli_tech_id', CLI_NB_FACTURE='$this->cli_nb_facture', CLI_REZO_BOX='$this->cli_rezo_box', CLI_REZO_BACKUP='$this->cli_rezo_backup', CLI_MTT_MAINTENANCE='$this->cli_mtt_maintenance', CLI_ECHEANCES_ABO='$this->cli_echeances_abo', CLI_PREMIERE_ECHEANCE='$this->cli_premiere_echeance', CLI_NB_POSTE='$this->cli_nb_poste' WHERE CLI_ID=$this->cli_id;";
			}
			elseif($vue=='vue_client')
			{
				if ($this->cli_pwd != '')
				{
					$insert_pass = ", CLI_PWD='$this->cli_pwd'";
				}
				$sql = "UPDATE T_CLIENT SET CLI_SOCIETE='$this->cli_societe', CLI_ADRESSE='$this->cli_adresse', CLI_CODE_POSTAL='$this->cli_code_postal', CLI_VILLE='$this->cli_ville', CLI_TELEPHONE='$this->cli_telephone', CLI_TELECOPIE='$this->cli_telecopie'".$insert_pass." WHERE CLI_ID=$this->cli_id;";
			}
		$query = mysql_query($sql) or die(sql_error('client','enreg','1',$sql) );
		}
		else		// insert
		{
			$sql = "SELECT KEY_KEY from T_KEY;";
			$query = mysql_query($sql) or die (sql_error('client', 'enreg', '2', $sql));
			$liste= mysql_fetch_array($query);
			$a_savoir= $liste['KEY_KEY'];
			$sql = "INSERT INTO T_CLIENT (CLI_ID, CLI_SOCIETE, CLI_ADRESSE, CLI_CODE_POSTAL, CLI_VILLE, CLI_TELEPHONE, CLI_TELECOPIE, CLI_SIRET, CLI_TYPE, CLI_NO_TVA, CLI_MAINTENANCE, CLI_ECHEANCES, CLI_TECH_ID, CLI_NB_FACTURE, CLI_NB_POSTE, CLI_A_SAVOIR) VALUES ('','$this->cli_societe', '$this->cli_adresse', '$this->cli_code_postal', '$this->cli_ville', '$this->cli_telephone', '$this->cli_telecopie', '$this->cli_siret', '$this->cli_type', '$this->cli_tva', '$this->cli_maintenance', '$this->cli_echeances', '$this->cli_tech_id', '$this->cli_nb_facture', '$this->cli_nb_poste',(SELECT KEY_KEY FROM T_KEY));";
		$query = mysql_query($sql) or die(sql_error('client','enreg','3',$sql) );
		$this->cli_id=mysql_insert_id();
		}
		mysql_close();
	}
	
  function get_cli_responsable()
  {
    $resp = new contact($this->responsable());
    return ($resp->get_prenom_nom());

  }
	function responsable()
	{
		mysqlinforezo();
		$sql="SELECT CON_ID FROM L_CLI_CON WHERE CLI_ID='$this->cli_id' AND CON_ID IN (SELECT CON_ID FROM T_CONTACT WHERE CON_RESPONSABLE=1);";
		$query=mysql_query($sql);
		$liste=mysql_fetch_array($query);
		$responsable=$liste['CON_ID'];
		return $responsable;
	}

	function MakeSelectContact($Default_Contact_ID, $Name="dev_contact")
	{
		$SELECT_CONTACT='<select name="'.$Name.'" id="'.$Name.'">';
		foreach ($this->all_contact as $contact)
		{
			$u="";
	 		if ($contact->con_id == $Default_Contact_ID) $u="selected";
			$SELECT_CONTACT.='<option value="'.$contact->con_id.'" '.$u.'>'.$contact->con_prenom.' '.$contact->con_nom.'</option>';
		}
		$SELECT_CONTACT.='</select>';
		return $SELECT_CONTACT;
	}
	
	function show_all_contact()
	{
		mysqlinforezo();
		$sql="Select CON_ID from L_CLI_CON WHERE CLI_ID='$this->cli_id'";
		$query=mysql_query($sql) or die ('erreur fonction show_all_contact'.$sql);
		mysql_close();
		if (mysql_num_rows($query)==0)
		{
			$SHOW_ALL_CONTACT='
			<span class="clear">&nbsp;</span>
			Aucun Personnel associé à ce client';
		}
		else
		{
			$SHOW_ALL_CONTACT=' 
			<span class="clear">&nbsp;</span>
			<h2> Personnels </h2>
			<div class="tableau">
			'.
			TITRE_CONTACT.
			'<div class="corps">'; 
			while($liste=mysql_fetch_array($query))
			{
				$contact=new contact($liste['CON_ID']);
				$SHOW_ALL_CONTACT= $SHOW_ALL_CONTACT.$contact->show();
			}
		$SHOW_ALL_CONTACT=$SHOW_ALL_CONTACT.'</div></div>';
		}
		
		return $SHOW_ALL_CONTACT;
	}
	
	function edit_all_contact()
	{
		$EDIT_ALL_CONTACT='
		<span class="clear">&nbsp;</span>
		<h2> Personnels </h2>
		<div class="tableau">
		'.
		TITRE_CONTACT.
		'<div class="corps">'; 
		mysqlinforezo();
		$sql="Select CON_ID from L_CLI_CON WHERE CLI_ID='$this->cli_id'";
		$query=mysql_query($sql) or die ('erreur fonction edit_all_contact'.$sql);
		mysql_close();
		while($liste=mysql_fetch_array($query))
		{
			$contact=new contact($liste['CON_ID']);
			$EDIT_ALL_CONTACT=$EDIT_ALL_CONTACT.$contact->edit();
		}
		$contact=new contact();
		if ($this->cli_id==0)
		{
			$EDIT_ALL_CONTACT=$EDIT_ALL_CONTACT.$contact->edit('','NOUVEAU');
		}
		else
		{
			$EDIT_ALL_CONTACT=$EDIT_ALL_CONTACT.$contact->edit($this->get_cli_id());
		}	
		$EDIT_ALL_CONTACT=$EDIT_ALL_CONTACT.'</div></div>';
		return $EDIT_ALL_CONTACT;
	}
	
	function select_technicien()
	{
		return MakeSelectUsers($this->cli_tech_id);
	}
	
	function edit($vue='')
	{
		$listetypes = funcEnumList("T_CLIENT", "CLI_TYPE");
		$selecttype = funcMakeFormList( 'cli_type', $listetypes, $this->cli_type, 'typeclient');
		
		$listetypes = funcEnumList("T_CLIENT", "CLI_ECHEANCES");
		$selectecheances = funcMakeFormList( 'cli_echeances', $listetypes, $this->cli_echeances, 'echeances');

		$listetypes = funcEnumList("T_CLIENT", "CLI_MAINTENANCE");
		$selectmaintenance = funcMakeFormList( 'cli_maintenance', $listetypes, $this->cli_maintenance , 'maintenance');
		
		$listetypes = funcEnumList("T_CLIENT", "CLI_ECHEANCES_ABO");
		$SelectEcheancesAbo= funcMakeFormList( 'cli_echeance_abo', $listetypes, $this->cli_echeances_abo , 'echeances_abo');

		$listetypes = funcEnumList("T_CLIENT", "CLI_PREMIERE_ECHEANCE");
    $Select1Echeance = funcMakeFormList( 'cli_premiere_echeance', $listetypes , $this->cli_premiere_echeance, 'premiere_echeance');
		
		if ($this->cli_id==0)
		{
			$TITRE='<h2>Nouveau Client</h2>';
			$END_FORM_NOUV='
			<span class="clear">&nbsp;</span>
			<div class="validation">
				<input type="submit" value="Nouveau client">
			</form>
			<span class="clear">&nbsp;</span>
			<form method="post" action="./index2.php?contenu=viewcustomers">
				<input type="submit" value="Retour">
			</form>
			</div>';
			$END_FORM_OLD='';
			$SELECT_USER=MakeSelectUsers(2);
		}
		else
		{	
			$TITRE='<h2>Editer le client</h2>';
			$END_FORM_NOUV='';
			$END_FORM_OLD='
		<span class="clear">&nbsp;</span>		
               		<p><input type="submit" value="Valider les modifications du client"></p>
	</form>
		<span class="clear">&nbsp;</span>		
	<form method="post" action="./index2.php?contenu=viewcustomers">
		<input type="submit" value="Retour">
	</form>';
	
		}
		if ($vue == '')
		{
			$vue_subjective='
		<div><label for="typeclient">Type de client : </label> '.$selecttype.'</div>
		<div><label for="tva">Numéro TVA : </label> <input type="text" name="cli_tva" size="12" value = "'.$this->cli_tva.'"maxlength=30 id="tva"></div>
		<div><label for="maintenance">Maintenance : </label> '.$selectmaintenance.'</div> 
		<div><label for="echeances">Échéances : </label> '.$selectecheances.'</div> 
		<div><label for="technicien">Technicien : </label> '.$this->select_technicien().'</div> 
		<div><label for="factures">Nombre de factures : </label> <input type="text" name="cli_nb_facture" size="50" value = "'.$this->cli_nb_facture.'" maxlength=5 id="factures"></div>
		<div> <label for="rezo_box">Abonement Rézo Box : </label> <input type="text" name="cli_rezo_box" size="5" value = "'.$this->cli_rezo_box.'" maxlength="6" id="rezo_box"> € / ans</div>
		<div> <label for="rezo_backup">Abonement Rézo Backup : </label> <input type="text" name="cli_rezo_backup" size="5" value="'.$this->cli_rezo_backup.'" maxlength="6" id="rezo_backup"> € / ans</div>
		<div> <label for="mtt_maintenance">Abonement Maintenance : </label> <input type="text" name="cli_mtt_maintenance" size="7" value="'.$this->cli_mtt_maintenance.'" maxlength="7" id="mtt_maintenance"> € / ans</div>
    <div> <label for="nb_poste">Nombre de postes : </label> <input type="text" name="cli_nb_poste" size="5" value="'.$this->cli_nb_poste.'" maxlength="5" id="nb_poste"</div>
		<div> <label for="echeances_abo">Echéances abonements : </label> ' . $SelectEcheancesAbo . '</div>
		<div> <label for="premiere_echeance">Premiere échéance : </label> ' . $Select1Echeance . '</div>
			';
		}
		elseif ($vue == 'vue_client')
		{
			$vue_subjective='
		<div><label for="pass1">Mot de passe : </label> <input type="password" name="pass1" size="12" value = "" maxlength=30 id="pass1"></div>
		<div><label for="pass2">Mot de passe (encore) : </label> <input type="password" name="pass2" size="12" value = "" maxlength=30 id="pass2"></div>
			';
		}
		$EDIT_CLIENT='
<span class="clear">&nbsp;</span>		
<div class="formadmin">
	<form method="post" action="./index2.php?contenu=updatecustomer">
		'.$TITRE.'
		<div><label for="societe" color="red">Société* : </label> <input type="text" name="cli_societe" size="50" value = "'.$this->cli_societe.'"maxlength=50 id="societe"></div>
		<div><label for="adresse">Adresse :</label> <input type="text" name="cli_adresse" size="50" value = "'.$this->cli_adresse.'"maxlength=200 id="adresse"></div>
		<div><label for="code_postal">Code postal : </label> <input type="text" name="cli_code_postal" size="12" value = "'.$this->cli_code_postal.'"maxlength=5 id="code_postal"></div>
		<div><label for="ville">Ville* : </label> <input type="text" name="cli_ville" size="50" value = "'.$this->cli_ville.'"maxlength=20 id="ville"></div>
		<div><label for="telephone">Téléphone : </label> <input type="text" name="cli_telephone" size="50" value = "'.$this->cli_telephone.'"maxlength=20 id="telephone"></div>
		<div><label for="fax">Fax : </label> <input type="text" name="cli_telecopie" size="50" value = "'.$this->cli_telecopie.'"maxlength=20 id="fax"></div>
		<div><label for="siret">Siret : </label> <input type="text" name="cli_siret" size="50" value = "'.$this->cli_siret.'"maxlength=20 id="siret"></div>
		'.$vue_subjective.'
                <input type="hidden" name="cli_id" value = "'.$this->get_cli_id().'">
	'.$END_FORM_OLD
 .$this->edit_all_contact().$END_FORM_NOUV.'<span class="clear">&nbsp;</span></div>';
		return $EDIT_CLIENT;
	}

	function change_pass()
	{
		$result='
<div class="formadmin">
	<form method="post" action="./index2.php?contenu=updatecustomer">
		<div><label for="pass1">Mot de passe : </label> <input type="password" name="pass1" size="12" value = "" maxlength=30 id="pass1"></div>
		<div><label for="pass2">Mot de passe (encore) : </label> <input type="password" name="pass2" size="12" value = "" maxlength=30 id="pass2"></div>
                <input type="hidden" name="cli_id" value = "'.$this->get_cli_id().'">
		<input type="hidden" name="cli_telecopie" value = "'.$this->cli_telecopie.'">
		<input type="hidden" name="cli_telephone" value = "'.$this->cli_telephone.'">
		<input type="hidden" name="cli_ville" value = "'.$this->cli_ville.'">
		<input type="hidden" name="cli_code_postal" value = "'.$this->cli_code_postal.'">
		<input type="hidden" name="cli_adresse" value = "'.$this->cli_adresse.'">
		<input type="hidden" name="cli_societe" value = "'.$this->cli_societe.'">
		<input type="submit" value="Ok">
		';
		return $result;
	}
	
	function show($vue='')
	{
		$sql="SELECT USE_PRENOM FROM T_USER WHERE USE_ID ='$this->cli_tech_id'";
		mysqlinforezo();
		$query = mysql_query($sql) or die( 'Erreur SQL Classe Client, Fonction show()'.$sql );
		mysql_close();
		$liste=mysql_fetch_array($query);
		$technicien=$liste['USE_PRENOM'];
    $new_inter='
      <form method="post" action="./index.php?contenu=add_inter">
        <input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">';
    if ($this->cli_maintenance == 'Oui')
      $new_inter .= ' <input type="submit" name="new_inter" value="Nouvelle Intervention">';
    $new_inter .= ' <input type="submit" name="inter_fac" value="Intervention payante">';
    $new_inter .=	'</form>';
		if($vue=='')
		{
		$new_facture='
			<form method="get" action="./index.php"> 
				<input type = "hidden" name = "contenu" value = "facture">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Nouvelle facture" class="grisage">
			</form>
		';
		$view_facture='	
			<form method="get" action="./index2.php">
				<input type = "hidden" name = "contenu" value = "view_factures">
				<input type = "hidden" name = "vue" value = "client">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Voir les factures" class="grisage">
			</form>
		';
		$new_devis='	
			<form method="get" action="./index.php">
				<input type = "hidden" name = "contenu" value = "devis">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Nouveau Devis">
			</form>
		';
		$view_devis='	
			<form method="get" action="./index2.php">
				<input type = "hidden" name = "contenu" value = "view_devis">
				<input type = "hidden" name = "vue" value = "client">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Voir les devis">
			</form>
		';

		$view_inter='
			<form method="get" action="./index2.php">
				<input type = "hidden" name = "contenu" value = "viewtask">
				<input type = "hidden" name = "vue" value = "client">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Voir les interventions">
			</form>
		';
			$info='
		<div class="etiquette">Type de client : </div> <div class="produit"> '.$this->get_cli_type().'</div>
		<div class="etiquette">Numéro TVA : </div> <div class="produit"> '.$this->get_cli_tva().'</div>
		<div class="etiquette">Maintenance : </div> <div class="produit"> '.$this->get_cli_maintenance().'</div> 
		<div class="etiquette">Échéances : </div> <div class="produit"> '.$this->get_cli_echeances().'</div> 
		<div class="etiquette">Technicien : </div> <div class="produit"> '.$this->get_cli_technicien().'</div> 
		<div class="etiquette">Nombre de factures : </div> <div class="produit"> '.$this->get_cli_nb_facture().'</div>
		<div class="etiquette">Clé : </div>
		<div class="produit"> 
			<form method="post" action="./index2.php?contenu=a_savoir">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type = "password" name = "cle" value="">
				<input type="submit" value="A savoir">
			</form>
		</div>';
			$new_affect='
			<form method="post" action="./index.php?contenu=add_affect">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Nouvelle Affectation">
			</form>
			';
			$view_affect='
			<form method="post" action="./index2.php?contenu=view_affect">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type="submit" value="Voir les affectations">
			</form>
			';

		}
		elseif($vue=='vue_client')
		{
			$info='
		<div class="etiquette">Maintenance : </div> <div class="produit"> '.$this->get_cli_maintenance().'</div> 
		<div class="etiquette">Technicien : </div> <div class="produit"> '.$this->get_cli_technicien().'</div> ';
		}
		$SHOW_CLIENT='
		<div class="action">
		'.$new_inter.$view_inter.$new_affect.$view_affect.$new_devis.$view_devis.$new_facture.$view_facture.'
		</div>
		<div class="presentation">
		<span class="clear">&nbsp;</span>
		<h2>Voir le client</h2>
		<div class="etiquette">Société : </div><div class="produit"> '.$this->get_cli_societe().'</div>
		<div class="etiquette">Adresse : </div><div class="produit"> '.$this->get_cli_adresse().'</div>
		<div class="etiquette">Code postal : </div> <div class="produit"> '.$this->get_cli_code_postal().'</div>
		<div class="etiquette">Ville : </div> <div class="produit"> '.$this->get_cli_ville().'</div>
		<div class="etiquette">Téléphone : </div> <div class="produit"> '.$this->get_cli_telephone().'</div>
		<div class="etiquette">Fax : </div> <div class="produit"> '.$this->get_cli_telecopie().'</div>
		<div class="etiquette">Siret : </div> <div class="produit"> '.$this->get_cli_siret().'</div>
		<div class="etiquette">Abonement Rézo Box : </div> <div class="produit"> '.$this->get_cli_rezo_box().'<a class="inline" href="?contenu=print_contrat&cli_id='.$this->get_cli_id().'&ctype=rezobox">Imprimer le contrat</a></div>
		<div class="etiquette">Abonement Rézo Backup : </div> <div class="produit"> '.$this->get_cli_rezo_backup().'<a class="inline" href="?contenu=print_contrat&cli_id='.$this->get_cli_id().'&ctype=rezobackup">Imprimer le contrat</a></div>
		<div class="etiquette">Abonement Maintenance : </div> <div class="produit"> '.$this->get_cli_mtt_maintenance().'<a class="inline" href="?contenu=print_contrat&cli_id='.$this->get_cli_id().'&ctype=maintenance">Imprimer le contrat</a></div>
		<div class="etiquette">Echéances abonements : </div> <div class="produit"> '.$this->get_cli_echeances_abo().'</div>
    <div class="etiquette">Premiere échéance : </div> <div class="produit"> ' .$this->get_cli_premiere_echeance() . '</div>
		'.$info.'
		'.$this->show_all_contact().'
		<span class="clear">&nbsp;</span>
		<form method="post" action="./index.php?contenu=editcustomer">
			<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
			<input type="submit" value="Modifier">
		</form>
	</div>';
		return $SHOW_CLIENT;
	}

	function show_sum()
	{
		$sql="SELECT USE_PRENOM FROM T_USER WHERE USE_ID ='$this->cli_tech_id'";
		mysqlinforezo();
		$query = mysql_query($sql) or die(sql_error('client','show_sum()', 1, $sql));
		mysql_close();
		$liste=mysql_fetch_array($query);
		$technicien=$liste['USE_PRENOM'];
		$SHOW_SUM='
	<div class="presentation">
		<h2>Voir le client</h2>
		<div class="etiquette">Société : </div><div class="produit"> '.$this->get_cli_societe().'</div>
		<div class="etiquette">Adresse : </div><div class="produit"> '.$this->get_cli_adresse().'</div>
		<div class="etiquette">Code postal : </div> <div class="produit"> '.$this->get_cli_code_postal().'</div>
		<div class="etiquette">Ville : </div> <div class="produit"> '.$this->get_cli_ville().'</div>
		<div class="etiquette">Téléphone : </div> <div class="produit"> '.$this->get_cli_telephone().'</div>
		<div class="etiquette">Maintenance : </div> <div class="produit"> '.$this->get_cli_maintenance().'</div> 
		<div class="etiquette">Technicien : </div> <div class="produit"> '.$this->get_cli_technicien().'</div> 
		<div class="etiquette">Clé : </div>
		<div class="produit"> 
			<form method="post" action="./index2.php?contenu=a_savoir">
				<input type = "hidden" name = "cli_id" value = "'.$this->cli_id.'">
				<input type = "password" name = "cle" value="">
				<input type="submit" value="A savoir">
			</form>
		</div>
		'.$this->show_all_contact().'
		<span class="clear">&nbsp;</span>
	</div>';
		return $SHOW_SUM;
	}

	function showSum()
	{
		$SHOW_SUM='
		<div class="etiquette">Société : </div><div class="produit"> '.$this->get_cli_societe().'</div>
		<div class="etiquette">Adresse : </div><div class="produit"> '.$this->get_cli_adresse().'</div>
		<div class="etiquette">Code postal : </div> <div class="produit"> '.$this->get_cli_code_postal().'</div>
		<div class="etiquette">Ville : </div> <div class="produit"> '.$this->get_cli_ville().'</div>
		<div class="etiquette">Téléphone : </div> <div class="produit"> '.$this->get_cli_telephone().'</div>';
		return $SHOW_SUM;
	}

	function affiche_ligne()
	{
    if ($this->cli_maintenance == 'Oui')
      $color = ' bgcolor="#55DD55"';
    if ($this->cli_maintenance == 'Non')
      $color = ' bgcolor="#DD5555"';
    if ($this->cli_maintenance == 'Client')
      $color = ' bgcolor="#5555DD"';
		$responsable = new contact($this->responsable());
		$affiche_ligne=
		'<tr' . $color . '>
			<td><a href="./index2.php?contenu=showcustomer&cli_id='.$this->cli_id.'"> '.$this->cli_societe.'</a></td>
			<td>'.$this->get_cli_telephone().'</td>
			<td>'.$responsable->get_prenom_nom().'</td>
			<td>'.$responsable->get_email().'</td>
		</tr>';
		return $affiche_ligne;
	}
// controle si on a le droit d'enregistrer dans la base de donnée
	function test4enreg()
	{
		if ($this->cli_societe=="")
			$result = 0;
		else
		{
			$sql= "SELECT CLI_SOCIETE FROM T_CLIENT WHERE CLI_SOCIETE= '$this->cli_societe' AND CLI_ID != '$this->cli_id';";
			mysqlinforezo();
			$query=mysql_query($sql) or die (sql_error('client','test4enreg','1',$sql));
			if (!mysql_num_rows($query))
			{	
				$result=1;
			}
			else
				$result=0;
		}
		return $result;
	}
// envoie les messages d'avertissement si tout les champs n'ont pas été rempli
	function warning()
	{
		if (
      (empty($this->cli_adresse)) ||
      (empty($this->cli_code_postal)) ||
      (empty($this->cli_ville)) ||
   		(empty($this->cli_telephone)) ||
      (empty($this->cli_telecopie)) ||
      (empty($this->cli_tva)))
		{
			$result= '<div class="presentation">';
			$fin='</div>';
		}
		if(empty($this->cli_adresse))
		{
			$result=$result."<center>Avertissement: L' '<b>adresse</b>' est vide !</center>";
		}
		if(empty($this->cli_code_postal))
		{
			$result=$result."<center>Avertissement: Le <b>Code postale</b> est vide !</center>";
		}
		if(empty($this->cli_ville))
		{
			$result=$result."<center>La '<b>ville</b>' est vide !</center>";
 		}
   		if(empty($this->cli_telephone))
   		{
			$result=$result."<center>Avertissement: Le '<b>numéro de telephone </b>' est vide !</center>";
   		}
		if(empty($this->cli_telecopie))
		{
			$result=$result."<center>Avertissement: Le '<b>numéro de fax</b>' est vide !</center>";
		}
		if(empty($this->cli_tva))
		{
			$result=$result."<center>Avertissement: Le '<b> numéro de TVA</b>' est vide !</center>";
		}
		$result=$result.$fin;
		return $result; 
	}

	function delete()
	{
		
	}

  function haveToPay()
  {
    if ($this->cli_mtt_maintenance == 0 && $this->cli_rezo_box == 0 && $this->cli_rezo_backup == 0)
      return (false);
    if ($this->cli_echeances_abo == 'mois')
      return (true);
    $months = array('Janvier' => 0, 
                    'Février' => 1, 
                    'Mars' => 2, 
                    'Avril' => 3, 
                    'Mai' => 4, 
                    'Juin' => 5,
                    'Juillet' => 6,
                    'Aout' => 7, 
                    'Septembre' => 8, 
                    'Octobre' => 9, 
                    'Novembre' => 10, 
                    'Décembre' => 11);
    $curMonth = date('n') - 1;
    $premiere_echeance = $months[$this->cli_premiere_echeance];
    if ($this->cli_echeances_abo == 'trimestre') {
      $payMonth = array($premiere_echeance,
                        ($premiere_echeance + 3) % 12, 
                        ($premiere_echeance + 6) % 12, 
                        ($premiere_echeance+ 9) % 12);
      if (in_array($curMonth, $payMonth))
        return (true);
      else
        return (false);
    }
    if ($this->cli_echeances_abo == 'semestre') {
      $payMonth = array($premiere_echeance,
                        ($premiere_echeance + 6) % 12);
      if (in_array($curMonth, $payMonth))
        return (true);
      else
        return (false);
    }
    if ($this->cli_echeances_abo == 'année') {
      if ($curMonth == $this->cli_premiere_echeance)
        return (true);
      else
        return (false);
    }
  }

  function makeFacture()
  {
    $periode = $this->cli_echeances_abo;
    if ($periode == 'mois') {
      $designation ='maintenance du mois: ' . strftime('%B', mktime(0, 0, 0, date("m") + 1));
      $div = 12;
    }
    elseif ($periode == 'trimestre') {
      if (date('n') <= 3)
        $t = 'deuxieme';
      elseif (date('n') <= 6)
        $t = 'troisième';
      elseif (date('n') <= 9)
        $t = 'quatrième';
      else
        $t = 'premier';
      $designation = 'maintenance du ' . $t  . ' trimestre';
      $div = 4;
    }
    elseif ($periode == 'semestre'){
      $div = 2;
      if ($date('n') < 6)
        $s = 'deuxieme';
      else
        $s = 'premier';
      $designation = 'maintenance du '. $s .' semestre';
    }
    elseif ($periode == 'année') {
      $div = 1;
      $designation = 'maintenance annuelle';
    }
    $l = 0;
    $facture = new facture;
    $facture->fac_titre = $designation;
    $facture->client = $this;
    $facture->fac_date_crea = time();
    $facture->contact = $this->contact->con_id;
    if ($this->cli_mtt_maintenance > 0) {
      $facture->tab_lfa[] = new ligne_facture;
      $facture->tab_lfa[$l]->lfa_type = T_MAINTENANCE;
      $facture->tab_lfa[$l]->lfa_designation = $designation; 
      $facture->tab_lfa[$l]->lfa_prix_ht = $this->get_cli_mtt_maintenance()/$div;
      $l++;
    }    
    if ($this->cli_rezo_box > 0) {
      $facture->tab_lfa[] = new ligne_facture;
      $facture->tab_lfa[$l]->lfa_type = T_MAINTENANCE;
      $facture->tab_lfa[$l]->lfa_designation = "Location de la rézobox";
      $facture->tab_lfa[$l]->lfa_prix_ht = $this->get_cli_rezo_box()/$div;
      $l++;
    }
    if ($this->cli_rezo_backup > 0) {
      $facture->tab_lfa[] = new ligne_facture;
      $facture->tab_lfa[$l]->lfa_type = T_MAINTENANCE;
      $facture->tab_lfa[$l]->lfa_designation = "Abonnement rézobackup";
      $facture->tab_lfa[$l]->lfa_prix_ht = $this->get_cli_rezo_backup()/$div;
      $l++;
    }
    unset($facture->fac_id);
    echo $facture->enreg();
    echo $facture->preview();
    return $facture->write_pdf(false);
  }

  function write_contrat($type)
  {
    $file = 'tmp/contrat-' . $type . '-' . $this->get_cli_id() . '.pdf';
    $pdf = $this->write_contrat_unitaire($type);
    $pdf->Output($file);
    $result = '<form method = "get" action = "'.$file.'">
        <input type="submit" name="print" value = "Imprimer">
      </form>';
    return($result);
  }

  function write_contrat_unitaire($type)
  {
    $pdf = new PDF_contrat();
    $pdf->addTitle($type);
    $pdf->addIntro($this);
    $pdf->addCorp($this, $type);
    $pdf->addEnd($this, $type);
    return $pdf;
  }
}

class liste_client
{
	var $nb_client;		// nombre de clients dans la liste
	var $tableau_client; 	// un tableau d'objet client
	var $caption;		//Titre de la liste des client

	function liste_client()
	{
		$this->nb_inter=0;
		$this->tableau_client=array();
	}

	function select($lettre='') // première lettre du nom de la société à chercher
	{
		$where = "WHERE T_CLIENT.CLI_TYPE != 'Client perdu' AND T_CLIENT.CLI_SOCIETE LIKE '${lettre}%'";
   		$sql = "SELECT T_CLIENT.CLI_SOCIETE, T_CLIENT.CLI_ID, T_CLIENT.CLI_TELEPHONE, T_CLIENT.CLI_CODE_NAF, T_CLIENT.CLI_NO_TVA, T_CLIENT.CLI_CODE_POSTAL, T_CLIENT.CLI_VILLE, T_CLIENT.CLI_TELECOPIE, T_CLIENT.CLI_ADRESSE, T_CLIENT.CLI_TYPE, T_CLIENT.CLI_ECHEANCES, T_CLIENT.CLI_NB_FACTURE, T_CLIENT.CLI_NB_POSTE, T_CLIENT.CLI_TECH_ID, T_CLIENT.CLI_PWD, T_CLIENT.CLI_MAINTENANCE, T_CLIENT.CLI_MAINTENANCE, T_CLIENT.CLI_A_SAVOIR, T_CLIENT.CLI_REZO_BOX, T_CLIENT.CLI_REZO_BACKUP, T_CLIENT.CLI_ECHEANCES_ABO, T_CLIENT.CLI_MTT_MAINTENANCE, T_CLIENT.CLI_SIRET, T_CLIENT.CLI_PREMIERE_ECHEANCE, T_CONTACT.CON_ID, T_CONTACT.CON_PRENOM, T_CONTACT.CON_NOM, T_CONTACT.CON_EMAIL, T_CONTACT.CON_TELEPHONE, T_CONTACT.CON_PORTABLE, T_CONTACT.CON_RESPONSABLE FROM (T_CLIENT INNER JOIN L_CLI_CON ON T_CLIENT.CLI_ID=L_CLI_CON.CLI_ID)INNER JOIN T_CONTACT ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID $where GROUP BY T_CLIENT.CLI_SOCIETE ORDER BY T_CLIENT.CLI_SOCIETE;";
		mysqlinforezo();
		$query= mysql_query($sql) or die (sql_error('liste_client', 'select()', 1, $sql));
		while ($liste = mysql_fetch_array($query))
		{
			$client=new client;
			$client->init_from_liste($liste);
			$this->tableau_client[] = $client;
			$this->nb_client = count($this->tableau_client);
		}
	}

	function select_periode($periode)
	{
		
		$where = "WHERE T_CLIENT.CLI_TYPE != 'Client perdu' AND T_CLIENT.CLI_MAINTENANCE = 'Oui' AND T_CLIENT.CLI_ECHEANCES_ABO = '$periode'";
   		$sql = "
		SELECT 
			T_CLIENT.CLI_ID,
			T_CLIENT.CLI_SOCIETE,
			T_CLIENT.CLI_ADRESSE,
			T_CLIENT.CLI_CODE_POSTAL,
			T_CLIENT.CLI_VILLE,
			T_CLIENT.CLI_TELEPHONE,
			T_CLIENT.CLI_TELECOPIE,
			T_CLIENT.CLI_TYPE,
			T_CLIENT.CLI_NO_TVA,
			T_CLIENT.CLI_MAINTENANCE,
			T_CLIENT.CLI_ECHEANCES,
			T_CLIENT.CLI_ECHEANCES_ABO,
			T_CLIENT.CLI_TECH_ID,
			T_CLIENT.CLI_MTT_MAINTENANCE,
			T_CLIENT.CLI_NB_FACTURE,
			T_CLIENT.CLI_PWD,
			T_CONTACT.CON_ID,
			T_CONTACT.CON_PRENOM,
			T_CONTACT.CON_NOM,
			T_CONTACT.CON_EMAIL,
			T_CONTACT.CON_PORTABLE,
			T_CONTACT.CON_AUTRE,
			T_CONTACT.CON_RESPONSABLE
			FROM (T_CLIENT INNER JOIN L_CLI_CON ON T_CLIENT.CLI_ID=L_CLI_CON.CLI_ID)INNER JOIN T_CONTACT ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID $where GROUP BY T_CLIENT.CLI_SOCIETE ORDER BY T_CLIENT.CLI_SOCIETE;";
		mysqlinforezo();
		$query= mysql_query($sql) or die (sql_error('liste_client', 'select_mensuel()', 1, $sql));
		while ($liste = mysql_fetch_array($query)){
			$client=new client;
			$client->init_from_liste($liste);
			$this->tableau_client[] = $client;
			$this->nb_client = count($this->tableau_client);
		}
	}
	
	function display()
	{
    $caption ='';
		for ($i = 'A'; $i != 'AA'; $i++)
		{
			$caption.='<a href="index2.php?contenu=viewcustomers&search='.$i.'">'.$i.'</a>&nbsp;';
		}
		$caption.= '<br> <a href="index2.php?contenu=viewcustomers">Tous</a>'; 
    $caption.= '<div class="legret"> Client sans contrat de maintenance </div>';
    $caption.= '<div class="legvert"> Client avec contrat de maintenance </div>';
		$DISPLAY='
<table class="client" border>
	<caption>'.$caption.'</caption>
	<tr>
		<th>Société</th>
		<th>Tel Société</th>
		<th>Nom Responsable</th>
		<th>Email Responsable</th>
	</tr>';
		foreach ($this->tableau_client as $client)
		{
			$DISPLAY.= $client->affiche_ligne();
		}
	$DISPLAY.='</table>';
	return $DISPLAY;
	}
	
}


define
(
	"TITRE_CLIENT",
	'<tr>
		<th>Société</th>
		<th>Tel Société</th>
		<th>Nom Responsable</th>
		<th>Email Responsable</th>
	</tr>'
)
?>

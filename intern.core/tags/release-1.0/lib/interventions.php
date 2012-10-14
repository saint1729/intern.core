<?php
include("class/iCalcreator.class.php");
class intervention
{
// déclaration des variables de classe
	var $int_id;			// identifiant intervention
	var $cli_id;			// client chez qui on doit intervenir
	var $use_id;			// technicien chargé de l'intervention
  var $dev_id;      // devis auquel l'intervention est liée
	var $int_createur_id; 		// identtifiant du crétaeur de l'intervention (id du client ou du technicien) 
	var $int_description; 		// description de l'intervention (a spécifier à la création)
	var $int_rapport;		// rapport du technicien
	var $int_status;		// état d'avancement de l'intervention: pas commencé, commencé, terminé
	var $int_date_intervention;	// date et heure prévu pour l'intervention
	var $int_date_crea;		// date de création de l'intervention
	var $int_date_cloture;		// date de cloture de l'intervention (date ou le status est mis à terminé)
	var $int_tps_passe;		// temps passé par le technicien sur l'intervention
	var $int_type;			// type d'intervention: 		
  var $int_deplacement; // déplacement nécéssité par l'intervention (télémaintenance, déplacement ou grand déplacement)
	var $cli_nb_facture;
	var $client;			//objet client
	var $technicien;		//nom technicien
//methodes de classe
//constructeur: si l'id est 0 on crée un objet vide
//		si l'id est différente de 0 on se connect à la base de donné pour récupérer les valeurs de l'objet
	function intervention($int_id=0)
	{
		if ($int_id==0)
		{
		}
		else
		{
			mysqlinforezo();
			$sql = "SELECT INT_ID, CLI_ID, USE_ID, DEV_ID, INT_CREATEUR_ID, INT_DESCRIPTION, INT_RAPPORT, INT_STATUS, UNIX_TIMESTAMP(INT_DATE_INTERVENTION) AS INT_DATE_INTERVENTION, INT_DATE_CREA, INT_DATE_CLOTURE, INT_TYPE, INT_DEPLACEMENT, INT_TPS_PASSE FROM T_INTERVENTION WHERE INT_ID = '$int_id';";
			$query=mysql_query($sql) or die (sql_error('intervention','intervention (constructeur)','1',$sql));
			$liste=mysql_fetch_array($query);
			$this->int_id = $int_id;
			$this->cli_id= $liste['CLI_ID'];
			$this->use_id= $liste['USE_ID'];
      $this->dev_id = $liste['DEV_ID'];
			$this->int_createur_id= $liste['INT_CREATEUR_ID']; 
			$this->int_description= $liste['INT_DESCRIPTION'];
			$this->int_rapport= $liste['INT_RAPPORT'];
			$this->int_status= $liste['INT_STATUS'];
			$this->int_date_intervention= $liste['INT_DATE_INTERVENTION'];
			$this->int_date_crea= $liste['INT_DATE_CREA'];
			$this->int_date_cloture= $liste['INT_DATE_CLOTURE'];
			$this->int_type= $liste['INT_TYPE'];
			$this->int_tps_passe= $liste['INT_TPS_PASSE'];
      $this->int_deplacement = $liste['INT_DEPLACEMENT'];
			mysql_close();
		}
	}

	function init_from_liste($liste)
	{
		$this->int_id=$liste["INT_ID"];
		$this->cli_id=$liste["CLI_ID"];
		$this->use_id=$liste["USE_ID"];
    $this->dev_id=$liste["DEV_ID"];
		$this->int_status=$liste["INT_STATUS"];
		$this->createur_id=$liste["INT_CREATEUR_ID"];
		$this->int_description=$liste["INT_DESCRIPTION"];
		$this->int_date_intervention = $liste["INT_DATE_INTERVENTION"];
		$this->int_technicien = $liste["USE_PRENOM"];
		$this->client = new client;
		$this->client->cli_societe=$liste['CLI_SOCIETE'];
		$this->int_tps_passe = $liste['INT_TPS_PASSE'];
    $this->int_deplacement = $liste['INT_DEPLACEMENT'];
		
	}
	
	function get_int_id()
	{
		return $this->int_id;
	}
	
	function get_cli_id()
	{
		if(empty($this->cli_id))
			$result='&nbsp;';
		else
			$result=$this->cli_id;
		return $result;
	}

	function get_use_id()
	{
		if(empty($this->use_id))
			$result='&nbsp;';
		else
			$result=$this->use_id;
		return $result;
	}

	function get_tech_mail()
	{
		mysqlinforezo();
		$sql = "SELECT USE_EMAIL FROM T_USER WHERE USE_ID = $this->use_id";
		$query = mysql_query($sql) or die (sql_error('intervention', 'get_tech_mail', 1, $sql));
		$liste = mysql_fetch_array($query);
		return ($liste['USE_EMAIL']);
	}

	function get_createur_id()
	{
		if(empty($this->int_createur_id))
			$result='&nbsp;';
		else
			$result=$this->int_createur_id;
		return $result;
	}

	function get_int_description()
	{
		if(empty($this->int_description))
			$result='&nbsp;';
		else
			$result=mise_en_page($this->int_description);
		return $result;
	}
	
	function get_int_rapport()
	{
		if(empty($this->int_rapport))
			$result='&nbsp;';
		else
			$result=mise_en_page($this->int_rapport);
		return $result;
	}

	function get_int_status()
	{
		if(empty($this->int_status))
			$result='&nbsp;';
		else
			$result=$this->int_status;
		return $result;
	}

  function set_int_status($status)
  {
    $this->int_status = $status;
  }
	
	function get_int_date_intervention()
	{
		setlocale (LC_ALL, "fr_FR.utf8");
		if(empty($this->int_date_intervention))
			$result='&nbsp;';
		else
			$result=strftime('%A %e %B %G' , $this->int_date_intervention);
		return $result;
	}
	
	function get_int_heure_intervention()
	{
		if(empty($this->int_date_intervention))
			$result='&nbsp;';
		else
			{
				if (date('H', $this->int_date_intervention)<12)
					$result = 'Matin';
				else
					$result = 'Après-midi';
			}
		return $result;
	}
	
	function set_date_by_day($jour)
	{
		if($jour<date('w'))
		{
			$jour = $jour + 7;
		}
		$this->int_date_intervention = time()+ ($jour-date('w'))*60*60*24;
		return 0;
	}
	
	function set_int_date_intervention($date, $timeStart, $timeEnd) {
		$bon_format = '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{1,4}$';
		if (ereg($bon_format, $date)) {
			$table_date = split("/", $date);
			$jour = $table_date[0];
			$mois = $table_date[1];
			$anne = $table_date[2];
      $minute = 0;

			if (checkdate($mois, $jour, $anne)) {
				$result = 0;
				$this->int_date_intervention=mktime($timeStart, $minute, 0, $mois, $jour, $anne);
				$this->int_start=mktime($timeStart, $minute, 0, $mois, $jour, $anne);
				$this->int_end=mktime($timeEnd, $minute, 0, $mois, $jour, $anne);
			} else {
				$result = "Ereur: date non valide, le format est bon mais ce jour n'existe pas";
			}
		} else {
			$result = "Erreur: le format de la date n'est pas bon: dd/mm/yyyy ou dd/mm/yyyy ou d/m/yy";
		}	
		return $result;
	}

	function get_int_date_crea()
	{
		if(empty($this->int_date_crea))
			$result='&nbsp;';
		else
			$result=$this->int_date_crea;
		return $result;
	}

	function get_int_date_cloture()
	{
		if(empty($this->int_date_cloture))
			$result='&nbsp;';
		else
			$result=$this->int_date_cloture;
		return $result;
	}
	
	function get_int_type()
	{
		if(empty($this->int_type))
			$result='&nbsp;';
		else
			$result=$this->int_type;
		return $result;
	}

	function set_int_tps_passe($temps)
	{
		$bon_format = '^[0-9]{1,2}[hH:][0-9]{0,2}$';
		$temps = str_replace(' ', '', $temps);
		if (ereg($bon_format, $temps))
		{
			$table_temps = split("[h:]",$temps);
			$heure = $table_temps[0];
			$minute = $table_temps[1];
			if ($heure < 24 && $minute < 60)
			{
				$this->int_tps_passe= $heure * 60 + $minute;
				$result = 0;
			}
			else
			{
				$result .= "Erreur: heure non valide, le format est correct mais cette heure n'existe pas";
			}
		}
		else
		{
			$result .= "Erreur le format de l'heure n'est pas bon: hhhmm ou hh:mm ou hhHmm ou h:m ou h:mm";
		}
		return $result;
	}
	
	function get_int_tps_passe()
	{
		if(empty($this->int_tps_passe))
			$result='';
		else
		{
			$temps=euclidivision($this->int_tps_passe, 60);
			$heure = $temps[0];
			$minute = $temps[1];
			$result =sprintf("%d h %02d",$heure, $minute);
		}
		return $result;
	}

	function get_technicien()
	{
		if(empty($this->use_id))
			$result='aucun technicien';
		else
		{
			mysqlinforezo();
			$sql = "SELECT USE_PRENOM, USE_NOM FROM T_USER WHERE USE_ID='$this->use_id'; ";
			$query = mysql_query($sql) or die(sql_error('intervention','get_technicien','1',$sql) );
			$liste = mysql_fetch_array($query);
			$result = $liste['USE_PRENOM'].' '.$liste['USE_NOM'];
			mysql_close();
		}
		return $result;
	}
// retourne l'objet technicien associé au client
  public function GetUser(){
    return new user($this->use_id);
  }

	function select_technicien()
	{
		return MakeSelectUsers($this->use_id);
	}
	
	function select_status()
	{
		$liste = funcEnumList('T_INTERVENTION', 'INT_STATUS');
		$select = funcMakeFormList('int_status', $liste, $this->int_status, 'int_status');
		return $select;
	}

	function radio_deplacement()
	{
		$liste = funcEnumList('T_INTERVENTION', 'INT_DEPLACEMENT');
		$select = funcMakeFormRadio('int_deplacement', $liste, $this->int_deplacement);
		return $select;
	}

  function checkForUpdate() {
    if (isset ($this->int_deplacement)) {
      return false;
    } else {
      return '<div class="formadmin"><center>Il faut renseigner le  "<b>Déplacement</b>" pour enregistrer le rapport !</center></div>';
    }
  }

	function test4enreg()
	{
		if ($this->int_id=='')
		{
			if ($this->cli_id == '') $result = "PAS DE CLIENT";
			elseif ($this->use_id == '') $result = "PAS DE TECHNICIEN";
			elseif ($this->int_createur_id == '') $result = "PAS DE CREATEUR";
			elseif ($this->int_description == '') $result = "PAS DE DESCRIPTION";
			elseif ($this->int_date_intervention == '') $result = "PAS DE DATE D'INTERVENTION";
			else $result = 0;
		}
		else
		{
      $result='';
		}
		return $result;
				
	}

	function enreg()
	{
		$date_inter = date('YmdHis',$this->int_date_intervention);
		mysqlinforezo();
		if ($this->int_id!="")	// update
		{
			$sql = "UPDATE T_INTERVENTION SET INT_DESCRIPTION='$this->int_description', INT_STATUS='$this->int_status', INT_DATE_INTERVENTION='$date_inter', INT_DATE_CLOTURE='$this->int_date_cloture', USE_ID='$this->use_id' WHERE INT_ID=$this->int_id;";
		$query = mysql_query($sql) or die(sql_error('intervention','enreg','1',$sql) );
		}
		else		// insert
		{
			$sql = "INSERT INTO T_INTERVENTION (INT_ID, CLI_ID, USE_ID, DEV_ID, INT_CREATEUR_ID, INT_DESCRIPTION, INT_DATE_INTERVENTION, INT_DATE_CREA, INT_TYPE, INT_START, INT_END) VALUES ('','$this->cli_id', '$this->use_id', '$this->dev_id', '$this->int_createur_id', '$this->int_description', $date_inter, '$this->int_date_crea', '$this->int_type', FROM_UNIXTIME('$this->int_start'), FROM_UNIXTIME('$this->int_end'));";
		$query = mysql_query($sql) or die(sql_error('intervention','enreg','2',$sql) );
		$this->int_id=mysql_insert_id();
		}
		mysql_close();
	}
	
	function add_new($cli_id='',$vue='', $addName='addFree')
	{
		$client = new client($cli_id);
		$date_actuelle= date('d/m/y');
		$heure_actuelle= date('H\hi');
		$technicien = $client->select_technicien();
		$text = $this->int_description;
    $dev_id = $this->dev_id;
    $int_id=$this->int_id;
		if ($cli_id=='')		// modification demande intervention
		{
			$date_actuelle = date('d/m/y', $this->int_date_intervention);
			$heure_actuelle = date('H\hi', $this->int_date_intervention);
			$cli_id= $this->cli_id;
      $dev_id= $this->dev_id;
			$technicien = $this->select_technicien();
		}
		$check_matin = "checked";
		if ($this->get_int_heure_intervention()!='Matin')
		{
			$check_apresmidi = "checked";
			$check_matin = '';
		}
		if ($vue == 'client')
		{
			$SPE_ADD_NEW='
<h3> Jour souhaité pour l\'intervention</h3>
<label for="lundi">Lundi</label> <input type="radio" name="jour" value="1" id="lundi" checked>
<label for="mardi">Mardi</label> <input type="radio" name="jour" value="2" id="mardi">
<label for="mercredi">Mercredi</label> <input type="radio" name="jour" value="3" id="mercredi">
<label for="jeudi">Jeudi</label> <input type="radio" name="jour" value="4" id="jeudi">
<label for="vendredi">Vendredi</label> <input type="radio" name="jour" value="5" id="vendredi">
			';
		}
		elseif ($vue=='')
		{
			$SPE_ADD_NEW='
<div><label for="int_select_users">Technicien : </label>'.$technicien.'</div>
<div><label for="int_date_intervention">Date d\'intervention : </label> <input type="text" name="int_date_intervention" size="10" value = "'.$date_actuelle.'"maxlength=50 id="int_date_intervention"></div>
<div>
	<label for="matin">Matin</label> <input type="radio" name="heure" value="matin" id="matin" '.$check_matin.'>
	<label for="apresmidi">Après-midi</label> <input type="radio" name="heure" value="apresmidi" id="apresmidi"'.$check_apresmidi.'>
</div>
			';
      $SPE_ADD_NEW .= timeSelect(array('div' => true, 'startLabel' => 'Heure de début : ', 'endLabel' => 'Heure de fin : '));
      $SPE_ADD_NEW .= '<div><label for="prevenir"> prevenir '. $client->get_cli_responsable().' par email : </label><input type=checkbox name="prevenir" id="prevenir" /> </div>';
		}
		$ADD_NEW='
<div class="formadmin">
	<h2>client: '.$client->get_cli_societe().'</h2>
	<form method="post" action="./index2.php?contenu=new_intervention">
		'.$SPE_ADD_NEW.'
		<div><label for="int_description">Description : </label> <textarea rows="5" cols="50" name = "int_description" id = "int_description">'.$text.'</textarea></div>
		<input type="hidden" name="cli_id" value = "'.$cli_id.'">
		<input type="hidden" name="int_id" value = "'.$int_id.'">
		<input type="hidden" name="dev_id" value = "'.$dev_id.'">
		<span class="clear">&nbsp;</span>
		<input type="submit" name="'. $addName .'" value="Ajouter">
	</form>
	<form method="get" action="./index2.php">
		<input type="hidden" name="contenu" value="showcustomer">
		<input type="hidden" name="cli_id" value="'.$cli_id.'">
		<input type="submit" value="Retour">
	</form>
</div>';
		return $ADD_NEW;
	}
	
	function edit($show_sum=true, $pay='free')
	{
		$text=$this->int_rapport;
    if ($show_sum)
      $EDIT = $this->show('no_rapport');
		$EDIT .= '
  <span class="clear">&nbsp;</span>
	<div class="formadmin">
		<h2>Rapport</h2>
		<form method="post" action="./index2.php?contenu=update_task">
			<input type="hidden" name="int_id" value = "'.$this->int_id.'">
			<div><label for="int_tps_passe">Temps passé :</label> <input type="text" name="int_tps_passe" size="10" value = "'.$this->get_int_tps_passe().'"maxlength=50 id="int_tps_passe"></div>
			<div><label for="int_status">Status : </label>'.$this->select_status().'</div>
			<div><label for="int_deplacement">Déplacement : </label>'.$this->radio_deplacement().'</div>
			<div><label for="int_rapport">Rapport : </label> <textarea rows="5" cols="50" name = "int_rapport" id = "int_rapport">'.$text.'</textarea></div>
			<span class="clear">&nbsp;</span>
			<input type="submit" name="'.$pay.'" value="Valider">
		</form>
	</div>
		';
		return $EDIT;
	}
	
	function update()
	{
		if ($this->int_status == 'Terminé')
		{
			$this->int_date_intervention = time();
			$date_inter = date('YmdHis');
			$sql = "UPDATE T_INTERVENTION SET INT_TPS_PASSE='$this->int_tps_passe', INT_STATUS='$this->int_status', INT_DEPLACEMENT='$this->int_deplacement', INT_RAPPORT='$this->int_rapport', INT_DATE_INTERVENTION='$date_inter' WHERE INT_ID=$this->int_id;";
		}
		else
		{
			$sql = "UPDATE T_INTERVENTION SET INT_TPS_PASSE='$this->int_tps_passe', INT_STATUS='$this->int_status', INT_DEPLACEMENT='$this->int_deplacement', INT_RAPPORT='$this->int_rapport' WHERE INT_ID=$this->int_id;";
		}
		mysqlinforezo();
		$query = mysql_query($sql) or die (sql_error('intervention', 'update', 1, $sql));
		mysql_close();
		return '<div class="bandeau"><h1>Rapport</h1><h2>Enregistré avec succès</h2></div>';
	}

  public function alertCustomer() {
    $tech = $this->getUser();
    $fromEmail = $tech->getEmail();
    $sujet = SOCIETE . ' a prévu une intervention chez vous';
    $contenu = $tech->getPrenomNom() . ' se déplacera chez vous le ' . $this->get_int_date_intervention() . ' à ' . $this->get_int_heure_intervention() . "\n" .
      'pour effectuer l\'intervention suivante :' . "\n" .
      strip_tags(html_entity_decode(str_replace('&nbsp;', '', $this->get_int_description())));
		$result='email envoyés à:<ul> ';
    $responsables = $this->getResponsables();
    while ($responsable = mysql_fetch_array($responsables)) {
			$entetedate  = date("D, j M Y H:i:s +0100"); // Offset horaire
			$entetemail  = "From: $fromEmail \n"; // Adresse expéditeur
			$entetemail .= "Cc: \n";
			#$entetemail .= "Bcc: yohann@inforezo.com \n"; // Copies cachées
			$entetemail .= "Reply-To: $fromEmail \n"; // Adresse de retour
			$entetemail .= "X-Mailer: PHP/" . phpversion() . "\n" ;
			$entetemail .= "Date: $entetedate";
      if( mail($responsable['CON_EMAIL'], $sujet, $contenu , $entetemail)) { 
        $result.= '<li>'.$responsable['CON_EMAIL'].'</li>';
      } else {
        $result.= '<li>echec du mail</li>';
      }
		}
    $result .= '</ul>';
		return $result;
  }

  public function getResponsables() {
		$sql = "SELECT CON_EMAIL FROM T_CONTACT INNER JOIN L_CLI_CON ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID INNER JOIN T_CLIENT ON L_CLI_CON.CLI_ID=T_CLIENT.CLI_ID WHERE T_CLIENT.CLI_ID='$this->cli_id' AND T_CONTACT.CON_RESPONSABLE= '1'";
		mysqlinforezo();
		$query = mysql_query($sql) or die (sql_error('intervention', 'send_mail', 1, $sql));
		mysql_close();
    return $query;
  }

	function send_mail($expediteur='', $sujet='', $contenu='')
	{
		if ($expediteur=='') {
      $tech = $this->getUser();
      $from_email = $tech->getEmail(); 
    }
		else $from_email = $expediteur;
		if ($sujet =='') $sujet = 'Rapport intervention ' . SOCIETE;  
		if ($contenu == '') {
      $contenu = 'Ce message vous a été envoyé automatiquement par '. SOCIETE .' car une intervention vient d\'être terminée, vous pouvez consulter le rapport en ligne sur '. BASE_URL.'?inter_id='.$this->int_id;   
      $contenu .= "\n \n Description de l'intervention:\n" . $this->int_description . "\n \n Durée de l'intervention: \t". $this->get_int_tps_passe()."\n \n Rapport de l'intervention: \n" . $this->int_rapport;
    }
		$result='email envoyés à:<ul> ';
    $query = $this->getResponsables();
		while ($liste = mysql_fetch_array($query))
		{
			$entetedate  = date("D, j M Y H:i:s +0100"); // Offset horaire
			$entetemail  = "From: $from_email \n"; // Adresse expéditeur
			$entetemail .= "Cc: \n";
			#$entetemail .= "Bcc: yohann@inforezo.com \n"; // Copies cachées
			$entetemail .= "Reply-To: $from_email \n"; // Adresse de retour
			$entetemail .= "X-Mailer: PHP/" . phpversion() . "\n" ;
			$entetemail .= "Date: $entetedate";
      if( mail($liste['CON_EMAIL'], $sujet, $contenu , $entetemail)) { 
        $result.= '<li>'.$liste['CON_EMAIL'].'</li>';
      } else {
        $result.= '<li>echec du mail</li>';
      }
		}
    $result .= '</ul>';
		return $result;
	}

	function alert_tech()
	{
		mysqlinforezo();
		$sql = "SELECT USE_EMAIL from T_USER WHERE USE_ID = '$this->use_id';";
		$query = mysql_query($sql) or die (sql_error('intervention', 'alert_tech()', 1, $sql));
		$liste = mysql_fetch_array($query);
		$tech_email= $liste['USE_EMAIL'];
		$sql = "SELECT CON_EMAIL FROM T_CONTACT INNER JOIN L_CLI_CON ON T_CONTACT.CON_ID=L_CLI_CON.CON_ID INNER JOIN T_CLIENT ON L_CLI_CON.CLI_ID=T_CLIENT.CLI_ID WHERE T_CLIENT.CLI_ID='$this->cli_id' AND T_CONTACT.CON_RESPONSABLE= '1'";
		$query = mysql_query($sql) or die (sql_error('intervention', 'alert_tech()', 2, $sql));
		$liste = mysql_fetch_array($query);
		$cli_email = $liste['CON_EMAIL'];
		mysql_close();
		$entetedate  = date("D, j M Y H:i:s +0100"); // Offset horaire
		$entetemail  = "From: $cli_email \n"; // Adresse expéditeur
		$entetemail .= "Cc: intervention@inforezo.com\n";
		$entetemail .= "Reply-To: $cli_email \n"; // Adresse de retour
		$entetemail .= "X-Mailer: PHP/" . phpversion() . "\n" ;
		$entetemail .= "Date: $entetedate";
		$sujet = 'demande d\'intervention par un client';
		$contenu = $this->client->cli_societe."a demandé l'intervention suivante: $this->int_description \n merci de confirmer";
		mail($tech_email, $sujet, $contenu , $entetemail); 
		$result.= $liste['CON_EMAIL'];
		
	}
	
	function show($param='', $pay='free')
	{	
		$client = new client($this->cli_id);
		if ($param == 'no_rapport')
		{
			$retour='<a class="bouton" href="index2.php?contenu=viewtask&vue=client&cli_id='.$client->cli_id.'">Retour</a>';
		}
		elseif($param == 'show_rapport' or $param == 'show_rapport_no_modif')
		{
			$rapport ='
	<p>Temps passé: '.$this->get_int_tps_passe().'</p>
	<p>Statut: '.$this->int_status.'</p>
	<p>Rapport: '.$this->get_int_rapport().'</p>';
			if ($param == 'show_rapport')
			{
			$rapport .='
	<form method="post" action="./index.php?contenu=edit_task">
		<input type="hidden" name="int_id" value = "'.$this->int_id.'">
		<input type="submit" value="Modifier le rapport">
	</form>'. 
  $this->write_pdf();
			$retour='<a class="bouton" href="index2.php?contenu=viewtask">Retour</a>';
			}
			elseif($param == 'show_rapport_no_modif')
			{
				$retour='<a class="bouton" href="index2.php?contenu=viewtask&vue=client&cli_id='.$client->cli_id.'">Retour</a>';
			}
		}
		else
		{
			$rapport ='
	<form method="post" action="./index.php?contenu=edit_task">
		<input type="hidden" name="int_id" value = "'.$this->int_id.'">
		<input type="submit" name="'.$pay.'" value="Rédiger le rapport">
	</form>
	<form method="post" action="./index2.php?contenu=prevenir_client">
		<input type="hidden" name="int_id" value = "'.$this->int_id.'">
		<input type="submit" value="Prévenir par mail">
	</form>';
  

			$retour='<a class="bouton" href="index2.php?contenu=viewtask">Retour</a>';
			if ($_SESSION['ID']== 1 or $_SESSION['ID']==2)
			{
				$rapport .='
		<form method="post" action="./index.php?contenu=edit_task_header">
			<input type="hidden" name="int_id" value = "'.$this->int_id.'">
			<input type="submit" value="Modifier la demande">
		</form>';
			}
		}
		$SHOW='
<div class="nouveau">&nbsp;</div>
<div class="presentation">
	<h2>Client: '.$client->get_cli_societe().'</h2>
	<p>Technicien: '.$this->get_technicien().'</p>
	<p>Date d\'intervention: '.$this->get_int_date_intervention().'</p>
	<p>Heure d\'intervention: '.$this->get_int_heure_intervention().'</p>
	<p>Description de l\'intervention: '.$this->get_int_description().'</p>
	'.$rapport.' '.$retour.'
</div>';
		return $SHOW;
	}

	function display_line() {
    
		if ($this->int_status != 'A traiter') 
		{
			$color= ' bgcolor="#888888"';
		} elseif ($this->int_date_intervention < time()) {
			$color=' bgcolor="#DD5555"';
		} else {
      $color='';
    }
    if ($this->dev_id > 0) {
      $devisColor = ' bgcolor="#55DD55"';
    } else {
      $devisColor = '';
    }



		$DISPLAY = '
		<tr'.$color.'>
			<td> <input type="checkbox" name="SELECTION[]" value="'.$this->int_id.'" id="'.$this->int_id.'"> </td>
			<td>'.$this->get_int_date_intervention().'</td>
			<td>'.$this->get_int_heure_intervention().'</td>
			<td>'.$this->int_technicien.'</td>
			<td>'.$this->get_int_tps_passe().'</td>
			<td>'.$this->client->cli_societe.'</td>
			<td'.$devisColor.'><a href="index2.php?contenu=showtask&int_id='.$this->int_id.'">'.$this->int_description.'</a></td>
		</tr>';
		return $DISPLAY;
	}

  function delete()
  {
    $sql = "DELETE FROM T_INTERVENTION WHERE INT_ID='$this->int_id';"; 
    mysqlinforezo();
    $query = mysql_query($sql) or die(sql_error('intervention', 'update', 1, $sql));
    mysql_close();
    return "<p> Intervention n° $this->int_id Supprimée. </p>";
  }

		function write_pdf()
		{
			$file = 'tmp/inter-'.$this->int_id.'.pdf';
			
			$date = $this->get_int_date_intervention();
			$heure = $this->get_int_heure_intervention();
			$technicien = $this->get_technicien();
			$temps = $this->get_int_tps_passe();
      $desc = utf8_decode($this->int_description);
      $rapport = utf8_decode($this->int_rapport);

			$pdf=new PDF_MC_Table();
			$pdf->AddPage();
			//logo
			$pdf->Image('images/logo.jpg',90,8,45);
      // titre
			$pdf->SetFont('Arial','B',12);
      $pdf->Ln(60);
			$pdf->Cell(60,10,'DATE',1, 0, 'C');
			$pdf->Cell(40,10,'HEURE',1, 0, 'C');
			$pdf->Cell(40,10,'TECHNICIEN',1, 0, 'C');
			$pdf->Cell(40,10,'TEMPS',1, 1, 'C');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(60,10,utf8_decode($date),1);
			$pdf->Cell(40,10,utf8_decode($heure),1);
			$pdf->Cell(40,10,$technicien,1);
			$pdf->Cell(40,10,$temps,1, 1);
			//  Description
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(180,10,'Description',1, 1, 'C');
			$pdf->SetFont('Arial','',12);
      $pdf->MultiCell(180,10,$desc, 1, 1);
			//  Rapport
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(180,10,'Rapport',1, 1, 'C');
			$pdf->SetFont('Arial','',12);
      $pdf->MultiCell(180,10,$rapport, 1, 1);
      // Signature client
      $pdf->Ln(10);
      $pdf->Cell(180,10,'Signature du client:');

			
			$pdf->Output($file);
			$result = '<form method = "get" action = "'.$file.'">
					<input type="submit" name="print" value = "Imprimer">
				</form>';
			return $result;
		}
}

class liste_inter
{
	var $nb_inter;		      // nombre d'interventions dans la liste
	var $tableau_inter; 	  // un tableau d'objet intervention
	var $caption;		        // Titre de la liste d'intervention
	
	
	function liste_inter($tab_inter = array())
	{
		if (!empty($tab_inter))
		{
			$liste = implode(",", $tab_inter);
   			$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_INTERVENTION.INT_ID, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE, T_INTERVENTION.INT_DEPLACEMENT as INT_DEPLACEMENT, T_INTERVENTION.CLI_ID, T_INTERVENTION.USE_ID, T_INTERVENTION.INT_CREATEUR_ID, T_INTERVENTION.INT_TPS_PASSE, UNIX_TIMESTAMP(T_INTERVENTION.INT_DATE_INTERVENTION) AS INT_DATE_INTERVENTION, T_CLIENT.CLI_SOCIETE, T_USER.USE_PRENOM FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID WHERE T_INTERVENTION.INT_ID IN ($liste) ORDER BY T_INTERVENTION.INT_DATE_INTERVENTION DESC, T_INTERVENTION.INT_DATE_CREA;";
			mysqlinforezo();
			$query= mysql_query($sql) or die (sql_error('liste_inter', 'select('.$vue.')', 1, $sql));
			while ($liste = mysql_fetch_array($query))
			{
				$inter=new intervention;
				$inter->init_from_liste($liste);
				$this->tableau_inter[] = $inter;
				$this->nb_inter = count($this->tableau_inter);
			}	 
		}
		
	}

	function select($vue='mes_quoti', $temps='', $statut='', $technicien='', $date_debut='', $date_fin='', $cli_id='',$devis='')
	{
		if (empty($vue)) $vue = 'mes_quoti';
		if ($vue=='mes_quoti')
		{
			$this->caption= "Mes interventions du jours";
			$where='WHERE T_INTERVENTION.USE_ID='.$_SESSION['ID'].' AND TO_DAYS(T_INTERVENTION.INT_DATE_INTERVENTION)<=TO_DAYS(NOW()) AND INT_STATUS="A traiter" ';
		}
		elseif($vue=='mes_hebdo')
		{
			$this->caption= "Mes interventions de la semaine";
			$where='WHERE T_INTERVENTION.USE_ID='.$_SESSION['ID'].' AND TO_DAYS(T_INTERVENTION.INT_DATE_INTERVENTION)-TO_DAYS(NOW())<=7 AND INT_STATUS="A traiter" ';
		}
		elseif($vue=='mes_toutes')
		{
			$this->caption= "Toutes mes interventions";
			$where='WHERE T_INTERVENTION.USE_ID='.$_SESSION['ID'].' AND INT_STATUS="A traiter" ';
		}
		elseif($vue=='toutes_quoti')
		{
			$this->caption= "Toutes les interventions du jour";
			$where='WHERE TO_DAYS(T_INTERVENTION.INT_DATE_INTERVENTION)<=TO_DAYS(NOW()) AND INT_STATUS="A traiter" ';
		}
		elseif($vue=='toutes_hebdo')
		{
			$this->caption= "Toutes les interventions de la semaine";
			$where='WHERE TO_DAYS(T_INTERVENTION.INT_DATE_INTERVENTION)-TO_DAYS(NOW())<=7 AND INT_STATUS="A traiter" ';
		}
		elseif($vue=='toutes_traiter')
		{
			$this->caption= "Toutes les interventions à traiter";
			$where='WHERE INT_STATUS="A traiter" ';
		}
		elseif($vue=='toutes_toutes')
		{
			$this->caption= "Toutes les interventions";
		}
    elseif($vue=='devis')
    {
      $this->caption = "Intervention liées à ce devis";
      $where='WHERE DEV_ID="'.$devis.'"';
    }
		elseif($vue=='client')
		{
			$client = new client($_GET['cli_id']);
			$this->caption= $client->cli_societe;
			$where='WHERE T_INTERVENTION.CLI_ID="'.$_GET['cli_id'].'"';
		}
		elseif($vue=='formulaire')
		{
			if (empty($statut))
			{
				echo "il faut cocher au moins 1 case statut (a faire ou terminé)";
				return 0;
			}
			foreach ($statut as $stat)
			{
				$liste_status .= "'".$stat."', ";
			}
			$liste_status=substr($liste_status, 0, -2);
			if (empty($technicien))
			{
				echo "il faut cocher au moins 1 case technicien";
				return 0;
			}
			foreach($technicien as $tech)
			{
				$liste_users .= $tech.', ';
			}
			$liste_users=substr($liste_users, 0, -2);
			$date_debut=date_input_to_mysql($date_debut);
			$date_fin = date_input_to_mysql($date_fin);
			
			if ($temps=='jour') $where = 'WHERE (DATE(INT_DATE_INTERVENTION)=CURDATE() OR (INT_STATUS=\'A traiter\' AND DATE(INT_DATE_INTERVENTION) < CURDATE())) AND';
			elseif ($temps == 'semaine') $where = 'WHERE YEARWEEK(INT_DATE_INTERVENTION) = YEARWEEK(CURDATE()) AND';
			elseif ($temps == 'mois') $where = 'WHERE MONTH(INT_DATE_INTERVENTION) = MONTH(CURDATE()) AND YEAR(INT_DATE_INTERVENTION) = YEAR(CURDATE()) AND';
			elseif ($temps == 'tout') $where = 'WHERE';
			elseif ($temps == 'custom')$where = "WHERE DATE(INT_DATE_INTERVENTION) BETWEEN '$date_debut' AND '$date_fin' AND";

			$where .=" INT_STATUS IN ($liste_status)";
			if ($technicien!='tous') $where .=" AND T_INTERVENTION.USE_ID IN ($liste_users)";
			if($cli_id != 'tous') $where .=" AND T_CLIENT.CLI_ID = $cli_id";
		}
   		$sql = "SELECT T_INTERVENTION.INT_DESCRIPTION, T_INTERVENTION.INT_ID, T_INTERVENTION.INT_STATUS, T_INTERVENTION.INT_TYPE, T_INTERVENTION.INT_DEPLACEMENT, T_INTERVENTION.CLI_ID, T_INTERVENTION.USE_ID, T_INTERVENTION.INT_CREATEUR_ID, T_INTERVENTION.INT_TPS_PASSE, T_INTERVENTION.DEV_ID, UNIX_TIMESTAMP(T_INTERVENTION.INT_DATE_INTERVENTION) AS INT_DATE_INTERVENTION, T_CLIENT.CLI_SOCIETE, T_USER.USE_PRENOM FROM (T_INTERVENTION INNER JOIN T_CLIENT ON T_INTERVENTION.CLI_ID=T_CLIENT.CLI_ID) INNER JOIN T_USER ON T_INTERVENTION.USE_ID=T_USER.USE_ID $where ORDER BY T_INTERVENTION.INT_DATE_INTERVENTION DESC, T_INTERVENTION.INT_DATE_CREA;";
		mysqlinforezo();
		$query= mysql_query($sql) or die (sql_error('liste_inter', 'select('.$vue.')', 1, $sql));
		while ($liste = mysql_fetch_array($query))
		{
			$inter=new intervention;
			$inter->init_from_liste($liste);
			$this->tableau_inter[] = $inter;
			$this->nb_inter = count($this->tableau_inter);
		}	 
	}


	function display_menu_inter($temps='jour', $statut=array("STATUS"=>"A traiter"), $technicien=array("TECHNICIEN"=>""), $date_debut="", $date_fin="", $cli_id='')
	{
		$check = $this->test_check($temps, $statut);
		$DISPLAY = '<div class = "bandeau"><h1>Interventions</h1><h2>'.$this->caption.'</h2></div>
		<span class="clear">&nbsp;</span>
	<div class="nouveau">
		<form method="post" action="./index2.php?contenu=viewtask&vue=formulaire">
		<div>	
			<label for="jour">Jour</label><input type="radio" name="TEMPS" value="jour" id="jour" '.$check['jour'].'> 
			<label for="semaine">Semaine</label><input type="radio" name="TEMPS" value="semaine" id="semaine" '.$check['semaine'].'> 
			<label for="mois">Mois</label><input type="radio" name="TEMPS" value="mois" id="mois" '.$check['mois'].'>
			<label for="tout">Tout</label><input type="radio" name="TEMPS" value="tout" id="tout" '.$check['tout'].'>
			<label class="short" for="date_debut">du</label><input type="text" name="date_debut" id="date_debut" size="8" maxlength="8" value="'.$date_debut.'">
			<input type="radio" name="TEMPS" value="custom" id="tout" '.$check['custom'].'>
			<label class="short" for="date_fin">au</label><input type="text" name="date_fin" id="date_fin" size="8" maxlength="8" value="'.$date_fin.'">
		</div>
		<div>
			<label for="a_traiter">A traiter</label><input type="checkbox" name="STATUS[]" value="A traiter" id="a_traiter" '.$check['a_traiter'].'>
			<label for="termine">Terminé</label><input type="checkbox" name="STATUS[]" value="Terminé" id="termine" '.$check['termine'].'>
		</div>
		<div>
			'.MakeCheckboxUsers($technicien).'
		</div>
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
	
	function test_check($temps, $statut=array("STATUS"=>"A traiter"))
	{
    $check_jour = $check_semaine = $check_mois = $check_tout = $check_custom = $check_a_traiter = $check_termine = "";
		if ($temps=='jour') $check_jour = 'checked';
		elseif($temps=='semaine') $check_semaine = 'checked';
		elseif($temps=='mois') $check_mois = 'checked';
		elseif($temps=='tout') $check_tout = 'checked';
		elseif($temps=='custom') $check_custom = 'checked';
		if (!empty($statut))
		{
			if (in_array('A traiter', $statut)) $check_a_traiter = 'checked';
			if (in_array('Terminé', $statut)) $check_termine = 'checked';
		}
		return array("jour"=>$check_jour, "semaine"=>$check_semaine, "mois"=>$check_mois, "tout"=>$check_tout, "custom"=>$check_custom, "a_traiter"=>$check_a_traiter, "termine"=>$check_termine);
	}

	function display_menu_inter_client($temps='jour', $statut=array("STATUS"=>"A traiter"), $date_debut="", $date_fin="")
	{
		$check = $this->test_check($temps, $statut);
		$DISPLAY = '
		<div class = "bandeau"><h1>Interventions</h1><h2>'.$this->caption.'</h2></div>
		<span class="clear">&nbsp;</span>
	<div class="nouveau">
		<form method="post" action="./index2.php?contenu=viewtask&vue=formulaire">
		<div>	
			<label for="jour">Jour</label><input type="radio" name="TEMPS" value="jour" id="jour" '.$check['jour'].'> 
			<label for="semaine">Semaine</label><input type="radio" name="TEMPS" value="semaine" id="semaine" '.$check['semaine'].'> 
			<label for="mois">Mois</label><input type="radio" name="TEMPS" value="mois" id="mois" '.$check['mois'].'>
			<label for="tout">Tout</label><input type="radio" name="TEMPS" value="tout" id="tout" '.$check['tout'].'>
			<label class="short" for="date_debut">du</label><input type="text" name="date_debut" id="date_debut" size="8" maxlength="8" value="'.$date_debut.'">
			<input type="radio" name="TEMPS" value="custom" id="tout" '.$check['custom'].'>
			<label class="short" for="date_fin">au</label><input type="text" name="date_fin" id="date_fin" size="8" maxlength="8" value="'.$date_fin.'">
		</div>
		<div>
			<label for="a_traiter">A traiter</label><input type="checkbox" name="STATUS[]" value="A traiter" id="a_traiter" '.$check['a_traiter'].'>
			<label for="termine">Terminé</label><input type="checkbox" name="STATUS[]" value="Terminé" id="termine" '.$check['termine'].'>
		</div>
		<div>
			<input type="submit" value="Chercher">
		</div>
	</form>
		</div>
		';
		return $DISPLAY;
	}
	
	function display_liste_inter($standalone=true)
	{
    if (!$standalone) {
      $DISPLAY = '<div class="formadmin">';
    } else {
      $DISPLAY = '';
    }
		$DISPLAY.='
	<table border>
		<caption>
      <div class="legdev">
        tache liée à un devis
      </div>
			<div class="legter">
				tache terminé
			</div>
			<div class="legret">
				tache a traiter en retard
			</div>
				Tache a traiter
		</caption>
		<form method="post" action="./index2.php?contenu=change_selected_task">
		<tr>
			<th> <input type="checkbox" value="0" id="tous" onClick="this.value=check(\'SELECTION[]\')"> </th>
			<th>Date</th><th>Heure</th><th>Technicien</th><th>Temps Passé</th><th>Client</th><th>Description</th>
		</tr>';
		$tps_total=0;
    if (empty($this->tableau_inter)) {
      $DISPLAY = "Aucune intervention ne correspond au critères définits";
      return $DISPLAY;
    }
    $nbDepl = 0;
    $nbNone = 0;
    $nbInter = 0;
		foreach ($this->tableau_inter as $inter)
		{
			$DISPLAY.= $inter->display_line();
			$tps_total+= $inter->int_tps_passe;
      if ($inter->int_deplacement == 'Déplacement' or $inter->int_deplacement == 'Grand Déplacement') {
        $nbDepl++;
      } else if ($inter->int_deplacement == '') {
        $nbNone++;
      }
      $nbInter++;
		}
		$temps = euclidivision($tps_total, 60);
		$total = sprintf("%d h %02d",$temps[0], $temps[1]);
    $nonRenseigne = '';
    if ($nbNone > 0) {
      $nonRenseigne = "($nbNone non renseignées)";
    }
		
		$DISPLAY .= '
		<tr>
			<th> <input type="checkbox" value="0" id="tous" onClick="this.value=check(\'SELECTION[]\')"> </th>
			<th>Date</th><th>Heure</th><th>Technicien</th><th>'.$total.'</th><th>Client</th><th>'.$nbDepl.' Déplacement/ '.$nbInter .' Interventions ('.number_format($nbDepl/$nbInter * 100, 0).'%)'.$nonRenseigne.'</th>
		</tr>
		</table>
		<input type="submit" name="print" value="Imprimer" id="print">
		<input type="submit" name="delete" value="Supprimer" id="delete">
		</form>';
    if (!$standalone) {
      $DISPLAY .= '</div>';
    }
		return $DISPLAY;
	}

  function viewCalendar() {
    
  }

  function createCalendar()
  {
    $v = new vcalendar(); // create a new calendar instance
    $v->setConfig( 'unique_id', 'icaldomain.com' ); // set Your unique id
    $v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
    $vevent = new vevent(); // create an event calendar component
    $vevent->setProperty( 'dtstart', array( 'year'=>2007, 'month'=>4, 'day'=>1, 'hour'=>19, 'min'=>0,  'sec'=>0 ));
    $vevent->setProperty( 'dtend',  array( 'year'=>2007, 'month'=>4, 'day'=>1, 'hour'=>22, 'min'=>30, 'sec'=>0 ));
    $vevent->setProperty( 'LOCATION', 'Central Placa' ); // property name - case independent
    $vevent->setProperty( 'summary', 'PHP summit' );
    $vevent->setProperty( 'description', 'This is a change' );
    $vevent->setProperty( 'comment', 'This is a comment' );
    $vevent->setProperty( 'attendee', 'attendee1@icaldomain.net' );
    $v->setComponent ( $vevent ); // add event to calendar
    $vevent = new vevent();
    $vevent->setProperty( 'dtstart', '20070401', array('VALUE' => 'DATE'));// alt. date format, now for an all-day event
    $vevent->setProperty( "organizer" , 'boss@icaldomain.com');
    $vevent->setProperty( 'summary', 'ALL-DAY event' );
    $vevent->setProperty( 'description', 'This is a description for an all-day event' );
    $vevent->setProperty( 'resources', 'COMPUTER PROJECTOR' );
    $vevent->setProperty( 'rrule', array( 'FREQ' => 'WEEKLY', 'count' => 4));// occurs also four next weeks
    $vevent->parse( 'LOCATION:1CP Conference Room 4350' );// supporting parse of strict rfc2445 formatted text
    $v->setComponent ( $vevent ); // add event to calendar
    //$v->returnCalendar(); // redirect calendar file to browser
    $v->setConfig( 'directory', 'tmp' ); // set directory
    $v->setConfig( 'filename', 'calendar.ics' ); // set file name
    $v->saveCalendar(); // save calendar to file

  }

	function make_pdf()
	{
		$file = 'tmp/inters.pdf';
		
		$pdf=new PDF_MC_Table();
		$pdf->AddPage();
		//logo
		$pdf->Image('images/logo.jpg',10,8,33);
		//espacement
		$pdf->Ln(10);
		$pdf->Cell(90);
		//INTERVENTIONS
		$pdf->SetFont('Arial','B',20);
		$pdf->Cell(60,10,'INTERVENTIONS',0,1,'C');
		$pdf->ln(15);
		
			//données
		foreach ($this->tableau_inter as $intervention)
		{
			//coordonnés société
			$pdf->SetFont('Times','',12);
			$client = new client($intervention->cli_id);
			$coords = $client->cli_societe."\n".$client->cli_adresse."\n".$client->cli_code_postal.' '.$client->cli_ville;
			$coords = utf8_decode($coords);
			$pdf->MultiCell(190,10,$coords,1,1);
			$pdf->MultiCell(190,10,utf8_decode($intervention->int_description), 1,1);
			$pdf->ln(5);
		}
		//TOTAL
		
		$pdf->Output($file);
		$result = '<form method = "get" action = "'.$file.'">
				<input type="submit" name="print" value = "Imprimer">
			</form>';
		return $result;
	}
}
?>

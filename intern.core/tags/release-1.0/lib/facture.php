<?php
include_once "lib/pdf.php";
include_once "lib/inforezo.php";

	class facture {
    var $fac_id;
    var $client;
    var $contact; 
    var $fac_titre;
    var $fac_info;
    var $fac_date_crea;
    var $fac_statut;
    var $fac_suivi;
    var $tab_lfa;
    var $fac_date_suivi;


      function facture( $arg=array("FAC_ID"=>"0",
                                   "CLI_ID"=>"0",
                                   "CON_ID"=>"0", 
                                   "FAC_TITRE"=>"Nouvelle facture", 
                                   "FAC_INFO"=>"Pour information",
                                   "FAC_STATUT"=>"À imprimer",
                                   "FAC_SUIVI"=>"Aucun")) {       
        if(is_int($arg)) {
          $sql = "SELECT FAC_ID, CLI_ID, CON_ID, FAC_TITRE, FAC_INFO, UNIX_TIMESTAMP(FAC_DATE_CREA) AS FAC_DATE_CREA, FAC_STATUT, FAC_SUIVI, UNIX_TIMESTAMP(FAC_DATE_SUIVI) AS FAC_DATE_SUIVI FROM T_FACTURE WHERE FAC_ID = '$arg';";
          mysqlinforezo();
          $query = mysql_query($sql) or die (sql_error('facture', 'facture (constructeur)', 1, $sql));
          $liste = mysql_fetch_array($query);
        } elseif(is_array($arg)) {
          $liste = $arg;
        }
        if (is_array($liste)) {
          $this->fac_id = $liste['FAC_ID'];
          $this->client = new client($liste['CLI_ID']);
          $this->client->init_all_contact();
          $this->contact = $liste['CON_ID'];
          $this->fac_titre = $liste['FAC_TITRE'];
          $this->fac_info = $liste['FAC_INFO'];
          $this->fac_date_crea = $liste['FAC_DATE_CREA'];
          $this->fac_statut = $liste['FAC_STATUT'];
          $this->fac_suivi = $liste['FAC_STATUT'];
          $this->init_tab_lfa();
        }
      } 
		
		function set_form_values($liste)
		{
			if (is_array($liste))
			{
				$this->client->cli_id = $liste['cli_id'];
				$this->contact = $liste['fac_contact'];
				$this->fac_titre = str_replace("\'", "¤",$liste['fac_titre']);
				$this->fac_acompte = str_replace("\'", "¤",$liste['fac_acompte']);
				$this->fac_info = str_replace("\'", "¤",$liste['fac_info']);
				$this->set_fac_statut($liste['fac_statut']);
				foreach ($this->tab_lfa as $no_ligne => $ligne_facture)
				{
					$this->tab_lfa[$no_ligne]->lfa_type = $liste['lfa_type'.$no_ligne];
					$this->tab_lfa[$no_ligne]->lfa_designation = str_replace("\'", "¤",$liste['lfa_designation'.$no_ligne]);
					$this->tab_lfa[$no_ligne]->lfa_no_serie = str_replace("\'", "¤",$liste['lfa_no_serie'.$no_ligne]);
					$this->tab_lfa[$no_ligne]->lfa_qtt = str_replace("\'", "¤",$liste['lfa_qtt'.$no_ligne]);
					if (isset($liste['lfa_pu_achat'.$no_ligne]))$this->tab_lfa[$no_ligne]->lfa_pu_achat = $liste['lfa_pu_achat'.$no_ligne];
					if (isset($liste['lfa_prix_ht'.$no_ligne]))$this->tab_lfa[$no_ligne]->lfa_prix_ht = $liste['lfa_prix_ht'.$no_ligne];
				}
				$result = 0;
			}
			else
			{
				$result = "Le parametre n'est pas une liste";
			}
			return $result;
		}

		function init_tab_lfa() { 
      $sql = "SELECT * FROM T_LIGNE_FACTURE NATURAL JOIN T_TYPE WHERE FAC_ID = '$this->fac_id' ORDER BY LFA_ID;";
      mysqlinforezo();
      $query = mysql_query($sql) or die (sql_error('facture', 'init_tab_lfa', 1 , $sql)); 
      mysql_close();
      while ($liste=mysql_fetch_array($query)) {       
        $this->tab_lfa[]= new ligne_facture($liste);
      }
		}

		function get_fac_titre()
		{
			$result = str_replace("¤","'",$this->fac_titre);
			return $result;
		}

		function get_fac_numero()
		{
			$result = $this->fac_id;
			$result = "F0708".sprintf("%03d",$result);
			return $result;
		}

		function get_fac_info()
		{
			$result = str_replace("¤","'",$this->fac_info);
			return $result;
		}
		
		function get_date_crea()
		{
			 setlocale (LC_ALL, "fr_FR.utf8");
			if(empty($this->fac_date_crea))
				$result='&nbsp;';
			else {
        $d = explode('-', $this->fac_date_crea);
        if (isset($d[1]))
          $result = $d[2] . '/' . $d[1] . '/' . $d[0];
        else {
          $result = strftime("%d/%m/%Y", $this->fac_date_crea);
        }
      }
			return $result;
		}
		
		function get_date_reglement()
		{
		/*if ($this->client->cli_echeances == 'A reception')
				$date =  $this->get_date_crea();
			elseif ($this->client->cli_echeance == '30 jours à reception')
			{
				$date_ = strftime($this->fac_date_crea)
				$date =  mktime(0,0,0,date("m" ) + 6  ,date("d" ) ,date("Y" ));
				$date = date($this->get_date_crea + 30;
			}
			elseif ($this->client->cli_echeances == '30 jours fin de mois le 10')
				$date = $this->get_date_crea();
			elseif ($this->client->cli_echeances == '30 jours fin de mois')
				$date = $this->get_date_crea();
				
			elseif ($this->client->cli_echeances == '60 jours fin de mois le 10')
				$date = $this->get_date_crea();
			elseif ($this->client->cli_echeances == '60 jours fin de mois')
				$date = $this->get_date_crea();
*/
      return $this->get_date_crea();
		}

    function get_nb_copy()
    {
      return $this->client->cli_nb_facture;
    }

    function set_fac_statut($statut) {
      $suivi_statut = array('Lettre simple', 'Lettre AR');
      $this->fac_statut = $statut;
      if (in_array($statut, $suivi_statut))
        $this->fac_date_suivi = time();
    }
						 
		function get_contact()
		{
			$responsable = new contact($this->contact);
			$result = $responsable->con_prenom.' '.$responsable->con_nom;
			return $result;
		}

		function get_interlocuteur()
		{
			$result = $_SESSION['prenom'].' '.$_SESSION['nom'];
			return $result;
		}

		function get_totalHT()
		{
			$total = 0;
			foreach ($this->tab_lfa as $ligne)
			{
				$total += $ligne->lfa_prix_ht*$ligne->lfa_qtt;
			}
			return $total;
		}
    
    function addTotalHT(&$array) {
      foreach ($this->tab_lfa as $ligne) {
        if (isset($array[$ligne->get_lfa_type_libelle()])) {
          $array[$ligne->get_lfa_type_libelle()]['Chiffre d\'affaire'] += $ligne->get_lfa_prix_ht() * $ligne->get_lfa_qtt();
          $array[$ligne->get_lfa_type_libelle()]['Marge brute'] += $ligne->get_lfa_marge();
          $array[$ligne->get_lfa_type_libelle()]['Achats'] += $ligne->get_lfa_prix_achat();
        } else {
          $array[$ligne->get_lfa_type_libelle()]['Chiffre d\'affaire'] = $ligne->get_lfa_prix_ht() * $ligne->get_lfa_qtt();
          $array[$ligne->get_lfa_type_libelle()]['Marge brute'] = $ligne->get_lfa_marge();
          $array[$ligne->get_lfa_type_libelle()]['Achats'] = $ligne->get_lfa_prix_achat();
        }
      }
      return $array;
    }

    function get_totalTTC()
    {
      $total = 0;
      foreach ($this->tab_lfa as $line) {
        $total += $line->get_lfa_ptttc();
      }
      return $total;
    }

		function get_total_marge()
		{
			$total = 0;
			foreach ($this->tab_lfa as $ligne)
			{
				$total += $ligne->get_lfa_marge();
			}
			return $total;
		}

    function edit() {
      if (!isAllowed('FactureModification') && isset($this->fac_statut) && $this->fac_statut != 'À imprimer') {
        return ($this->preview() . $this->write_pdf());
      }
			if ($this->fac_id==0) {
				$TITRE='<h2>Nouvelle facture pour '.$this->client->cli_societe.'</h2>';
			} else {	
				$TITRE='<h2>Editer la facture de '.$this->client->cli_societe.'</h2>'; 
			}
			$SELECT_CONTACT=$this->client->MakeSelectContact($this->contact, 'fac_contact');
			$liste_statut = funcEnumList("T_FACTURE", "FAC_STATUT");
			$SELECT_STATUT = funcMakeFormList( 'fac_statut', $liste_statut, $this->fac_statut, 'statut');
			$objet_facture = serialize($this);
			$ENTETE= '
				<input type="hidden" name="objet_facture" value = \''.$objet_facture.'\'>
				<div><label for="fac_titre">Titre : </label> <input type="text" name="fac_titre" size="50" value = "'.$this->get_fac_titre().'"maxlength=50 id="fac_titre"></div>
				<div><label for="cli_id"> Client: </label>'.MakeSelectCustomers($this->client->cli_id).'</div>
				<div><label for="responsable">Responsable : </label> '.$SELECT_CONTACT.' </div> 
				<div><label for="statut">Statut : </label> '.$SELECT_STATUT.' </div> 
				<div><label for="info">Pour info : </label> <input type="text" name="fac_info" size="50" value = "'.$this->fac_info.'" maxlength=200 id="info"></div>
				';
			$CORPS='<h2> les lignes de la facture</h2>';
			$CORPS.='
        <table class="facture">
          <tr>
            <th> Type</th>
            <th> Désignation</th>
            <th> N° de série </th>
            <th> Qtt</th>
            <th> Prix achat</th>
            <th> PU HT</th>
            <th> PT HT</th>
            <th> PT TTC</th>
          </tr>';
			foreach ($this->tab_lfa as $no_ligne => $ligne_facture) {
				$CORPS .= $ligne_facture->edit($no_ligne);
			}
			$CORPS .= '</table>';
			$PIED = '
				<input '. $disable .'type="submit" name="new_line" value="Nouvelle ligne">
				<input '. $disable .'type="submit" name="save" value="Enregister">
				<input '. $disable .'type="submit" name="preview" value="Aperçu">
				<input type="submit" name="back" value="Retour">
				';
			$EDIT ='
			 	<span class="clear">&nbsp;</span>
				<div class="formadmin">
					<form method="post" action="./index2.php?contenu=facture" name="edit_facture">'
						.$TITRE
						.$ENTETE.'<span class="clear">&nbsp;</span>'
			 			.$CORPS.'<span class="clear">&nbsp;</span>'
						.$PIED
					.'</form>	
				</div>';
			return $EDIT;
    }
		
		function preview() { 
			$ENTETE='<img class="logo_facture" src="images/logo.gif" alt="logo inforezo">
				<div class="coor_client">
					<p>'.$this->client->cli_societe.'</p>
					<p>'.$this->client->cli_adresse.'</p>
					<p>'.$this->client->cli_code_postal.' '.$this->client->cli_ville.'</p>
				</div>
				<div class="facture">FACTURE</div>			
				<span class="clear">&nbsp;</span>
				<table border cellspacing=0 class="presentation-devis">
					<tr>
						<th>Facture N°</th>
						<th>Date Création</th>
						<th>A l\'attention de</th>
						<th>Interlocuteur</th>
						<th>Page n°</th>
					</tr>
					<tr>
						<td>'.$this->get_fac_numero().'</td>
						<td>'.$this->get_date_crea().'</td>
						<td>'.$this->get_contact().'</td>
						<td>'.$this->get_interlocuteur().'</td>
						<td>1</td>
					</tr>
				</table>
				<h1>'.$this->get_fac_titre().'</h1>';
				$CORPS='
				<table border cellspacing=0 rules="cols" class="presentation-facture">
					<thead>
					<tr class="presentation-facture">
						<th>Libellé</th>
						<th>Qté</th>
						<th>PU HT</th>
						<th>PT HT</th>
					</tr>
					</thead>
					<tbody>';
				foreach ($this->tab_lfa as $ligne_facture)
				{
					if ($ligne_facture->lfa_type != 'Option')
						$TOTAL+= $ligne_facture->lfa_qtt * $ligne_facture->lfa_prix_ht;
					$STOTAL+= $ligne_facture->lfa_qtt * $ligne_facture->lfa_prix_ht;
					if ($STOTAL != 0 && $ligne_facture->lfa_type=='Titre') 
					{
						$STOTAL = number_format($STOTAL, 2, ',', ' ');
						$CORPS.='<tr style="text-align: right;"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><strong>'.$STOTAL.' €</strong></td></tr>';
						$STOTAL = 0;
					}
					$CORPS.= $ligne_facture->preview();
				}
				$STOTAL = number_format($STOTAL, 2, ',', ' ');
				$CORPS.='<tr style="text-align: right;"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><strong>'.$STOTAL.' €</strong></td></tr>';
				$TVA = $TOTAL * 0.196;
				$TOTAL_TTC = number_format($TOTAL + $TVA, 2, ',', ' ');
				$TVA = number_format($TVA, 2, ',', ' ');
				$TOTAL = number_format($TOTAL, 2, ',', ' ');
				$CORPS.='	
					</tbody>
				</table>
				<table border cellspacing=0 style="float: right; width: 30%;">
					<tr><th colspan=2 style="text-align: center;">MONTANT DE L\'OFFRE</th></tr>
					<tr><th style="text-align: left;">Montant H.T</th><td>'.$TOTAL.' €</td></tr>
					<tr><th style="text-align: left;">T.V.A: 19.6%</th><td>'.$TVA.' €</td></tr>
					<tr><th style="text-align: left;">Montant T.T.C</th><td>'.$TOTAL_TTC.' €</td></tr>
				</table>
				<div class="info">
					<h3>Pour info:</h3>
					<p>'.$this->fac_info.'</p>
				</div>
				';
				$FOOTER='
				<div class="footer">
					10 rue Victor Grignard 42 000 Saint-Etienne Tel: 0477 924 891 Fax: 0477 912 309 contact@inforezo.com www.inforezo.com SARL de 8000 € Siren-Siret: 442 304 374 00014 /APE: 5829c
				</div>';
				$objet_facture = serialize($this);
				$ACTION='
				<form method="post" action="./index2.php?contenu=facture">
					<input type="hidden" name="objet_facture" value = \''.$objet_facture.'\'>
					<input type="submit" name="back_from_preview" value = "Retour">
				</form>';
			$PREVIEW = 
		 		'<span class="clear">&nbsp;</span>
				<div class="formadmin">'
					.$TITRE
					.$ENTETE
					.$CORPS
					.$FOOTER.
				'</div>'
				.$ACTION;
			return $PREVIEW;
		}

		function enreg()
		{
			$date = date('Y-m-d');
			if (empty($this->fac_titre))
			{
				return 'La facture n\'est pas enregistrée, il faut lui donner un titre';
			}
			if (!isset($this->fac_id))
			{
				$cli_id = $this->client->cli_id;
				$sql = "SELECT FAC_ID FROM T_FACTURE WHERE CLI_ID = '$cli_id' AND FAC_TITRE = '$this->fac_titre';";
				mysqlinforezo();
				$query = mysql_query($sql) or die(sql_error('facture', 'enreg', 1, $sql));
				$liste =  mysql_fetch_array($query);
				if (mysql_num_rows($query) > 0) {
          $this->fac_id = $liste['FAC_ID'];
					return 'la facture est déja enregistrée';
        }
				$this->fac_date_crea = time();
				$cli_id = $this->client->cli_id;
				$sql = "INSERT INTO T_FACTURE (FAC_ID, CLI_ID, CON_ID, FAC_TITRE, FAC_INFO, FAC_DATE_CREA, FAC_STATUT) VALUES ('', '$cli_id', '$this->contact', '$this->fac_titre', '$this->fac_info', '$date', '$this->fac_statut');";
				mysql_query($sql) or die(sql_error('facture', 'enreg', 1, $sql));
				$this->fac_id=mysql_insert_id();
				mysql_close;
				foreach ($this->tab_lfa as $no_ligne => $ligne_facture)
				{
					$ligne_facture->fac_id = $this->fac_id;
					$ligne_facture->enreg();
				}
				return 'Facture Enregistrée (nouvelle)';
			}
			else
			{
        $date_suivi = date('Y-m-d', $this->fac_date_suivi);
				$cli_id = $this->client->cli_id;
				$sql = "delete from T_LIGNE_FACTURE WHERE FAC_ID=$this->fac_id";
				mysqlinforezo();
				mysql_query($sql) or die (sql_error('facture', 'enreg', 2, $sql));
				$sql = "UPDATE T_FACTURE SET CON_ID='$this->contact', FAC_TITRE='$this->fac_titre', FAC_INFO='$this->fac_info', FAC_STATUT='$this->fac_statut', CLI_ID='$cli_id', FAC_DATE_SUIVI='$date_suivi' WHERE FAC_ID= '$this->fac_id';";
				mysql_query($sql) or die (sql_error('facture', 'enreg', 3, $sql));
				foreach ($this->tab_lfa as $no_ligne => $ligne_facture)
				{
					$ligne_facture->fac_id = $this->fac_id;
					$ligne_facture->enreg();
				}
				return 'Facture enregistrée (modification)';
			}
		}

		function write_pdf($print_form=true)
		{
			$file = 'tmp/facture-'.$this->fac_id.'.pdf';
			
			$pdf = $this->write_pdf_unitaire();
			$pdf->Output($file);
      //autant d'exemplaire que necessaire
      $pdf =& new concat_pdf(); 
      $current_copy = 0;
      while ($current_copy < $this->get_nb_copy()) {
        $array[$current_copy] = $file;
        $current_copy++;
      }
      $pdf->setFiles($array);
      $pdf->concat(); 
      $pdf->Output($file); 
      if ($print_form) {
			$result = '<form method = "get" action = "'.$file.'">
					<input type="submit" name="print" value = "Imprimer">
				</form>';
      }
      else {
        $result = $file;
      }
			return $result;
		}

    function write_letter() {
      $file = 'tmp/lettre-'.$this->fac_id.'.pdf';
      $pdf = new PDF_letter();
      $pdf->addHeader($this->client);
      $pdf->addTitle('Première relance');
      $pdf->addContent('tpl/lettre_simple.tpl', $this);
      $pdf->Output($file);
      $this->set_fac_statut('Lettre simple');
      $this->enreg();
      return ($file);
    }

    function write_AR() {
      $file = 'tmp/AR-'.$this->fac_id.'.pdf';
      $pdf = new PDF_letter();
      $pdf->addHeader($this->client);
      $pdf->addTitle('RELANCE');
      $pdf->addContent('tpl/lettre_AR.tpl', $this);
      $pdf->Output($file);
      $this->set_fac_statut('Lettre AR');
      return ($file);
    }

		function write_pdf_unitaire()
		{
      // Le taux de TVA global de la facture sera le taux de TVA du type de la DERNIERE ligne de la facture!!
      // ce qui implique qu'il faut un taux de TVA uniforme dans une facture!!
      //todo: verifier que le taux de TVA est uniforme sur une facture!

			$date_crea = $this->get_date_crea();
			$date_reglement = $this->get_date_reglement();
			$responsable = utf8_decode($this->get_contact());
			$titre = $this->get_fac_titre();
			$pdf=new PDF_MC_Table();
			$pdf->AddPage();
			//logo
			$pdf->Image('images/logo.jpg',10,8,45);
			//FACTURE
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',40);
			$pdf->Cell(100,10);
			$pdf->Cell(60,10,'FACTURE',0,1,'C');
			//espacement
			$pdf->Ln(10);
			$pdf->Cell(90);
			//coordonnés société
			$pdf->SetFont('Times','',12);
			$coords = $this->client->cli_societe."\n".$this->client->cli_adresse."\n".$this->client->cli_code_postal.' '.$this->client->cli_ville;
			$coords = utf8_decode($coords);
			$pdf->MultiCell(90,7,$coords,0,1);
			$pdf->Ln(10);
			//premier tableau
				//entete
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(30,7,utf8_decode('Facture n°'),1,0,'C');
			$pdf->Cell(40,7,utf8_decode('Date création'),1,0,'C');
			$pdf->Cell(45,7,utf8_decode('A l\'attention de'),1,0,'C');
			$pdf->Cell(45,7,utf8_decode('Interlocuteur'),1,0,'C');
			$pdf->Cell(30,7,utf8_decode('Page n°'),1,0,'C');
			$pdf->Ln();
				//Données
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(30,6,$this->get_fac_numero(),1,0,'C');
			$pdf->Cell(40,6,$date_crea,1,0,'C');
			$pdf->Cell(45,6,$responsable,1,0,'C');
			$pdf->Cell(45,6,utf8_decode($_SESSION['prenom']).' '.utf8_decode($_SESSION['nom']),1,0,'C');
			$pdf->Cell(30,6,$pdf->PageNo(),1,0,'R');
			$pdf->Ln();
			//titre facture
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(0,10,utf8_decode($titre),0,1,'C');
			
			//Deuxieme tableau
				//entete
			$pdf->SetFont('Arial','BI',10);
			$pdf->Cell(23,7,utf8_decode('Référence'),1,0,'C');
			$pdf->Cell(117,7,utf8_decode('Libellé'),1,0,'C');
			$pdf->Cell(10,7,utf8_decode('Qté'),1,0,'C');
			$pdf->Cell(20,7,utf8_decode('PU HT'),1,0,'C');
			$pdf->Cell(20,7,utf8_decode('PT HT'),1,0,'C');
			$pdf->Ln();
				//données
			$pdf->SetFont('Arial','',10);
			$pdf->SetWidths(array(23,117,10,20,20));
			foreach ($this->tab_lfa as $ligne_facture)
			{
				if ($ligne_facture->lfa_type != 'Option')
					$TOTAL+= $ligne_facture->lfa_qtt * $ligne_facture->lfa_prix_ht;
				if ($ligne_facture->lfa_type_libelle == 'Titre')
				{
					$pdf->SetAligns(array('C','C','R','R','R'));
					$ref = '';
				}
				else
				{
					$pdf->SetAligns(array('L','L','R','R','R'));
					$ref = utf8_decode($ligne_facture->lfa_type_libelle);
				}	
				$STOTAL+= $ligne_facture->lfa_qtt * $ligne_facture->lfa_prix_ht;
				if ($STOTAL != 0 && $ligne_facture->lfa_type_libelle=='Titre') 
				{
					$STOTAL = number_format($STOTAL, 2, ',', ' ');
					$pdf->SetAligns(array('C','R','R','R','R'));
					$pdf->Row(array('','Sous Total','','',$STOTAL.' '.EURO));
					$pdf->SetAligns(array('C','C','R','R','R'));
					$STOTAL = 0;
				}
				$type = $ligne_facture->pdf_lfa_designation();
				$pdf->Row(array($ref, $type, $ligne_facture->pdf_lfa_qtt(), $ligne_facture->pdf_lfa_puht(), $ligne_facture->pdf_lfa_ptht()));
			}
			$STOTAL = number_format($STOTAL, 2, ',', ' ');
      $pdf->SetAligns(array('C','R','R','R','R'));
			$pdf->Row(array('','Sous Total','','',$STOTAL.' '.EURO));
			$pdf->Cell(190,0,'','T',1);

			//pour info
			if ($pdf->GetY() > 230)
				$pdf->AddPage();
			
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,7,'',0,1,'');

			$pdf->MultiCell(130,5,utf8_decode($this->fac_info."\n".file_get_contents("tpl/gerant.tpl")),0,1);

			
			//TOTAL
			$pdf->SetY(-50);
      $tauxTVA = $ligne_facture->lfa_tva/100;
			$totalTTC = number_format($TOTAL*(1+$tauxTVA),2,',',' ').' '.EURO;	
			//$echeance = $this->client->cli_echeances;
			$echeance = 'A reception de facture';
			$pdf->Cell(130,7,utf8_decode('Condition de règlement: '.$echeance),1,0,'C');
			$pdf->Cell(10,7,'',0,0,'C');
			$pdf->Cell(50,7,utf8_decode('MONTANT DE L\'OFFRE'),1,1,'C');
			#$date_reglement = $this->get_date_reglement();
			$ligne = utf8_decode('Date de règlement: '.$date_reglement.' Net à payer: '.$totalTTC);
			$pdf->Cell(130,7,$ligne,1,0,'C');
			$pdf->Cell(10,7,'',0,0,'C');
			$pdf->Cell(25,7,'Montant H.T',1,0);
			$pdf->Cell(25,7,number_format($TOTAL,2, ',', ' ').' '.EURO,1,1,'R');
			$pdf->Cell(130,7,utf8_decode('En cas de retard de paiement, il sera appliqué des pénalités de 1.5%'),'LTR',0,'C');
			$pdf->Cell(10,7,'',0,0,'C');
			$pdf->Cell(25,7,'T.V.A.: '. number_format($tauxTVA*100, 1).'%',1,0);
			$pdf->Cell(25,7,number_format($TOTAL*$tauxTVA,2, ',', ' ').' '.EURO,1,1,'R');
			$pdf->Cell(130,7,utf8_decode('par mois de retard. Pas d\'escompte sur paiement anticipé.'),'LBR',0,'C');
			$pdf->Cell(10,7,'',0,0,'C');
			$pdf->Cell(25,7,'Montant T.T.C',1,0);
			$pdf->Cell(25,7,$totalTTC,1,1,'R');
			return $pdf;
		}

    function calc_overdate()
    {
      if ($this->client->cli_echeances == 'A reception')
        $date = strtotime($this->fac_date_crea .'+ 15 day');
      if ($this->client->cli_echeances == '30 jours à reception')
        $date = strtotime($this->fac_date_crea .'+ 45 day');
      if ($this->client->cli_echeances == '30 jours fin de mois') {
        $date = strtotime($this->fac_date_crea);
        $dat = split(',',date('n,Y'), $date);
        $date = mktime(23,59,59,$dat[0]+1,0,$dat[1]); 
      }
      if ($this->client->cli_echeances == '30 jours fin de mois le 10') {
        $date = strtotime($this->fac_date_crea);
        $dat = split(',',date('n,Y'), $date);
        $date = mktime(23,59,59,$dat[0]+1,10,$dat[1]); 
      }
      if ($this->client->cli_echeances == '60 jours fin de mois'){ 
        $date = strtotime($this->fac_date_crea);
        $dat = split(',',date('n,Y'), $date);
        $date = mktime(23,59,59,$dat[0]+2,0,$dat[1]); 
      }
      if ($this->client->cli_echeances == '60 jours fin de mois le 10'){ 
        $date = strtotime($this->fac_date_crea);
        $dat = split(',',date('n,Y'), $date);
        $date = mktime(23,59,59,$dat[0]+2,10,$dat[1]); 
      }
      /*echo 
      date('d-M-Y', strtotime($this->fac_date_crea)) .
      $this->client->cli_echeances .
      date('d-M-Y',$date) .
      '<br>';*/
      return ($date);
    }

    function get_color() {
      if ($this->calc_overdate() < time() && $this->fac_statut == 'En attente de reglement') {
        return (' bgcolor="#FF0000"');
      }
      if ($this->fac_statut == 'Lettre simple' && $this->calc_suivi() < time()) {
        return (' bgcolor="#DD0000"');
      }
      if ($this->fac_statut == 'Lettre AR' && $this->calc_suivi() < time()) {
        return (' bgcolor="#AA0000"');
      }
    }

    function calc_suivi() {
      $date = strtotime($this->fac_date_suivi . '+ 15 day');
      return $date;
    }

    function export() {
      $libelle = $this->get_fac_numero() . ' ' . $this->client->get_cli_societe();
      $part = explode(' ', $this->client->get_cli_societe());
      $cptClient = '411' . strtoupper(substr($part[0], 0, 7));
      $piece = $this->fac_id;
      $exp = 'VE;' . $this->get_date_crea() . ';'. $cptClient . ';' . $piece . ';' . $libelle . ';'. $this->get_totalTTC() . ";0\n";
      foreach ($this->tab_lfa as $line) {
        if ($line->get_lfa_ptht() > 0) {
          $exp .= 'VE;' . $this->get_date_crea() . ';'. $line->get_lfa_cpt() . ';'. $piece . ';' . $libelle . ';0;'. $line->get_lfa_ptht() . "\n";
          $exp .= 'VE;' . $this->get_date_crea() . ';'. $line->get_lfa_cpt_tva() . ';' . $piece . ';' . $libelle . ';0;'. $line->get_lfa_mtt_tva() . "\n";
        }
      }
      return $exp;
    }

	}	
	
	class ligne_facture
	{
		var $lfa_id;
		var $fac_id;
		var $lfa_type;
		var $lfa_designation;
		var $lfa_no_serie;
		var $lfa_pu_achat;
		var $lfa_qtt;
		var $lfa_prix_ht;
    var $lfa_tva;
    var $lfa_tva_cpt;
    var $lfa_cpt;
    var $lfa_type_libelle;

    function ligne_facture($arg=array("LFA_ID"=>"0", "FAC_ID"=>"0", "LFA_TYPE"=>"Materiel", "LFA_DESIGNATION"=>"", "LFA_NO_SERIE"=>"", "LFA_QTT"=>"1", "LFA_PRIX_ACHAT"=>"0","LFA_PRIX_HT"=>"0")) {       
			if(is_int($arg)) {
				$sql = "SELECT * FROM T_LIGNE_FACTURE NATURAL JOIN T_TYPE WHERE LFA_ID = '$arg';";
				mysqlinforezo();
				$query = mysql_query($sql) or die (sql_error('ligne_facture', 'ligne_facture (constructeur)', 1, $sql));
				$liste =  mysql_fetch_array($query);
			}
			elseif(is_array($arg)) {
				$liste = $arg;
			}
			if (is_array($liste)) {
				$this->lfa_id = $liste['LFA_ID'];
				$this->fac_id = $liste['FAC_ID'];
				$this->lfa_type = $liste['TYP_ID'];
				$this->lfa_designation = str_replace("\'","¤",$liste['LFA_DESIGNATION']);
				$this->lfa_no_serie = $liste['LFA_NO_SERIE'];
				$this->lfa_qtt = $liste['LFA_QTT'];
				$this->lfa_pu_achat = $liste['LFA_PRIX_ACHAT'];
				$this->lfa_prix_ht = $liste['LFA_PRIX_HT'];
        $this->lfa_tva = $liste['TYP_TVA_TAUX'];
        $this->lfa_tva_cpt = $liste['TYP_TVA_COMPTE'];
        $this->lfa_cpt = $liste['TYP_COMPTE'];
        $this->lfa_type_libelle = $liste['TYP_TYPE'];
			}
    } 

    function get_lfa_cpt() {
      return $this->lfa_cpt;
    }

    function get_lfa_type_libelle() {
      return $this->lfa_type_libelle;
    }

    function get_lfa_prix_ht() {
      return $this->lfa_prix_ht;
    }
    
    function get_lfa_qtt() {
      return $this->lfa_qtt;
    }

    function edit($no_ligne) {
			$liste_type = funcSelectList("T_TYPE", "TYP_TYPE", "TYP_ID");
			$SELECT_TYPE = '<td>'.funcMakeFormList( 'lfa_type'.$no_ligne, $liste_type, $this->lfa_type, 'lfa_type'.$no_ligne).'</td>';
			$EDIT ='<tr>
			'.$SELECT_TYPE.'
			<td><input type="text" size="40" name="lfa_designation'.$no_ligne.'" value="'.$this->get_lfa_designation().'"> </td>
			<td><input type="text" size="10" maxlength="15" name="lfa_no_serie'.$no_ligne.'" value="'.$this->get_lfa_no_serie().'"> </td>
			<td><input type="text" size="2" maxlength="3" name="lfa_qtt'.$no_ligne.'" value="'.$this->lfa_qtt.'"></td>
			<td><input type="text" size="7" maxlength="10" name="lfa_pu_achat'.$no_ligne.'" value="'.$this->get_lfa_pu_achat().'"></td>
			<td><input type="text" size="7" maxlength="10" name="lfa_prix_ht'.$no_ligne.'" value="'.$this->lfa_prix_ht.'"></td>
			<td><input type="text" size="7" maxlength="10" name="lfa_total'.$no_ligne.'" value="'.$this->lfa_prix_ht*$this->lfa_qtt.'" disabled"></td>
			<td><input type="text" size="5" maxlength="5" name="lfa_mtt_tva'.$no_ligne.'" value="'.$this->get_lfa_ptttc().'" disabled"></td>
			<td><input type="submit" name="suppr'.$no_ligne.'" value ="suppr"></td>
			</tr>';
			return $EDIT;
		}


    function get_lfa_cpt_tva() {
      return $this->lfa_tva_cpt;
    }
		
		function get_lfa_designation()
		{
			$result = str_replace("¤","'",$this->lfa_designation);
			return ($result);
		}
		
		function pdf_lfa_designation()
		{
			$result = $this->get_lfa_designation();
      if ($this->lfa_type == 'Materiel') {
        $result .= '; ' . 'N° série: ' . $this->lfa_no_serie;
      }
			return utf8_decode($result);
		}
		
		function pdf_lfa_puht()
		{
			if ($this->lfa_type=='Titre' or $this->lfa_prix_ht==0)
				$result = '';
			else
				$result = number_format($this->lfa_prix_ht, 2, ',', ' ').' '.EURO;
			return $result;
		}

		function pdf_lfa_ptht()
		{
			if ($this->lfa_type=='Titre' or $this->lfa_prix_ht==0)
				$result = '';
			else
				$result = number_format($this->lfa_prix_ht * $this->lfa_qtt, 2, ',', ' ').' '.EURO;
			return $result;
		}

		function pdf_lfa_qtt()
		{
			if ($this->lfa_type=='Titre' or $this->lfa_qtt==0 )
				$result = '';
			else
				$result = $this->lfa_qtt;
			return $result;
		}

    function get_lfa_ptht() {
      return ($this->lfa_qtt * $this->lfa_prix_ht);
    }

    function get_lfa_tva() {
      return $this->lfa_tva;
    }

    function get_lfa_mtt_tva() {
      $tva = ($this->get_lfa_ptht() * $this->get_lfa_tva() / 100);
      return (number_format($tva, 2));
    }

    function get_lfa_ptttc() {
      return ($this->get_lfa_ptht() + $this->get_lfa_mtt_tva());
    }
		
		function preview()
		{
			$style = 'style="text-align: left;"';
			$PUHT = number_format($this->lfa_prix_ht, 2, ',' , ' ').' €';
			$total = number_format($this->lfa_qtt * $this->lfa_prix_ht, 2, ',', ' ').' €';
			$qtt = $this->lfa_qtt;
			if ($this->lfa_type == 'Titre')
			{
				$style = 'style="text-align: center; padding: 15px 0;"';
				$PUHT = '&nbsp;';
				$total = '&nbsp;';
				$qtt = '&nbsp;';
			}
			$PREVIEW='
			<tr>
				<td '.$style.'>'.$this->get_lfa_designation().'</td>
				<td>'.$qtt.'</td>
				<td style="text-align: right;">'.$PUHT.'</td>
				<td style="text-align: right;">'.$total.'</td>
			</tr>
			';
			return $PREVIEW;
		}

		function enreg()
		{
			$sql = "INSERT INTO T_LIGNE_FACTURE (LFA_ID, FAC_ID, TYP_ID, LFA_DESIGNATION, LFA_NO_SERIE, LFA_QTT, LFA_PRIX_ACHAT, LFA_PRIX_HT) VALUES ('', '$this->fac_id', '$this->lfa_type', '$this->lfa_designation', '$this->lfa_no_serie', '$this->lfa_qtt', '$this->lfa_pu_achat', '$this->lfa_prix_ht');";
			mysqlinforezo();
			mysql_query($sql) or die(sql_error('ligne_facture', 'enreg', 1, $sql));
			return 'ligne enregistrée';
		}

		function get_lfa_no_serie()
		{
			return $this->lfa_no_serie;
		}

		function get_lfa_pu_achat()
		{
			return $this->lfa_pu_achat;
		}

    function get_lfa_prix_achat() {
      return ($this->lfa_pu_achat * $this->lfa_qtt);
    }

		function get_lfa_marge()
		{
			$marge = ($this->lfa_prix_ht - $this->lfa_pu_achat) * $this->lfa_qtt;
			return $marge;
		}
	}

	class liste_factures
	{
		var $tab_factures;

		function liste_factures($criteres='')
		{
			if(!is_array($criteres))
			{
				$sql = "select * from T_FACTURE ORDER BY FAC_ID DESC;";
			}
			else
			{
				if (empty($criteres['Statut']))
				{
					echo "il faut cocher au moins 1 case statut (a faire ou terminé)";
					return 0;
				}
        $liste_status= "";
				foreach ($criteres['Statut'] as $stat)
				{
					$liste_status .= "'".$stat."', ";
				}
				$liste_status=substr($liste_status, 0, -2);
        if (isset($criteres['date_debut'])) {
          $date_debut = date_input_to_mysql($criteres['date_debut']);
        } else {
          $date_debut = '';
        }
        if (isset($criteres['date_fin'])) {
          $date_fin = date_input_to_mysql($criteres['date_fin']);
        } else {
          $date_fin = '';
        }
				
				if ($criteres['TEMPS']=='jour')
          $where = 'WHERE (DATE(FAC_DATE_CREA)=CURDATE() OR (FAC_STATUT=\'A traiter\' AND DATE(FAC_DATE_CREA) < CURDATE())) AND';
				elseif ($criteres['TEMPS'] == 'semaine')
          $where = 'WHERE YEARWEEK(FAC_DATE_CREA) = YEARWEEK(CURDATE()) AND';
				elseif ($criteres['TEMPS'] == 'mois')
          $where = 'WHERE MONTH(FAC_DATE_CREA) = MONTH(CURDATE()) AND YEAR(FAC_DATE_CREA) = YEAR(CURDATE()) AND';
				elseif ($criteres['TEMPS'] == 'tout') 
          $where = 'WHERE';
				elseif ($criteres['TEMPS'] == 'custom')
          $where = "WHERE DATE(FAC_DATE_CREA) BETWEEN '$date_debut' AND '$date_fin' AND";
	
				$where .=" FAC_STATUT IN ($liste_status)";
				$cli_id = $criteres['cli_id'];
				if($cli_id != 'tous') $where .=" AND CLI_ID = $cli_id";
				$sql = "SELECT * FROM T_FACTURE $where ORDER BY FAC_ID DESC;";
			}
			$query = mysql_query($sql) or die(sql_error('liste_facture','liste_facture (contructeur)', 1, $sql));
			while ($liste = mysql_fetch_array($query))
			{
				$this->tab_factures[] = new facture ($liste);
			}
			
		}

		function test_check($temps, $statut=array("En cours","À facturer"), $marge="")
		{
      $check_jour = $check_semaine = $check_mois = $check_tout = $check_custom = $check_en_cours = $check_a_facturer = $check_signe = $check_perdu = $check_marge = '';
			if ($temps=='jour') $check_jour = 'checked';
			elseif($temps=='semaine') $check_semaine = 'checked';
			elseif($temps=='mois') $check_mois = 'checked';
			elseif($temps=='tout') $check_tout = 'checked';
			elseif($temps=='custom') $check_custom = 'checked';
			if (!empty($statut))
			{
				if (in_array('En cours', $statut)) $check_en_cours = 'checked';
				if (in_array('À facturer', $statut)) $check_a_facturer = 'checked';
				if (in_array('Signé', $statut)) $check_signe = 'checked';
				if (in_array('Perdu', $statut)) $check_perdu = 'checked';
			}
			if ($marge=='marge') $check_marge = 'checked';
			return array("jour"=>$check_jour, "semaine"=>$check_semaine, "mois"=>$check_mois, "tout"=>$check_tout, "custom"=>$check_custom, "en_cours"=>$check_en_cours, "a_facturer"=>$check_a_facturer, "signe"=>$check_signe, "perdu"=>$check_perdu, "marge"=>$check_marge);
		}
					
		function display_menu($criteres=array())
		{
			$temps = $criteres['TEMPS'];
			$statut = $criteres['Statut'];
			$cli_id = $criteres['cli_id'];
      if (isset($critere['date_debut'])) {
        $date_debut = $criteres['date_debut'];
      } else {
        $date_debut = '';
      }
      if (isset($critere['date_fin'])) {
        $date_fin = $criteres['date_fin'];
      } else {
        $date_fin = '';
      }
      if (isset($critere['marge'])) {
        $marge = $criteres['marge'];
      } else {
        $marge = '';
      }
			$liste = funcEnumList('T_FACTURE','fac_statut');
			$check = $this->test_check($temps, $statut, $marge);
			$DISPLAY = '<div class = "bandeau"><h1>Interventions</h1><h2>'./*$this->caption.*/'</h2></div>
			<span class="clear">&nbsp;</span>
		<div class="nouveau">
			<form method="post" action="./index2.php?contenu=view_factures&vue=formulaire">
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
				'.funcMakeFormCheckbox('Statut[]',$liste,$statut).'		
			</div>
			<div>
				'.MakeSelectCustomers($cli_id).'
			</div>
			<div>
				<label for="marge">Marges</label><input type="checkbox" name="marge" value="marge" id="marge" '.$check['marge'].'>
			</div>
			<div>
				<input type="submit" value="Chercher">
      </div>
        <h3>Recherche Rapide</h3>
        <div><input type="submit" value="voir tout" name="all"></div>
        <div><input type="submit" value="voir en attente" name="waiting"></div>
		</form>
			</div>';
		return $DISPLAY;
		}
		
		function show($show_marge=false)
		{
      if (isAllowed('facSsMenu')) {
        $SSMENU = 
        '<input type="submit" name="print_facture" value="Imprimer">
        <input type="submit" name="mark_wait" value="Marquer en attente de reglement">
        <input type="submit" name="mark_paid" value="Marquer comme réglée">
        <input type="submit" name="mark_unpaid" value="Marquer comme impayée">
        <input type="submit" name="send_letter" value="Imprimer une relance">
        <input type="submit" name="send_AR" value="Imprimer un AR">
        <input type="submit" name="export" value="exporter vers EBP">';
      }
			if (empty($this->tab_factures)) {
				$DISPLAY = "Aucune facture ne correspond au critères définits";
				return $DISPLAY;
			}
				
			if ($show_marge) {
				$marge='<th> Marge HT </th>';
      } else {
        $marge = '';
      }
      $DISPLAY = '
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
      ';

			$DISPLAY .= "
			<h1> Liste des factures </h1>
			<div class=\"nouveau\">&nbsp;</div>
			<table border>
				<tr>
					<th> N° </th>
					<th> Titre de la facture </th>
					<th> Client </th>
					<th> Total HT </th>
					$marge
					<th> Date creation </th>
					<th> Date reglement </th>
					<th> Statut </th>
          <th> Selection </th>
				</tr>
			";
      $DISPLAY .= '<form method="post" action="./index2.php?contenu=print_selected_factures">';
      $CORPS = '';
      $TotalHT = 0;
      $Total_marge = 0;
			foreach ($this->tab_factures as $facture)
			{
				if ($show_marge){
					$marge = '<td>'.$facture->get_total_marge().'</td>';
				}
				$CORPS.='
				<tr>
					<td> '.$facture->fac_id.' </td>
					<td><a href="index.php?contenu=facture&fac_id='.$facture->fac_id.'">'.$facture->get_fac_titre().'</td>
					<td><a href="index.php?contenu=showcustomer&cli_id='.$facture->client->cli_id.'"> '.$facture->client->cli_societe.'<span>'.$facture->client->showSum().'</span></a> </td>
					<td style="text-align: right;"> '.number_format($facture->get_totalHT(),2, ',', ' ').' </td>
					'.$marge.'
					<td> '.$facture->get_date_crea().' </td>
					<td'. $facture->get_color() .'> '.date('d/m/Y',$facture->calc_overdate()).' </td>
					<td> '.$facture->fac_statut.' </td>
          <td> <input type="checkbox" name="selectionFacture[]" value="' . $facture->fac_id . '"> </td>
				</tr>
				';
				$TotalHT += $facture->get_totalHT();
				$Total_marge += $facture->get_total_marge();
			}
			if ($show_marge){
				$marge = '<th>'.$Total_marge.'</th>';
			}
			$TOTAL = '<tr> 
					<th> &nbsp; </th>
					<th> &nbsp; </th>
					<th> Total </th>
					<th>'.number_format($TotalHT,2,',',' ').'</th>
					'.$marge.'
					<th> &nbsp; </th>
					<th> &nbsp; </th>
					<th> &nbsp; </th>
					<td><input type="checkbox" value="0" id="tous" onClick="this.value=check(\'selectionFacture[]\')"> </td>
				</tr>';
			$DISPLAY .= $TOTAL . $CORPS . $TOTAL . $SSMENU .
        '</form>
        </table>';
      if (isAllowed('makeFacMaintenance')) {
        $DISPLAY .= '<form method="post" action="index.php?contenu=make_factures_maintenance">
            <input type="submit" value="Générer les factures de maintenance">
            </form>';
      }
      if (isAllowed('viewAnalyse')) {
        $DISPLAY .= $this->displayAnalyse();
      }
			return $DISPLAY;
		}

    function displayAnalyse() {
      $analyse = $this->mkanalyse();
      $tab = new table($analyse);
      $tab->addTitles(array('Catégories','Chiffre d\'affaire', 'Marge Brute', 'Achats'));
      $tab->setClass("analyse");
      return $tab->display();
    }

    function mkAnalyse() {
      foreach ($this->tab_factures as $facture) {
        $facture->addTotalHT(&$array);
      }
      $totCA = $totMB = $totA = 0;
      foreach ($array as  $value) {
        $totCA += $value["Chiffre d'affaire"];
        $totMB += $value['Marge brute'];
        $totA += $value['Achats'];
      }
        $array['Total']["Chiffre d'affaire"] = $totCA;
        $array['Total']["Marge brute"] = $totMB;
        $array['Total']['Achats'] = $totA;
        if (isset($array['Maintenance'])) {
          $array['Total SS M']["Chiffre d'affaire"] = $array['Total']["Chiffre d'affaire"] - $array['Maintenance']["Chiffre d'affaire"];
          $array['Total SS M']["Marge brute"] = $array['Total']['Marge brute'] - $array['Maintenance']['Marge brute'];
          $array['Total SS M']["Achats"] = $array['Total']['Achats'] - $array['Maintenance']['Achats'];
        }
      return ($array);
    }
	}

function delete_fac_after($no_facture)
{
  $sql="delete from T_LIGNE_FACTURE where FAC_ID > $no_facture";
  mysqlinforezo();
  mysql_query($sql) or die(sql_error('misc', 'delete_fac_after', 1, $sql));
  $sql="delete from T_FACTURE where FAC_ID > $no_facture";
  mysql_query($sql) or die(sql_error('misc', 'delete_fac_after', 2, $sql));
  $no_facture++;
  $sql="ALTER TABLE T_FACTURE AUTO_INCREMENT = $no_facture;";
  mysql_query($sql) or die(sql_error('misc', 'delete_fac_after', 3, $sql));
  return ("factures supprimée, la prochaine facture aura le n° $no_facture");
}

?>

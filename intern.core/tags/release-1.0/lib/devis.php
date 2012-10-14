<?php
include_once "lib/pdf.php";
include_once "lib/inforezo.php";
include_once "lib/type.php";
	class devis
    {
      var $dev_id;
      var $client;
      var $contact; 
      var $dev_titre;
      var $dev_acompte; 
      var $dev_info;
      var $dev_date_crea;
      var $dev_date_modif;
      var $dev_statut;
      var $tab_lde;
      var $switch;

                function devis($arg=array("DEV_ID"=>"0", "CLI_ID"=>"0", "CON_ID"=>"0", "DEV_TITRE"=>"Nouveau Devis", "DEV_ACOMPTE"=>"30", "DEV_INFO"=>"Pour information", "DEV_STATUT"=>"En cours"))
                {       
			if(is_numeric($arg))
			{
				$sql = "SELECT DEV_ID, CLI_ID, CON_ID, DEV_TITRE, DEV_ACOMPTE, DEV_INFO, UNIX_TIMESTAMP(DEV_DATE_CREA) AS DEV_DATE_CREA, UNIX_TIMESTAMP(DEV_DATE_MODIF) AS DEV_DATE_MODIF, DEV_STATUT FROM T_DEVIS WHERE DEV_ID = '$arg';";
				mysqlinforezo();
				$query = mysql_query($sql) or die (sql_error('devis', 'init_devis', 1, $sql));
				$liste = mysql_fetch_array($query);
			}
			elseif(is_array($arg))
			{
				$liste = $arg;
			}
			if (is_array($liste))
			{
				$this->dev_id = $liste['DEV_ID'];
				$this->client = new client($liste['CLI_ID']);
				$this->client->init_all_contact();
				$this->contact = $liste['CON_ID'];
				$this->dev_titre = $liste['DEV_TITRE'];
				$this->dev_acompte = $liste['DEV_ACOMPTE'];
				$this->dev_info = $liste['DEV_INFO'];
				$this->dev_date_crea = $liste['DEV_DATE_CREA'];
				$this->dev_date_modif = $liste['DEV_DATE_MODIF'];
				$this->dev_statut = $liste['DEV_STATUT'];
				$this->init_tab_lde();
				$this->switch = 0;
			}
			
                } 
		
		function set_form_values($liste)
		{
			if (is_array($liste))
			{
				$this->contact = $liste['dev_contact'];
				$this->dev_titre = str_replace("\'", "¤",$liste['dev_titre']);
				$this->dev_acompte = str_replace("\'", "¤",$liste['dev_acompte']);
				$this->dev_info = str_replace("\'", "¤",$liste['dev_info']);
				$this->dev_statut = $liste['dev_statut'];
				foreach ($this->tab_lde as $no_ligne => $ligne_devis)
				{
					$this->tab_lde[$no_ligne]->lde_type = $liste['lde_type'.$no_ligne];
					$this->tab_lde[$no_ligne]->lde_designation = str_replace("\'", "¤",$liste['lde_designation'.$no_ligne]);
					$this->tab_lde[$no_ligne]->lde_qtt = str_replace("\'", "¤",$liste['lde_qtt'.$no_ligne]);
					if (isset($liste['lde_prix_achat'.$no_ligne]))$this->tab_lde[$no_ligne]->lde_prix_achat = $liste['lde_prix_achat'.$no_ligne];
					$this->tab_lde[$no_ligne]->lde_prix_vente = $liste['lde_prix_vente'.$no_ligne];
				}
				$result = 0;
			}
			else
			{
				$result = "Le parametre n'est pas une liste";
			}
			return $result;
		}

		function init_tab_lde() { 
      $sql = "SELECT * FROM T_LIGNE_DEVIS NATURAL JOIN T_TYPE WHERE DEV_ID = '$this->dev_id' ORDER BY LDE_ID;";
      mysqlinforezo();
      $query = mysql_query($sql) or die (sql_error('devis', 'init_tab_lde', 1 , $sql)); 
      mysql_close();
      while ($liste=mysql_fetch_array($query))
      {       
              $this->tab_lde[]= new ligne_devis($liste);
      }
		}

		function get_dev_id()
		{
			return $this->dev_id;
		}

		function get_dev_titre()
		{
			$result = str_replace("¤","'",$this->dev_titre);
			return $result;
		}

		function get_dev_info()
		{
			$result = str_replace("¤","'",$this->dev_info);
			return $result;
		}
		
		function get_date_crea()
		{
			 setlocale (LC_ALL, "fr_FR.utf8");
			if(empty($this->dev_date_crea))
				$result='&nbsp;';
			else {
        $d = explode('-', $this->dev_date_crea);
        if (isset($d[1]))
          $result = $d[2] . '/' . $d[1] . '/' . $d[0];
        else {
          $result = strftime("%d/%m/%Y", $this->dev_date_crea);
        }
      }
			return $result;
		}
						 
		function get_date_modif()
		{
			setlocale (LC_ALL, "fr_FR.utf8");
			if(empty($this->dev_date_modif))
				$result='&nbsp;';
			else
				$result=strftime('%e/%m/%G' , $this->dev_date_modif);
			return $result;
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
			foreach ($this->tab_lde as $ligne)
			{
				$total += $ligne->lde_prix_vente*$ligne->lde_qtt;
			}
			return $total;
		}

		function get_margeHT()
		{
			$total_vente = 0;
			$total_achat= 0;
			foreach ($this->tab_lde as $ligne)
			{
				$total_vente += $ligne->lde_prix_vente*$ligne->lde_qtt;
				$total_achat += $ligne->lde_prix_achat*$ligne->lde_qtt;
			}
			$marge = $total_vente - $total_achat;
			return $marge;
		}

    function getJavascript() {
      $types = new list_type();
      $JAVA = '<script language="JavaScript">';

      $JAVA .= $types->javaChoixType();
      $JAVA .= $types->javaChoixPrixAchat();
      $JAVA .= '</script>';
      
		
      return ($JAVA);

    }

    function edit() {
      if (!isAllowed('devis')) {
        return ($this->preview() . $this->write_pdf());
      }
		  $JAVA = $this->getJavascript();		
			if ($this->dev_id==0)
			{
				$TITRE='<h2>Nouveau Devis pour '.$this->client->cli_societe.'</h2>';
			}
			else
			{	
				$TITRE='<h2>Editer le Devis de '.$this->client->cli_societe.'</h2>';
			}
			if ($this->switch)
			{
				$SWITCH = '<input type="submit" name="switch" value ="Cacher">';
				$prix_achat = '<th> Prix d\'achat</th>';
				$marge = '<th>Marge</th>';
				$margePourCent = '<th>Marge (%)</th>';
			}
			else
			{
				$SWITCH = '<input type="submit" name="switch" value ="Montrer">';
				$prix_achat = '';
				$marge = '';
				$margePourCent = '';
			}
			$SELECT_CONTACT=$this->client->MakeSelectContact($this->contact);
			$liste_acompte = funcEnumList("T_DEVIS", "DEV_ACOMPTE");
			$SELECT_ACOMPTE = funcMakeFormList( 'dev_acompte', $liste_acompte, $this->dev_acompte, 'acompte');
			$liste_statut = funcEnumList("T_DEVIS", "DEV_STATUT");
			$SELECT_STATUT = funcMakeFormList( 'dev_statut', $liste_statut, $this->dev_statut, 'statut');
			$objet_devis = serialize($this);
			$ENTETE=$JAVA.
			'
				<input type="hidden" name="objet_devis" value = \''.$objet_devis.'\'>
				<div><label for="dev_titre">Titre : </label> <input type="text" name="dev_titre" size="50" value = "'.$this->get_dev_titre().'"maxlength=50 id="dev_titre"></div>
				
				<div><label for="responsable">Responsable : </label> '.$SELECT_CONTACT.' </div> 
				<div><label for="acompte">Acompte : </label> '.$SELECT_ACOMPTE.' % </div> 
				<div><label for="statut">Statut : </label> '.$SELECT_STATUT.' </div> 
				<div><label for="info">Pour info : </label> <input type="text" name="dev_info" size="50" value = "'.$this->dev_info.'" maxlength=200 id="info"></div>
				';
			$CORPS='<h2> les lignes du devis</h2>';
			$CORPS.='
<table class="devis">
	<tr>
		<th> Type</th>
		<th> Désignation</th>
		<th> Qtt</th>
		'.$prix_achat.'
		<th> Prix de vente</th>
		<th> Total</th>
		'.$marge
		.$margePourCent.'
	</tr>';
			$total_achat = 0;
			$total_vente = 0;
			foreach ($this->tab_lde as $no_ligne => $ligne_devis)
			{
				$CORPS .= $ligne_devis->edit($this->switch, $no_ligne);
				$total_achat += $ligne_devis->lde_prix_achat * $ligne_devis->lde_qtt;
				$total_vente += $ligne_devis->lde_prix_vente * $ligne_devis->lde_qtt;
			}
      $total_marge = $total_vente - $total_achat;
      if ($total_vente == 0)
        $total_marge_percent = '0';
      else
        $total_marge_percent = round($total_marge / $total_vente * 100);
      $CORPS.='
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>Total:</td>';
          if ($this->switch)
            $CORPS .= ' <td>' . $total_achat . '</td>';
          $CORPS .= '
          <td>' . $total_vente . '</td>';
          if ($this->switch)
            $CORPS .= '
          <td>&nbsp;</td>
          <td>' . $total_marge . '</td>
          <td>' . $total_marge_percent . ' %</td>';
          $CORPS .= '
        </tr>
      </table>';
			$PIED = '
				<input type="submit" name="new_line" value="Nouvelle ligne">
				<input type="submit" name="save" value="Enregister">
				<input type="submit" name="preview" value="Aperçu">
				<input type="submit" name="back" value="Retour">
				';
			
			$EDIT ='
			 	<span class="clear">&nbsp;</span>
				<div class="formadmin">
					<form method="post" action="./index2.php?contenu=devis" name="edit_devis">'
						.$SWITCH
						.$TITRE
						.$ENTETE.'<span class="clear">&nbsp;</span>'
			 			.$CORPS.'<span class="clear">&nbsp;</span>'
						.$PIED
					.'</form>	
				</div>';
			return $EDIT;
                }
		
		function preview()
		{
		
			$ENTETE='<img class="logo_devis" src="images/logo.gif" alt="logo inforezo">
				<div class="coor_client">
					<p>'.$this->client->cli_societe.'</p>
					<p>'.$this->client->cli_adresse.'</p>
					<p>'.$this->client->cli_code_postal.' '.$this->client->cli_ville.'</p>
				</div>
				<div class="DEVIS">DEVIS</div>			
				<span class="clear">&nbsp;</span>
				<table border cellspacing=0 class="presentation-devis">
					<tr>
						<th>Devis N°</th>
						<th>Date Création</th>
						<th>Date Modification</th>
						<th>A l\'attention de</th>
						<th>Interlocuteur</th>
						<th>Page n°</th>
					</tr>
					<tr>
						<td>'.$this->dev_id.'</td>
						<td>'.$this->get_date_crea().'</td>
						<td>'.$this->get_date_modif().'</td>
						<td>'.$this->get_contact().'</td>
						<td>'.$this->get_interlocuteur().'</td>
						<td>1</td>
					</tr>
				</table>
				<h1>'.$this->get_dev_titre().'</h1>';
				$CORPS='
				<table border cellspacing=0 rules="cols" class="presentation-devis">
					<thead>
					<tr class="presentation-devis">
						<th>Libellé</th>
						<th>Qté</th>
						<th>PU HT</th>
						<th>PT HT</th>
					</tr>
					</thead>
					<tbody>';
				foreach ($this->tab_lde as $ligne_devis)
				{
					if ($ligne_devis->lde_type != 'Option')
						$TOTAL+= $ligne_devis->lde_qtt * $ligne_devis->lde_prix_vente;
					$STOTAL+= $ligne_devis->lde_qtt * $ligne_devis->lde_prix_vente;
					if ($STOTAL != 0 && $ligne_devis->lde_type=='Titre') 
					{
						$STOTAL = number_format($STOTAL, 2, ',', ' ');
						$CORPS.='<tr style="text-align: right;"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><strong>'.$STOTAL.' €</strong></td></tr>';
						$STOTAL = 0;
					}
					$CORPS.= $ligne_devis->preview();
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
					<p>'.$this->dev_info.'</p>
				</div>
				';
				$FOOTER='
				<div class="footer">
					10 rue Victor Grignard 42 000 Saint-Etienne Tel: 0477 924 891 Fax: 0477 912 309 contact@inforezo.com www.inforezo.com SARL de 8000 € Siren-Siret: 442 304 374 00014 /APE: 5829c
				</div>';
				$objet_devis = serialize($this);
				$ACTION='
				<form method="post" action="./index2.php?contenu=devis">
					<input type="hidden" name="objet_devis" value = \''.$objet_devis.'\'>
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

    function copyFor($client)
    {
      unset($this->dev_id);
      $this->client = $client;
      $this->enreg();
    }

    function copyForm()
    {
      $objet_devis = serialize($this);
      $COPY = '
        <div class="formadmin">
          <h2> Copier le devis </h2>
          <form method="post" action="./index.php?contenu=devis" name="copy_devis">
            <input type="hidden" name="objet_devis" value = \''.$objet_devis.'\'>
            ' . MakeSelectCustomers($this->client->cli_id) . '
            <input type="submit" name="copy" value="Copier ce devis pour ce client" />
          </form>
        </div>';
      return $COPY;
    }

    function assignForm()
    {
      $objet_devis = serialize($this);
      $ASSIGN = '
        <div class="formadmin">
          <h2> Assigner une intervention sur ce devis </h2>
          <form method="post" action ="./index.php?contenu=add_inter" name="assign_devis">
            <input type="hidden" name="objet_devis" value= \''. $objet_devis . '\'>
            <input type="submit" name="assign" value ="Assigner une intervention" />
          </form>
        </div>';
      return ($ASSIGN);
    }

		function enreg()
		{
			$date = date('Y-m-d');
			$this->dev_date_modif = time();
			if (empty($this->dev_titre))
			{
				return 'Le Devis n\'est pas enregistré, il faut lui donner un titre';
			}
			if (!isset($this->dev_id))
			{
				$this->dev_date_crea = time();
				$cli_id = $this->client->cli_id;
				$sql = "INSERT INTO T_DEVIS (DEV_ID, CLI_ID, CON_ID, DEV_TITRE, DEV_ACOMPTE, DEV_INFO, DEV_DATE_CREA, DEV_DATE_MODIF, DEV_STATUT) VALUES ('', '$cli_id', '$this->contact', '$this->dev_titre', '$this->dev_acompte', '$this->dev_info', '$date', '$date', '$this->dev_statut');";
				mysqlinforezo();
				mysql_query($sql) or die(sql_error('devis', 'enreg', 1, $sql));
				$this->dev_id=mysql_insert_id();
				mysql_close;
				foreach ($this->tab_lde as $no_ligne => $ligne_devis)
				{
					$ligne_devis->dev_id = $this->dev_id;
					$ligne_devis->enreg();
				}
				return 'Devis Enregistré (nouveau)';
			}
			else
			{
				$sql = "delete from T_LIGNE_DEVIS WHERE DEV_ID=$this->dev_id";
				mysqlinforezo();
				mysql_query($sql) or die (sql_error('devis', 'enreg', 2, $sql));
				$sql = "UPDATE T_DEVIS SET CON_ID='$this->contact', DEV_TITRE='$this->dev_titre', DEV_ACOMPTE='$this->dev_acompte', DEV_INFO='$this->dev_info', DEV_DATE_MODIF='$date', DEV_STATUT='$this->dev_statut' WHERE DEV_ID= '$this->dev_id';";
				mysql_query($sql) or die (sql_error('devis', 'enreg', 3, $sql));
				foreach ($this->tab_lde as $no_ligne => $ligne_devis)
				{
					$ligne_devis->dev_id = $this->dev_id;
					$ligne_devis->enreg();
				}
				return 'Devis enregistré (modification)';
			}
		}

		function write_pdf()
		{
			$file = 'tmp/devis-'.$this->dev_id.'.pdf';
			
			$date_crea = $this->get_date_crea();
			$date_modif = $this->get_date_modif();
			$responsable = utf8_decode($this->get_contact());
			$titre = $this->get_dev_titre();

			$pdf=new PDF_MC_Table();
			$pdf->AddPage();
			//logo
			$pdf->Image('images/logo.jpg',10,8,45);
			//DEVIS
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',40);
			$pdf->Cell(100,10);
			$pdf->Cell(60,10,'DEVIS',0,1,'C');
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
			$pdf->Cell(20,7,utf8_decode('Devis n°'),1,0,'C');
			$pdf->Cell(30,7,utf8_decode('Date création'),1,0,'C');
			$pdf->Cell(40,7,utf8_decode('Date modification'),1,0,'C');
			$pdf->Cell(40,7,utf8_decode('A l\'attention de'),1,0,'C');
			$pdf->Cell(40,7,utf8_decode('Interlocuteur'),1,0,'C');
			$pdf->Cell(20,7,utf8_decode('Page n°'),1,0,'C');
			$pdf->Ln();
				//Données
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,6,$this->dev_id,1);
			$pdf->Cell(30,6,$date_crea,1);
			$pdf->Cell(40,6,$date_modif,1);
			$pdf->Cell(40,6,$responsable,1);
			$pdf->Cell(40,6,$_SESSION['prenom'].' '.$_SESSION['nom'],1);
			$pdf->Cell(20,6,$pdf->PageNo(),1);
			$pdf->Ln();
			//titre devis
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(0,10,utf8_decode($titre),0,1,'C');
			
			//Deuxieme tableau
				//entete
			$pdf->SetFont('Arial','BI',10);
			$pdf->Cell(27,7,utf8_decode('Référence'),1,0,'C');
			$pdf->Cell(113,7,utf8_decode('Libellé'),1,0,'C');
			$pdf->Cell(10,7,utf8_decode('Qté'),1,0,'C');
			$pdf->Cell(20,7,utf8_decode('PU HT'),1,0,'C');
			$pdf->Cell(20,7,utf8_decode('PT HT'),1,0,'C');
			$pdf->Ln();
				//données
			$pdf->SetFont('Arial','',10);
			$pdf->SetWidths(array(27,113,10,20,20));
			foreach ($this->tab_lde as $ligne_devis)
			{
				if ($ligne_devis->lde_libelle != 'Option')
					$TOTAL+= $ligne_devis->lde_qtt * $ligne_devis->lde_prix_vente;
				if ($ligne_devis->lde_libelle == 'Titre')
				{
					$pdf->SetAligns(array('C','C','R','R','R'));
					$ref = '';
				}
				else
				{
					$pdf->SetAligns(array('L','L','R','R','R'));
					$ref = $ligne_devis->lde_libelle;
				}	
				$STOTAL+= $ligne_devis->lde_qtt * $ligne_devis->lde_prix_vente;
				if ($STOTAL != 0 && $ligne_devis->lde_libelle=='Titre') 
				{
					$pdf->SetAligns(array('L','R','R','R','R'));
					$STOTAL = number_format($STOTAL, 2, ',', ' ');
					$pdf->Row(array('','Sous Total','','',$STOTAL.' '.EURO));
					$STOTAL = 0;
					$pdf->SetAligns(array('C','C','R','R','R'));
				}
				$pdf->Row(array(utf8_decode($ref), $ligne_devis->pdf_lde_designation(), $ligne_devis->pdf_lde_qtt(), $ligne_devis->pdf_lde_puht(), $ligne_devis->pdf_lde_ptht()));
			}
			$STOTAL = number_format($STOTAL, 2, ',', ' ');
      $pdf->SetAligns(array('L','R','R','R','R'));
			$pdf->Row(array('','Sous Total','','',$STOTAL.' '.EURO));
			$pdf->Cell(190,0,'','T',1);

			//pour info
			if ($pdf->GetY() > 230)
				$pdf->AddPage();
			
			$pdf->SetY(-50);
			$pdf->SetFont('Arial','B',10);
			$mtt_acompte_ttc = $TOTAL *1.196 * $this->dev_acompte / 100;
			$mtt_acompte_ttc = number_format($mtt_acompte_ttc, 2, ',', ' ');
      if ($this->dev_acompte > 0)
        $acompte = utf8_decode('La commande sera prise en compte dès la réception de l\'acompte de '.$this->dev_acompte.' %, soit le montant de '.$mtt_acompte_ttc.' TTC, une facture vous sera envoyée à votre demande ou à réception du montant.'."\n");
      $pdf->MultiCell(130,5,  $acompte .utf8_decode($this->dev_info)."\n".'Laurent',0,1);
			
			//TOTAL
			$pdf->SetY(-50);
			$pdf->Cell(140,7,'',0,0);
			$pdf->Cell(50,7,utf8_decode('MONTANT DE L\'OFFRE'),1,1,'C');
			$pdf->Cell(140,7,'',0,0);
			$pdf->Cell(25,7,'Montant H.T',1,0);
			$pdf->Cell(25,7,number_format($TOTAL,2, ',', ' ').' '.EURO,1,1,'R');
			$pdf->Cell(140,7,'',0,0);
			$pdf->Cell(25,7,'T.V.A.: 19.6%',1,0);
			$pdf->Cell(25,7,number_format($TOTAL*0.196,2, ',', ' ').' '.EURO,1,1,'R');
			$pdf->Cell(140,7,'',0,0);
			$pdf->Cell(25,7,'Montant T.T.C',1,0);
			$pdf->Cell(25,7,number_format($TOTAL*1.196,2, ',', ' ').' '.EURO,1,1,'R');

			
			
			$pdf->Output($file);
			$result = '<form method = "get" action = "'.$file.'">
					<input type="submit" name="print" value = "Imprimer">
				</form>';
			return $result;
		}

    function getColor() {
      if ($this->dev_statut != 'En cours')
        return '';
      $arr = explode('-', $this->dev_date_modif);
      $t = mktime(0,0,0,$arr[1],$arr[2], $arr[0]);
      $age = time() -  $t;
      $res = '';
      if ($age > 60*60*24*15)
        $res = ' bgcolor="#880000"';
      if ($age > 60*60*24*30)
        $res = ' bgcolor="#AA0000"';
      if ($age > 60*60*24*60)
        $res = ' bgcolor="#CC0000"';
      return($res);
    }
	}	
	
	class ligne_devis
	{
		var $lde_id;
		var $dev_id;
		var $lde_type;
		var $lde_designation;
		var $lde_qtt;
		var $lde_prix_achat;
		var $lde_prix_vente;

                function ligne_devis($arg=array("LDE_ID"=>"0", "DEV_ID"=>"0", "LDE_TYPE"=>"Materiel", "LDE_DESIGNATION"=>"", "LDE_QTT"=>"1", "LDE_PRIX_ACHAT"=>"0", "LDE_PRIX_VENTE"=>"0"))
                {       
			if(is_int($arg))
			{
				$sql = "SELECT * FROM T_LIGNE_DEVIS NATURAL JOIN T_TYPE WHERE LDE_ID = '$ligne_id';";
				mysqlinforezo();
				$query = mysql_query($sql) or die (sql_error('devis', 'init_ligne_devis', 1, $sql));
				$liste =  mysql_fetch_array($query);
			}
			elseif(is_array($arg))
			{
				$liste = $arg;
			}
			if (is_array($liste))
			{
				$this->lde_id = $liste['LDE_ID'];
				$this->dev_id = $liste['DEV_ID'];
				$this->lde_type = $liste['TYP_ID'];
				$this->lde_designation = str_replace("\'","¤",$liste['LDE_DESIGNATION']);
				$this->lde_qtt = $liste['LDE_QTT'];
				$this->lde_prix_achat = $liste['LDE_PRIX_ACHAT'];
				$this->lde_prix_vente = $liste['LDE_PRIX_VENTE'];
        $this->lde_libelle = $liste['TYP_TYPE'];
			}
                } 

		function edit($switch=0, $no_ligne = 0)
		{
      $liste_id = '';
			$liste_type = funcSelectList("T_TYPE", "TYP_TYPE", "TYP_ID");
			$SELECT_TYPE = '<td>'.funcMakeFormList( 'lde_type'.$no_ligne, $liste_type, $this->lde_type, 'lde_type'.$no_ligne, 'onchange="ChoixType('.$no_ligne.')" ', $liste_id).'</td>';
			if ($switch)
			{
				$prix_achat = '<td><input type="text" size="7" maxlenght="10" name="lde_prix_achat'.$no_ligne.'" value="'.$this->lde_prix_achat.'" onchange="ChoixPrixAchat('.$no_ligne.')"></td>';
				$marge = '<td>'.($this->lde_prix_vente-$this->lde_prix_achat)*$this->lde_qtt.'</td>';
				if ($this->lde_prix_vente > 0) {
					$margePourCent = '<td>'. number_format(100 * ($this->lde_prix_vente-$this->lde_prix_achat)/$this->lde_prix_vente,  0, ',', ' ').'%</td>';
        } else {
					$margePourCent = '<td>NA</td>';
        }
			} else {
        $prix_achat = '';
        $marge = '';
        $margePourCent = '';
      }
			$EDIT ='<tr>
			'.$SELECT_TYPE.'
			<td><input type="text" size="60" name="lde_designation'.$no_ligne.'" value="'.$this->get_lde_designation().'"> </td>
			<td><input type="text" size="2" maxlength="3" name="lde_qtt'.$no_ligne.'" value="'.$this->lde_qtt.'"></td>
			'.$prix_achat.'
			<td><input type="text" size="7" maxlength="10" name="lde_prix_vente'.$no_ligne.'" value="'.$this->lde_prix_vente.'"></td>
			<td><input type="text" size="7" maxlength="10" name="lde_total'.$no_ligne.'" value="'.$this->lde_prix_vente*$this->lde_qtt.'" disabled"></td>
			'.$marge
			.$margePourCent.'
			<td><input type="submit" name="suppr'.$no_ligne.'" value ="suppr"></td>
			<td><input type="submit" name="insert'.$no_ligne.'" value ="inserer"></td>
			</tr>';
			return $EDIT;
		}
		
		function get_lde_designation()
		{
			$result = str_replace("¤","'",$this->lde_designation);
			return $result;
		}
		
		function pdf_lde_designation()
		{
			$result = utf8_decode($this->get_lde_designation());
			return $result;
		}
		
		function pdf_lde_puht()
		{
			if ($this->lde_type=='Titre' or $this->lde_prix_vente==0)
				$result = '';
			else
				$result = number_format($this->lde_prix_vente, 2, ',', ' ').' '.EURO;
			return $result;
		}

		function pdf_lde_ptht()
		{
			if ($this->lde_type=='Titre' or $this->lde_prix_vente==0)
				$result = '';
			else
				$result = number_format($this->lde_prix_vente * $this->lde_qtt, 2, ',', ' ').' '.EURO;
			return $result;
		}

		function pdf_lde_qtt()
		{
			if ($this->lde_type=='Titre' or $this->lde_qtt==0 )
				$result = '';
			else
				$result = $this->lde_qtt;
			return $result;
		}
		
		function preview()
		{
			$style = 'style="text-align: left;"';
			$PUHT = number_format($this->lde_prix_vente, 2, ',' , ' ').' €';
			$total = number_format($this->lde_qtt * $this->lde_prix_vente, 2, ',', ' ').' €';
			$qtt = $this->lde_qtt;
			if ($this->lde_type == 'Titre')
			{
				$style = 'style="text-align: center; padding: 15px 0;"';
				$PUHT = '&nbsp;';
				$total = '&nbsp;';
				$qtt = '&nbsp;';
			}
			$PREVIEW='
			<tr>
				<td '.$style.'>'.$this->get_lde_designation().'</td>
				<td>'.$qtt.'</td>
				<td style="text-align: right;">'.$PUHT.'</td>
				<td style="text-align: right;">'.$total.'</td>
			</tr>
			';
			return $PREVIEW;
		}

		function enreg()
		{
			$sql = "INSERT INTO T_LIGNE_DEVIS (LDE_ID, DEV_ID, TYP_ID, LDE_DESIGNATION, LDE_QTT, LDE_PRIX_ACHAT, LDE_PRIX_VENTE) VALUES ('', '$this->dev_id', '$this->lde_type', '$this->lde_designation', '$this->lde_qtt', '$this->lde_prix_achat', '$this->lde_prix_vente');";
			mysqlinforezo();
			mysql_query($sql) or die(sql_error('ligne_devis', 'enreg', 1, $sql));
			return 'ligne enregistrée';
		}
	}

	class liste_devis
	{
		var $tab_devis;

		function liste_devis($criteres='')
		{
      $liste_status = '';
			if(!is_array($criteres))
			{
				echo 'test';
				$sql = "select * from T_DEVIS WHERE DEV_STATUT != 'Perdu' ORDER BY DEV_ID DESC;";
			}
			else
			{
				if (empty($criteres['Statut']))
				{
					echo "il faut cocher au moins 1 case statut (a faire ou terminé)";
					return 0;
				}
				foreach ($criteres['Statut'] as $stat)
				{
					$liste_status .= "'".$stat."', ";
				}
				$liste_status = substr($liste_status, 0, -2);
				
				if ($criteres['TEMPS']=='jour')
          $where = 'WHERE (DATE(DEV_DATE_CREA)=CURDATE() OR (DEV_STATUT=\'A traiter\' AND DATE(DEV_DATE_CREA) < CURDATE())) AND';
				elseif ($criteres['TEMPS'] == 'semaine')
          $where = 'WHERE YEARWEEK(DEV_DATE_CREA) = YEARWEEK(CURDATE()) AND';
				elseif ($criteres['TEMPS'] == 'mois') 
          $where = 'WHERE MONTH(DEV_DATE_CREA) = MONTH(CURDATE()) AND YEAR(DEV_DATE_CREA) = YEAR(CURDATE()) AND';
				elseif ($criteres['TEMPS'] == 'tout')
          $where = 'WHERE';
				elseif ($criteres['TEMPS'] == 'custom') {
          $date_debut = date_input_to_mysql($criteres['date_debut']);
          $date_fin = date_input_to_mysql($criteres['date_fin']);
          $where = "WHERE DATE(DEV_DATE_CREA) BETWEEN '$date_debut' AND '$date_fin' AND";
        }
	
				$where .=" DEV_STATUT IN ($liste_status)";
				$cli_id = $criteres['cli_id'];
				if($cli_id != 'tous') $where .=" AND CLI_ID = $cli_id";
				$sql = "SELECT * FROM T_DEVIS $where ORDER BY DEV_ID DESC;";
			}
			$query = mysql_query($sql) or die(sql_error('liste_devis','liste_devis (contructeur)', 1, $sql));
			while ($liste = mysql_fetch_array($query))
			{
				$this->tab_devis[] = new devis ($liste);
			}
			
		}

		function test_check($temps, $statut=array("En cours","À facturer"), $marge)
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
      if (isset($criteres['date_debut'])) {
        $date_debut = $criteres['date_debut'];
      } else {
        $date_debut = '';
      }
      if (isset($criteres['date_fin'])) {
        $date_fin = $criteres['date_fin'];
      } else {
        $date_fin = '';
      }
			$liste = funcEnumList('T_DEVIS','dev_statut');
      if (isset($criteres['marge'])) {
        $marge = $criteres['marge'];
      } else {
        $marge = '';
      }
			$check = $this->test_check($temps, $statut, $marge);
      $quickSearch = '<div>';
      foreach ($liste as $item) {
        $quickSearch .= '<input type="submit" name="quickSearch" value="'.$item.'"><br>'."\n";
      }
      $quickSearch .= '</div>';
			$DISPLAY = '<div class = "bandeau"><h1>Interventions</h1><h2>'/*.$this->caption*/.'</h2></div>
			<span class="clear">&nbsp;</span>
      <div class="nouveau">
			<form method="post" action="./index2.php?contenu=view_devis&vue=formulaire">
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
      '.$quickSearch.'
		</form>
			</div>';
		return $DISPLAY;
		}
		
		function show($show_marge=false)
		{
      if (isset($_SESSION['ID'])){
        $SSMENU = 
        '<input type="submit" name="make_factures" value="créer les factures"> 
        <input type="submit" name="sign_devis" value="marquer comme signé"> 
        <input type="submit" name="fact_devis" value="marquer comme à facturer"> 
        <input type="submit" name="lost_devis" value="marquer comme perdu">'; 
      }
			if (empty($this->tab_devis))
			{
				$DISPLAY = "Aucun devis ne correspond au critères définits";
				return $DISPLAY;
			}
				
			$hide_marge = !$show_marge;
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
			<h1><a href='index2.php?contenu=view_devis&show_marge=$hide_marge'> Liste des devis </a></h1>
			<div class=\"nouveau\">&nbsp;</div>
			<table border>" .
			'<form method="post" action="./index2.php?contenu=change_selected_devis">' .
				"<tr>
					<th> N° </th>
					<th> Titre du devis </th>
					<th> Client </th>
					<th> Total HT </th>
					$marge
					<th> Date creation </th>
					<th> Statut </th>
					<th> Sélection </th>
				</tr>
			";
      $TotalHT = 0;
      $total_marge = 0;
      $CORPS = '';
			foreach ($this->tab_devis as $devis)
			{
				if ($show_marge)
					$marge = '<td style="text-align: right;"> '.number_format($devis->get_margeHT(),2, ',', ' ').' </td>';
				$CORPS.='
				<tr>
					<td> '.$devis->get_dev_id().' </td>
					<td><a href="index.php?contenu=devis&dev_id='.$devis->dev_id.'">'.$devis->get_dev_titre().'</a></td>
					<td><a href="?contenu=showcustomer&cli_id='.$devis->client->cli_id.'"> '.$devis->client->cli_societe.'<span>'.$devis->client->showSum().'</span></a> </td>
					<td style="text-align: right;"> '.number_format($devis->get_totalHT(),2, ',', ' ').' </td>
					'.$marge.'
					<td'.$devis->getColor().'> '.$devis->get_date_crea().' </td>
					<td> '.$devis->dev_statut.' </td>
					<td> <input type="checkbox" name="selectionDevis[]" value="'.$devis->get_dev_id().'"> </td>
				</tr>
				';
				$TotalHT += $devis->get_totalHT();
				$total_marge += $devis->get_margeHT();
			}
			if ($show_marge) {
				$total_marge_form='<th>'.number_format($total_marge,2,',',' ').' </th>';
      } else { 
        $total_marge_form = '';
      }
			$TOTAL = '<tr> 
					<th> </th>
					<th> </th>
					<th> Total </th>
					<th>'.number_format($TotalHT,2,',',' ').'</th>
					'.$total_marge_form.'
					<th> </th>
					<th> </th>
					<td><input type="checkbox" value="0" id="tous" onClick="this.value=check(\'selectionDevis[]\')"> </td>
				</tr>';
			$DISPLAY .= $TOTAL . $CORPS . $TOTAL . $SSMENU .
      '</form>
      </table>';
			return $DISPLAY;
		}
	}

?>

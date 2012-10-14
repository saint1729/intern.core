<?php
	define('EURO',chr(128));
	include "fpdf153/fpdf.php";
  include "fpdi/fpdi.php";

	class PDF_MC_Table extends FPDF
	{
		var $widths;
		var $aligns;

		//En-tête
		function Header()
		{
			//Logo
			//$this->Image('images/logo.jpg',10,8,33);
			//Police Arial gras 15
			$this->SetFont('Arial','B',15);
			//Décalage à droite
			$this->Cell(80);
			//Titre
			//$this->Cell(30,10,'Titre',1,0,'C');
			//Saut de ligne
			//$this->Ln(20);
		}

		//Pied de page
		function Footer()
		{
			//Positionnement à 1,5 cm du bas
			$this->SetY(-20);
			//Police Arial italique 8
			$this->SetFont('Arial','I',8);
			//Numéro de page
			//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
			//bla bla légal
			$this->SetFont('Arial','',10);
      $footer = utf8_decode(file_get_contents("tpl/footer.tpl"));
      $afooter = explode("\n",$footer);
			$this->Cell(0,7,$afooter[0],0,1,'C');
			$this->Cell(0,7,$afooter[1],0,1,'C');

		}
		
		function SetWidths($w)
		{
			//Tableau des largeurs de colonnes
			$this->widths=$w;
		}

		function SetAligns($a)
		{
			//Tableau des alignements de colonnes
			$this->aligns=$a;
		}

		function Row($data)
		{
			//Calcule la hauteur de la ligne
			$nb=0;
			for($i=0;$i<count($data);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
			$h=5*$nb;
			//Effectue un saut de page si nécessaire
			$this->CheckPageBreak($h);
			//Dessine les cellules
			for($i=0;$i<count($data);$i++)
			{
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Sauve la position courante
				$x=$this->GetX();
				$y=$this->GetY();
				//Dessine le cadre
				//$this->Rect($x,$y,$w,$h);
				//Dessine les lignes (gauche et droite)
				$this->Line($x,$y,$x,$y+$h);
				$this->Line($x+$w,$y,$x+$w,$y+$h);
				//Imprime le texte
				$this->MultiCell($w,5,$data[$i],0,$a);
				//Repositionne à droite
				$this->SetXY($x+$w,$y);
			}
			//Va à la ligne
			$this->Ln($h);
		}

		function CheckPageBreak($h)
		{
			//Si la hauteur h provoque un débordement, saut de page manuel
			if($this->GetY()+$h>$this->PageBreakTrigger)
				$this->AddPage($this->CurOrientation);
		}
		
		function NbLines($w,$txt)
		{
			//Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r",'',$txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb)
			{
				$c=$s[$i];
				if($c=="\n")
				{
					$i++;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
					continue;
				}
				if($c==' ')
					$sep=$i;
				$l+=$cw[$c];
				if($l>$wmax)
				{
					if($sep==-1)
					{
						if($i==$j)
							$i++;
					}
					else
						$i=$sep+1;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
				}
				else
					$i++;
			}
			return $nl;
		}
	}

  class concat_pdf extends FPDI { 
    var $files = array(); 

    function setFiles($files) { 
      $this->files = $files; 
    }
   
    function concat() { 
      if (! is_array($this->files)) {
        echo "Le nombre d'exemplaire des factures pour ce client est certainement à 0";
        return;
      }
      foreach($this->files AS $file) { 
        $pagecount = $this->setSourceFile($file); 
        for ($i = 1; $i <= $pagecount; $i++) { 
          $tplidx = $this->ImportPage($i); 
          $s = $this->getTemplatesize($tplidx); 
          $this->AddPage('P', array($s['w'], $s['h'])); 
          $this->useTemplate($tplidx); 
        } 
      } 
    } 
  } 

  class PDF_contrat extends FPDF {

    function addTitle($type) {
      if ($type == 'rezobackup')
        $titre = 'INFOREZO BACKUP';
      if ($type == 'rezobox')
        $titre = 'CONTRAT REZOBOX';
      if ($type == 'maintenance')
        $titre = 'CONTRAT D\'ASSISTANCE INFOREZO';
      $this->AddPage();
      $this->setFont('Arial', 'B', 15);
      $w=$this->GetStringWidth($titre)+6;
      $this->SetX((210-$w)/2);
      $this->Cell($w,9,$titre,0,0,'C',false);
      $this->Ln();
      echo $titre;
    }

    function addIntro($client) {
      $content = file_get_contents('tpl/intro.tpl');
      $content = str_replace('[CLI_SOCIETE]', $client->cli_societe, $content);
      $content = str_replace('EURO', chr(128), $content);
      $content = utf8_decode($content);
      $this->SetFont('Arial','',8);
      $this->MultiCell(0,4,$content);
      $this->Ln();
    }

    function addCorp($client, $type)
    {
      $content = file_get_contents('tpl/'.$type.'.tpl');
      $content = str_replace('[CLI_SOCIETE]', $client->cli_societe, $content);
      $content = utf8_decode($content);
      $this->SetFont('Arial', '', 8);
      $this->MultiCell(90,4,$content);
      if ($this->col == 0)
        $this->Ln(4);
      else {
        $this->AddPage();
        $this->SetCol(0);
      }
    }

    function addEnd($client, $type)
    {
      $this->addInfoClient($client);
      if ($type == 'rezobackup')
        $this->addBackup($client);
      if ($type == 'rezobox')
        $this->addRezobox($client);
      if ($type == 'maintenance')
        $this->addMaintenance($client);
      $this->addPay($client);
    }

    function addRezobox($client)
    {
      $this->Cell(0,5,'Le Contrat REZOBOX:',0,1);

      $this->Cell(150,5,utf8_decode('Configuration par défaut:'),'LT',0);
      $this->Cell(30,5,EURO.' HT / Mois','TR',1, 'R');

      $this->Cell(5,5,'','L',0);
      $this->Cell(3,3,'X',1,0);
      $this->Cell(30,5,'Anti-Spam',0,0);
      $this->Cell(3,3,'X',1,0);
      $this->Cell(30,5,'Proxy-Transparent',0,0);
      $this->Cell(109,5,'','R',1);

      $this->Cell(5,5,'','L',0);
      $this->Cell(3,3,'X',1,0);
      $this->Cell(30,5,'Anti-Virus',0,0);
      $this->Cell(3,3,'X',1,0);
      $this->Cell(30,5,utf8_decode('Firewall (Internet->Réseau)'),0,0);
      $this->Cell(109,5,'','R',1);
      $this->Cell(180,5,'','LRB',1);
      
      $this->mkcell('Option 1 : Firewall (Réseau->internet)', '',1);
      $this->mkcell('Option 2 : Proxy par Identification', '',1);
      $this->mkcell('Option 3 : Location de l\'ordinateur', '',1);
      $this->mkcell('Total du contrat REZOBOX', $client->cli_rezo_box/12);
    }

    function mkcell($opt, $price, $chkbx = false)
    {
      $comp = 0;
      $this->Cell(180,5,'','LR',1);
      $this->Cell(5,5,'','L',0);
      if ($chkbx)
        $this->Cell(3,3,'',1,0);
      else
        $comp = 3;
      $this->Cell(142,5,utf8_decode($opt),0,0);
      $this->Cell(30+$comp,5, $price .' '.EURO.' HT / Mois','R',1,'R');
      $this->Cell(180,5,'','LRB',1);
    }

    function addMaintenance($client)
    {
      $this->Cell(0,5,'Montant du contrat Hors taxe et en euros',0,1);
      $this->Cell(100,5,utf8_decode('Désignation'),1,0,'C');
      $this->Cell(50,5,'Nbr de postes',1,0,'C');
      $this->Cell(30,5,'Montant',1,1,'C');
      $this->Cell(100,5,'Contrat de maintenance informatique',1,0);
      $this->Cell(50,5,$client->cli_nb_poste,1,0,'C');
      $this->Cell(30,5,$client->cli_mtt_maintenance . ' ' . EURO .' HT/An',1,1,'R');
      $this->Cell(100,5,'Montant annuel du contrat',1,0);
      $this->Cell(80,5,$client->cli_mtt_maintenance . ' ' . EURO .' HT/An',1,1,'R');
    }

    function addBackup($client)
    {
      $txt = 'Le contrat INFOREZO Backup:';
      $this->Cell(0,5,$txt, 0, 1);
      $txt = 'Sauvegarde Quotidienne';
      $this->Cell(5,5,'',1,0);
      $this->Cell(165,5,$txt,'LTB',0);
      $txt = '50 ' . EURO . ' HT / Mois';
      $this->Cell(20,5,$txt,'TBR',1);
      $txt = 'Sauvegarde Hebdomadaire';
      $this->Cell(5,5,'',1,0);
      $this->Cell(165,5,$txt,'LTB',0);
      $txt = '30 ' . EURO . ' HT / Mois';
      $this->Cell(20,5,$txt,'TBR',1);
      $txt = 'Montant du contrat rezobackup:';
      $this->Cell(170,5,$txt,'LTB',0);
      $txt = $client->cli_rezo_backup/12 . ' ' . EURO . ' HT / Mois';
      $this->Cell(20,5,$txt,'TBR',1,'R');
    }

    function addPay($client)
    {
      if ($client->cli_echeances_abo == 'mois')
        $m = 'X';
      if ($client->cli_echeances_abo == 'trimestre')
        $t = 'X';
      if ($client->cli_echeances_abo == 'semestre')
        $s = 'X';
      if ($client->cli_echeances_abo == 'année')
        $a = 'X';
      $this->Cell(0,5,'Choix du payement:', 0,1);
      $this->Cell(3,3,$m,1,0);
      $this->Cell(40,5,'Mensuel (+2%)', 0, 0);
      $this->Cell(3,3,$t,1,0);
      $this->Cell(40,5,'Trimestriel',0,0);
      $this->Cell(3,3,$s,1,0);
      $this->Cell(40,5,'Semestriel',0,0);
      $this->Cell(3,3,$a,1,0);
      $this->Cell(40,5,'Annuel',0,1);
      $this->Cell(0,5,utf8_decode('Fait en deux exemplaires à St Etienne, le'),0,1);
      $this->Cell(100,5,'INFOREZO',0,0);
      $txt = utf8_decode('Pour la société: ' . $client->cli_societe);
      $this->Cell(100,5,$txt,0,2);
      $this->Cell(100,5,'Date et signature',0,2);
      $txt = utf8_decode('Précédées de la mention "Bon pour accord"');
      $this->Cell(100,5,$txt,0,0);
      $this->Ln(5);
      $this->Cell(100,5,'',0,0);
      $this->Cell(100,15,'Cachet:',0,2);
      $this->Cell(90,50,'',1,1);

    }
    
    function addInfoClient($client)
    {
      $intro = utf8_decode('INFORMATION NÉCESSAIRE A LA VALIDITÉ DU CONTRAT');
      $societe = utf8_decode('Societe: ' . $client->cli_societe);
      $adresse = utf8_decode('Adresse: ' . $client->cli_adresse);
      $telephone = utf8_decode('Telephone: ' . $client->cli_telephone);
      $telecopie = utf8_decode('Télécopie: ' . $client->cli_telecopie);
      $responsable = utf8_decode('Responsable: ' . $client->get_cli_responsable());
      $this->Cell(0,5,$intro,0,2);
      $this->Cell(0,5,$societe,0,2);
      $this->Cell(0,5,$adresse,0,2);
      $this->Cell(0,5,$telephone,0,2);
      $this->Cell(0,5,$telecopie,0,2);
      $this->Cell(0,5,$responsable,0,2);
    }

    function AcceptPageBreak()
    {
      //Méthode autorisant ou non le saut de page automatique
      if($this->col<1)
      {
          //Passage à la colonne suivante
          $this->SetCol($this->col+1);
          //Ordonnée en haut
          $this->SetY($this->y0 + 50);
          //On reste sur la page
          return false;
      }
      else
      {
          //Retour en première colonne
          $this->SetCol(0);
          //Saut de page
          return true;
      }
    }

    function SetCol($col)
    {
      //Positionnement sur une colonne
      $this->col=$col;
      $x=10+$col*95;
      $this->SetLeftMargin($x);
      $this->SetX($x);
    }
  }
  
  class PDF_letter extends FPDF {
    function addHeader($client) {
      $this->addPage();
      $this->addLogo();
      $this->addInfo();
      $this->addAddress($client);
    }
    function addContent($filetpl, $facture){
      $this->SetFont('Times','',12);
      $tpl = file_get_contents($filetpl);
      $tpl = utf8_decode($tpl);
      preg_match('#^(.*)\[facture\](.*)$#Usi',$tpl,$part);
      $this->MultiCell(0,5,$part[1],0,1);
      $this->addFacture($facture);
      $this->MultiCell(0,5,$part[2],0,1);
      $this->addSign();
    }

    function addFacture($facture) {
      $this->Ln(5);
      $this->Cell(80,5,'Date:', 0,0,'',true);
      $this->Cell(80,5,utf8_decode('N° Facture:'), 0,0,'',true);
      $this->Cell(80,5,'Montants:', 0,1,'',true);
      $this->Cell(80,5,date('d/m/y',$facture->fac_date_crea),0,0);
      $this->Cell(80,5,$facture->get_fac_numero(),0,0);
      $this->Cell(80,5,$facture->get_totalTTC(). ' '.EURO,0,1);
    }

    function addSign()
    {
      $this->Ln(10);
      $this->Cell(0,5,'Laurent Montes',0,0,'R');
    }
    function addLogo() {
      $this->Image('images/logo.jpg', 10,8,33);
      $this->SetY(45);
    }
    function addInfo() {
      $address = utf8_decode('10 rue Victor Grignard');
      $codePostal = '42 000';
      $ville = 'St Etienne';
      $tel = '04 77 92 48 91';
      $fax = '04 77 91 23 09';
      $this->SetFont('Times', '', 8);
      $this->Cell(0,3,$address,0,1);
      $this->Cell(0,3,$codePostal.' '.$ville,0,1);
      $this->Cell(0,3,'Tel: ' . $tel,0,1);
      $this->Cell(0,3,'Fax: ' . $fax,0,1);
    }
    function addAddress($client) {
      $this->SetFont('Times', '', 12);
      $this->SetX(120);
      $this->Cell(0,10,$client->cli_societe, 0,2);
      $this->Cell(0,10,$client->cli_adresse, 0,2);
      $this->Cell(0,10,$client->cli_code_postal . ' ' . $client->cli_ville, 0,1);
    }
    function addTitle($title) {
      setlocale(LC_TIME, 'fr_FR' );
      $title = utf8_decode($title);
      $date = strftime('le %d %B %G');
      $this->Ln(10);
      $this->Cell(120,5,'Objet: ', 0,0);
      $this->Cell(0,5,'Saint-Etienne,',0,1);
      $this->Cell(120,5,$title, 0,0);
      $this->Cell(0,5,$date,0,1);
      $this->SetFont('Arial','B',20);
      $this->SetFillColor(127);
      $this->Ln(10);
			$this->Cell(0,10,$title,1,1,'C',true);
      $this->Ln(10);
    }
  }
?>

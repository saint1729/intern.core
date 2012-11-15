<?php
//*************************************************************************************************************************//
//						Les fonctions utiles pour inforezo
//
//*************************************************************************************************************************//
// fonction mysqlinforezo : cette fonction va nous connecter mysql puis a la base de donnée inforezo
require_once("config/conf.inc.php");
function mysqlinforezo()
{
	$server=DB_SERVER;
	$user=DB_USER;
	$pass=DB_PASSWORD;
	$db=DB_NAME;
	mysql_connect($server, $user, $pass) or die('Erreur de connexion');
	mysql_select_db($db) or die('Base inexistante');
	return true;
}

// fonction veriflogin : cette fonction verifie qu'il n'y a pas de triche, elle doit être présente au début de chaque page, elle vérifié que le login, le mot de passe
function veriflogin($login, $pwd)
{
	mysqlinforezo();
	$SQL = "SELECT USE_PWD FROM T_USER WHERE USE_LOGIN = '$login'";
	$query = mysql_query($sql) or die( 'Erreur: impossible de vérifier qui est connecté' );
	$dt=mysql_fetch_array($query);
	mysql_close();
	if (($pwd == $dt['USE_PWD']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

//fonction DateFrToUs => convertit la date du format français au format utilisé par mysql (remplace les années inferieures à 100 par 2000+ l'année)
// $datefr => une date au format français jj/mm/aa ou jj/mm/aaaa
// $dateus => une date au format américain mysql aaaa/mm/jj
function DateFrToUs($datefr)
{
	list($day, $month, $year) = explode("/", $datefr);
	if ($year < 100) $year= $year + 2000;
	$dateus = $year.'-'.$month.'-'.$day;
	return $dateus;
}
function date_input_to_mysql($string)
{
	$bon_format = '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{1,4}$';
	if (ereg($bon_format, $string))
	{
		$table_date = split("/", $string);
		$jour = $table_date[0];
		$mois = $table_date[1];
		$anne = $table_date[2];
		if (checkdate($mois, $jour, $anne))
		{
			$time_stp=mktime(0,0,0,$mois,$jour,$anne);
			$result = date('Y-m-d', $time_stp);
		}
		else
		{
			$result = "Ereur: date non valide, le format est bon mais ce jour n'existe pas<br> $anne $mois $jour";
		}
	}
	else
	{
		$result= "Erreur: format de la date non valide";
	}
	return $result;
}

// fonction DateUstoFr => convertit la date du format us mysql au format français, a utiliser avant d'afficher une date provenant de la base de donnée
// $dateus => une date au format américain mysql aaaa/mm/jj
// $datefr => une date au format français jj/mm/aaaa
function DateUsToFr($dateus)
{
	list($year, $month, $day) = explode("-", $dateus);
	$datefr = $day.'/'.$month.'/'.$year;
	return $datefr;
}

// fonction funcEnumList : retourne un tableau contenant les vleur d'un champs de type enum
// $strTable : nom de la table
// $strEnumField : nom du champ enum dont on veut lister les valeurs 
function funcEnumList( $strTable, $strEnumField ) 
{ 
	mysqlinforezo();
	$reqListEnum = 'SHOW COLUMNS FROM '. $strTable .' LIKE "'.$strEnumField.'";';
	$recListEnum = mysql_query($reqListEnum);
	$arrRow = mysql_fetch_assoc($recListEnum);
//memoire libere toi
	mysql_free_result($recListEnum);
	$arrEnum = explode(
		'\',\'',
		preg_replace(
			'/.*\(\'(.*)\'\)/',
			'\\1',
			$arrRow['Type']
		)
	); 
	mysql_close();
	return $arrEnum;
}

// fonction funcSelectList : retourne un tableau contenant les valeur d'un champs
// strTable : nom de la table
// $strField : nom du champs
function funcSelectList ($strTable, $strLabelField, $strValueField) {
  mysqlinforezo();
  $query = "SELECT $strLabelField, $strValueField from $strTable;";
  $res = mysql_query($query);
  $i = 0;
  while ($row = mysql_fetch_array($res, MYSQL_NUM)) {
    $array[$i]['label'] = $row[0];
    $array[$i]['value'] = $row[1];
    $i++;
  }
  mysql_close();
  return ($array);
}


// fonction funcMakeFormList retourne une chaine de caractère qui écrit un select
// $strName : Nom du select
// $arrValue : contenu de la liste les éléments de la liste (par exemple le tableau arrEnum
// $strId : id de la liste
// $strOpt : option html de la liste
// $strSelected : option selectionné par defaut
function funcMakeFormList( $strName, $arrValue, $strSelected = '', $strId = '', $strOpt = '') {
 if ( $strId == '' ) {
  $strId = $strName;
 }
 if ( $strOpt == '' ) {
  $strOpt = ' ';
 }
 $strHtml = '<select name="' . $strName . '" id="' . $strId . '"' . $strOpt . '>';
 foreach ( $arrValue as $strValue ) {
 if ( is_array($strValue)) {
   $strLabel = $strValue['label'];
   $strValue = $strValue['value'];
 } else {
   $strLabel = $strValue;
 }
  if ($strValue == $strSelected ) {
   $strHtmlSelected ='selected="selected"';
  } else {
   $strHtmlSelected ='';
  }
  $strHtml.= '<option value="' . $strValue . '"' . $strHtmlSelected . '>' . $strLabel . '</option>';
 }
 $strHtml.= '</select>';
 return $strHtml;
}

// fonction funcMakeFormCheckbox retourne une chaine de caractère qui écrit un groupe de case à cocher
// $strName : Nom du groupe de case 
// $arrValue : contenu de la liste les éléments de la liste (par exemple le tableau arrEnum)
// $strSelected : option selectionné par defaut
function funcMakeFormCheckbox( $strName, $arrValue, $arrSelected = array(‘’))
{
	foreach ( $arrValue as $strValue )
	{
		if (is_array($arrSelected) && in_array($strValue, $arrSelected))
		{
			$checked ='checked';
		}
		else
		{
			$checked ='';
		}
		$strHtml= '<label for="'.$strValue.'">'.$strValue.'</label><input type="checkbox" name="'.$strName.'" value="'.$strValue.'" id="'.$strValue.'" '.$checked.'>';
		    
	}
	return $strHtml;
}

// fonction funcMakeFormRadio retourne une chaine de caractère qui écrit un groupe de boutons radios
// $strName : Nom du groupe de boutons 
// $arrValue : contenu de la liste les éléments de la liste (par exemple le tableau arrEnum)
// $strSelected : option selectionné par defaut
function funcMakeFormRadio( $strName, $arrValue, $strSelected = ‘’)
{
	foreach ( $arrValue as $strValue )
	{
		if ($strValue == $strSelected)
		{
			$checked ='checked';
		}
		else
		{
			$checked ='';
		}
		$strHtml.= '<label for="'.$strValue.'">'.$strValue.'</label><input type="radio" name="'.$strName.'" value="'.$strValue.'" id="'.$strValue.'" '.$checked.'>';
		    
	}
	return $strHtml;
}


function sql_error($class, $fonction, $no_requete, $requete)
{
	if ($no_requete==1)
		$num_requete='1ere';
	else
		$num_requete=$no_requete.'e';
	$result='<span class="clear">&nbsp;</span><h3>Erreur</h3><h4>classe '.$class.'</h4><h5>fonction '.$fonction.'</h5><h6>'.$num_requete.' requete</h6><p><strong>Requete: </strong>'.$requete.'</p><p><strong>Erreur:</strong> '.mysql_error().'</p>';
	return $result;
}

function relier($cli_id=0, $con_id=0)
{
	if ($cli_id !=0 && $con_id!=0)
	{
		$sql= "INSERT INTO L_CLI_CON (CLI_CON_ID, CLI_ID, CON_ID) VALUES ('', '$cli_id', '$con_id');";
		mysqlinforezo();
		$query = mysql_query($sql) or die(sql_error('inforezo', 'relier', 1, $sql));
		$result=1;
	}
	else
	{
		$result=0;
	}
	return $result;
}

function euclidivision($nombre , $diviseur)
{
	$nombre = intval($nombre)+0;
	$diviseur = intval($diviseur)+0;
	if ($diviseur == 0){ return array();}
	if ($nombre == 0){ return array(0,0);}
	$quotient = floor($nombre/$diviseur);
	$reste = $nombre - $quotient*$diviseur;
	if ($reste < 0)
	{
		if ($diviseur > 0)
		{
			$quotient--;
			$reste += $diviseur;
		}
		else
		{
			$quotient++;
			$reste -= $diviseur;
		}
	}
	return array($quotient, $reste);
}
// Affiche le texte
function mise_en_page($dispTxt)
{
		//supression des espaces  et autre en fin de chaîne
	$dispTxt= rtrim("$dispTxt");
	$dispTxt = ereg_replace("</li>\r", "</li>\n", $dispTxt);
	$dispTxt = ereg_replace("</li> \r", "</li>\n", $dispTxt);
		//remplacement des saut des retour chariot par des paragraphes
	$dispTxt = ereg_replace("\r", "\n</p><p>", $dispTxt);
		//remplacement des paragraphes vide par des br
	$dispTxt = ereg_replace("<p>\n\n</p>", "\n<br />\n", $dispTxt);
		//enlever les paragraphes autour des titres
	$dispTxt = ereg_replace("<p>\n<h6>", "\n<h6>", $dispTxt);
	$dispTxt = ereg_replace("</h6>\n</p>", "</h6>\n", $dispTxt);
	$result='<div class="mise_en_page">'.$dispTxt;
	//test si la dernière balise est un paragraphe ou un titre todo: probleme si que des chiffre dans $dispTxt
	if(!((strrpos($dispTxt,"</h6>")+5)==strlen($dispTxt)))
	{
		$result.='</div><span class="clear">&nbsp;</span>';
	}
	return $result;
}

	function printr($var)
	{
		print("<pre>***");
		print_r($var);
		print("***</pre>");
	}

  class table
  {
    private $array;
    private $titles;
    private $class;
    private $showKeys;

    public function __construct($array) {
      $this->array = $array;
      $this->showKeys = true;
    }
    
    public function display($invert = false) {
      $DISPLAY = "<table" . $this->getClass() . ">";
      if (is_array($this->titles)) {
        $DISPLAY .= "<tr>\n";
        foreach ($this->titles as $title)
          $DISPLAY .="<th>$title</th>\n";
        $DISPLAY .= "</tr>\n";
      }
      foreach ($this->array as $key => $values) {
        $DISPLAY .= "<tr>\n";
        if ($this->showKeys) {
          $DISPLAY .= "<th>$key</th>\n";
        }
        foreach ($values as $value) {
          if (is_double($value)) {
            $value = number_format($value, 2, ',', ' ');
          }
          $DISPLAY .= "<td>" . $value . "</td>\n";
        }
        $DISPLAY .= "</tr>";
      }
      $DISPLAY .= "</table>";
      return $DISPLAY;
    }

    public function hideKeys() {
      $this->showKeys = false;
    }

    public function setClass($class) {
      $this->class = $class;
    }

    public function addTitles($titles) {
      $this->titles = $titles;
    }

    private function getClass() {
      if (isset($this->class))
        return " class=\"$this->class\"";
    }

  }

// function timeSelect => print 2 select with start-stop time
function timeSelect($params) {
  $table = "
    <option>9</option>
    <option>10</option>
    <option>11</option>
    <option>12</option>
    <option>13</option>
    <option>14</option>
    <option>15</option>
    <option>16</option>
    <option>17</option>
    <option>18</option>
    <option>19</option>
  ";
  $startName = 'timeStart';
  $endName = 'timeEnd';
  if (isset($params['startName']))
    $startName = $params['startName'];
  if (isset($params['endName']))
    $endName = $params['endName'];
  if (isset($params['startLabel']))
    $startLabel = '<label for="timeStart">' . $params['startLabel'] . '</label>';
  if (isset($params['endLabel']))
    $endLabel = '<label for="timeEnd">' . $params['endLabel'] . '</label>';
  $form = $startLabel . '<select id="timeStart" name="'.$startName.'">' . $table . '</select>' .
  $endLabel . '<select id="timeEnd" name="' . $endName . '">' . $table . '</select>';
  if ($params['div'] === true)
    $form = '<div>' . $form . '</div>';
  return ($form);
}

/**
 * @insert a new array member at a given index
 * @param array $array
 * @param mixed $new_element
 * @param int $index
 * @return array
 */
function insertArrayIndex($array, $new_element, $index) {
 /*** get the start of the array ***/
 $start = array_slice($array, 0, $index); 
 /*** get the end of the array ***/
 $end = array_slice($array, $index);
 /*** add the new element to the array ***/
 $start[] = $new_element;
 /*** glue them back together and return ***/
 return array_merge($start, $end);
 }


?>

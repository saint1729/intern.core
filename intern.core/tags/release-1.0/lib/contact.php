<?php
include ('inforezo.php');
class contact
{
// déclaration des variables de classe
	var $con_id;
	var $con_prenom;
	var $con_nom;
	var $con_email;
	var $con_portable;
	var $con_autre;
	var $con_responsable;
//methodes de classe
//constructeur: si l'id est 0 on crée un objet vide
//		si l'id est différente de 0 on se connect à la base de donné pour récupérer les valeurs de l'objet
	function contact($con_id=0)
	{
		if ($con_id==0)
		{
			$this->con_id = "";
			$this->con_prenom= "";
			$this->con_nom= "";
			$this->con_email= ""; 
			$this->con_portable= "";
			$this->con_autre= "";
			$this->con_responsable= "";
		}
		else
		{
			mysqlinforezo();
			$sql = "SELECT CON_ID, CON_PRENOM, CON_NOM, CON_PORTABLE, CON_EMAIL, CON_RESPONSABLE, CON_AUTRE FROM T_CONTACT WHERE CON_ID = '$con_id';";
			$query = mysql_query($sql) or die( 'Erreur lors de la vérification des données'.$sql );
			$list=mysql_fetch_array($query);
			$this->con_id = $con_id;
			$this->con_prenom= $list['CON_PRENOM'];
			$this->con_nom= $list['CON_NOM'];
			$this->con_email= $list['CON_EMAIL']; 
			$this->con_portable= $list['CON_PORTABLE'];
			$this->con_autre= $list['CON_AUTRE'];
			$this->con_responsable= $list['CON_RESPONSABLE'];
			mysql_close();
		}
	}
	function get_id()
	{
		return $this->con_id;
	}
	function get_nom()
	{
		if ($this->con_nom=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_nom;
		return $result;
	}
	function get_prenom()
	{
		if ($this->con_prenom=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_prenom;
		return $result;
	}
	function get_prenom_nom()
	{
		if ($this->con_nom=="" && $this->con_prenom=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_prenom.' '.$this->con_nom ;
		return $result;
	}

	function get_portable()
	{
		if ($this->con_portable=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_portable;
		return $result;
	}

	function get_email()
	{
		if ($this->con_email=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_email;
		return $result;
	}
	function get_autre()
	{
		if ($this->con_autre=="")
			$result= '&nbsp;';
		else 
			$result= $this->con_autre;
		return $result;
	}
	function enreg()
	{
		mysqlinforezo();
		
		if ($this->con_id!="")	// update
		{
			$sql = "UPDATE T_CONTACT SET CON_RESPONSABLE='$this->con_responsable', CON_NOM='$this->con_nom', CON_PRENOM='$this->con_prenom', CON_EMAIL='$this->con_email', CON_PORTABLE='$this->con_portable', CON_AUTRE='$this->con_autre' WHERE CON_ID=$this->con_id;";
		$query = mysql_query($sql) or die( 'Erreur lors de l\'enregistrement de l\'objet contact'.$sql );
		}
		else		// insert
		{
			$sql = "INSERT INTO T_CONTACT (CON_ID, CON_RESPONSABLE, CON_NOM, CON_PRENOM, CON_EMAIL, CON_PORTABLE, CON_AUTRE) VALUES ('','$this->con_responsable', '$this->con_nom', '$this->con_prenom', '$this->con_email', '$this->con_portable', '$this->con_autre');";
		$query = mysql_query($sql) or die( 'Erreur lors de l\'enregistrement de l\'objet contact'.$sql );
		$this->con_id=mysql_insert_id();
		}
		mysql_close();
	}
		
	function edit($cli_id='',$type='DEFAUT')
	{
		if ($this->con_id == 0)
		{
			$bouton= "Ajouter";
			$hidden_name='cli_id';
			$hidden_value=$cli_id;
      $suppr='';
		}
		else
		{
			$bouton= "Modifier";
			$hidden_name='con_id';
			$hidden_value=$this->con_id;
			$suppr=' 
				<span class="supprimer">
					<input type="submit" name="Supprimer" value="Supprimer">
				</span>
			';
		}	
		if ($this->con_responsable == 1)
			$checked="checked";
		else 
			$checked="";
		if($type=='DEFAUT')
		{
			$START_FORM='<form method="post" action="./index2.php?contenu=updatecontact">';
			$BOUTON_FORM='
			<span class="modifier">
    				<input type="submit" name="'.$bouton.'" value="'.$bouton.'">
			</span>
			'.$suppr;
			$END_FORM='</form>';
		}
		$EDIT_CONTACT=$START_FORM.'
	<p>
		<span class="responsable">
			<input type="checkbox" name="con_responsable" value="1" '.$checked.'>
		</span>
		<span class="nom">
			<input type="text" name="con_nom" value="'.$this->con_nom.'" size="10" maxlength="19">
		</span>
		<span class="prenom">
			<input type="text" name="con_prenom" value="'.$this->con_prenom.'" size="10" maxlength="19">
		</span>
		<span class="email">
			<input type="text" name="con_email" value="'.$this->con_email.'" size="15" maxlength="49">
		</span>
		<span class="portable">
			<input type="text" name="con_portable" value="'.$this->con_portable.'" size="10" maxlength="19">
		</span>
		<span class="autre">
			<input type="text" name="con_autre" value="'.$this->con_autre.'" size="10" maxlength="19">
		</span>
		<input type="hidden" name="'.$hidden_name.'" value="'.$hidden_value.'"/>
		'.$BOUTON_FORM.'
	</p>	
		'.$END_FORM;
		return $EDIT_CONTACT;
	}
	function show()
	{
		if ($this->con_responsable==1)
			$resp='oui';
		else
			$resp='non';
		$SHOW_CONTACT='
	<p> 
		<span class="responsable">
			'.$resp.'
		</span>
		<span class="nom">
			'.$this->get_nom().'
		</span>
		<span class="prenom">
			'.$this->get_prenom().'
		</span>
		<span class="email">
			'.$this->get_email().'
		</span>
		<span class="portable">
			'.$this->get_portable().'
		</span>
		<span class="autre">
			'.$this->get_autre().'
		</span>
	</p>	
		';
		return $SHOW_CONTACT;
	}
	function test4enreg()
	{
		if (!$this->con_nom)
		     $result = 0;
		else
			$result = 1;
		return $result;
				 
	}
	function no_enreg()
	{
		$result='<div class="sumadmin">Aucun nom n\'est spécifié le contact ne sera pas enregistrer</div>';
		return $result;
	}
	function warning()
	{
		if (
		(empty($this->con_prenom)) ||
		(empty($this->con_email)) ||
		(empty($this->con_portable)) ||
   		(empty($this->con_autre)))
		{
			$result= '<div class="presentation">';
			$fin='</div>';
		}
		if(empty($this->con_prenom))
		{
			$result=$result."<center>Avertissement: Le '<b>prénom du contact</b>' est vide !</center>";
		}
		if(empty($this->con_email))
		{
			$result=$result."<center>Avertissement: L' <b>Email</b> est vide !</center>";
		}
		if(empty($this->con_portable))
		{
			$result=$result."<center>Avertissement: Le '<b>numéro de portable</b>' du contact est vide !</center>";
 		}
   		if(empty($this->con_autre))
   		{
			$result=$result."<center>Avertissement: L' '<b>Autre moyen de contact </b>' est vide !</center>";
   		}
		$result=$result.$fin;
		return $result; 
	}
	function delete()
	{
		mysqlinforezo();
		$sql="DELETE FROM L_CLI_CON WHERE CON_ID = $this->con_id";
		$query=mysql_query($sql) or die (sql_error('contact', 'delete', 1, $sql));
		$sql="DELETE FROM T_CONTACT WHERE CON_ID = $this->con_id";
		$query=mysql_query($sql) or die (sql_error('contact', 'delete', 2, $sql));
		mysql_close();
		
	}
}
define
(
	"TITRE_CONTACT",
	'<div class="entete">
		<p>
			<span class="responsable">Responsable</span>
			<span class="nom">Nom*</span>
			<span class="prenom">Prénom</span>
			<span class="email">Email</span>
			<span class="portable">Portable</span>
			<span class="autre">Autre</span>
		</p>
	</div>'
);
// test de la librairie
/*
$test= new contact(17);
echo $test->get_prenom_nom();
*/
?>

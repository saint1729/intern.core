<?php

class user
{
  private static $rootEmail = null;
	private $use_id;
	private $use_login;
	private $use_pwd;
	private $use_prenom;
	private $use_nom;
	private $use_portable_pro;
	private $use_portable_perso;
	private $use_email;
  private $use_rights;
  private $use_visible;


	public function __construct($use_id) {
    if (is_array($use_id)) {
      $this->load($use_id);
    }
		elseif ($use_id!=0) {
			mysqlinforezo();
			$sql="SELECT * FROM T_USER WHERE USE_ID=$use_id";
			$query = mysql_query($sql) or die( 'Erreur SQL constructeur de classe user'.$sql );
			$liste=mysql_fetch_array($query);
			mysql_close();
      $this->load($liste);
		}
	}

  public function user2line() {
    $line = array($this->user2link(), $this->use_rights, $this->use_visible, $this->use_email);
    return $line;
  }

  public function user2link() {
    $link =  '<a href="?contenu=admin&page=users&action=update&use_id='.$this->use_id.'">'. $this->use_prenom .' '. $this->use_nom  .'</a>';
    return $link;
  }

  public function user2form() {
  }

  public function getEmail() {
    if ($this->use_email == '') {
      return $this->getRootEmail();
    } else {
      return $this->use_email;
    }
  }

  public function getRootEmail() {
    if (self::$rootEmail === null ) {
      self::$rootEmail = self::initRootEmail();
    }
    return self::$rootEmail;
  }

  public function getPrenomNom() {
    return ($this->use_prenom . ' ' . $this->use_nom);
  }

  private static function initRootEmail() {
    mysql_inforezo();
    $sql="select USE_EMAIL from T_USER where USER_RIGHTS=4 ORDER BY USE_EMAIL DESC LIMIT 1;";
    $query = mysql_query($sql) or die(sql_error('user', 'initRootEmail', 1, $sql));
    $email = mysql_fetch_array($query);
    return ($email['USE_EMAIL']);

  }

  private function load($liste) {
    $this->use_id = $liste['USE_ID'];
    $this->use_login = $liste['USE_LOGIN'];
    $this->use_pwd = $liste['USE_PWD'];
    $this->use_prenom = $liste['USE_PRENOM'];
    $this->use_nom = $liste['USE_NOM'];
    $this->use_portable_pro = $liste['USE_PORTABLE_PRO'];
    $this->use_portable_perso = $liste['USE_PORTABLE_PERSO'];
    $this->use_email = $liste['USE_EMAIL'];
    $this->use_rights = $liste['USER_RIGHTS'];
    $this->use_visible = $liste['USE_VISIBLE'];
  }
}	

class list_users
{
  private $users;

  public function __construct()
  {
    mysqlinforezo();
    $sql = "SELECT * FROM T_USER;";
    $query = mysql_query($sql) or die('Error');
    while ($list = mysql_fetch_array($query)) {
      $this->users[] = new user($list);
    }
  }

  public function show()
  {
    $tab = new table($this->users2Tab());
    $tab->addTitles(array('Utilisateur', 'Droits', 'Visible', 'Email'));
    $tab->hideKeys();
    echo $tab->display();
    printr($this->users);

  }
  
  private function users2Tab()
  {
      foreach ($this->users as $user) {
        $tab[] = $user->user2line();
      }
      return $tab;
  }

}

function MakeSelectUsers($Default_User_ID=2)
{
	mysqlinforezo();
	$sql = 'SELECT USE_ID, USE_LOGIN FROM T_USER WHERE USE_VISIBLE=1 order by USE_LOGIN;';
	$query = mysql_query($sql) or die( 'Erreur' );
	$SELECT_USERS='<select name="use_id">';
	while ( $list = mysql_fetch_array( $query ) )
	{
		$u="";
	 	if ($list['USE_ID'] == $Default_User_ID) $u="selected";
		$SELECT_USERS=$SELECT_USERS.'<option value="'.$list['USE_ID'].'" '.$u.'>'.$list['USE_LOGIN'].'</option>';
	}
	$SELECT_USERS=$SELECT_USERS.'</select>';
	mysql_close();
	return $SELECT_USERS;
}

function ListAllUsers()
{
	mysqlinforezo();
	$sql= 'SELECT USE_ID, USE_LOGIN, USE_PRENOM FROM T_USER WHERE USE_VISIBLE=1 order by USE_PRENOM;';
	$query = mysql_query($sql) or die(sql_error('User', 'ListAllUsers', 1, $sql));
	while( $list = mysql_fetch_array($query))
	{
		$result[]=$list['USE_ID'];
		
	}
	return $result;
}	

function MakeCheckboxUsers($default_users_id)
{	
  $keys = array_keys($default_users_id);
	if ($default_users_id[$keys[0]]=="") 
	{
		$default_users_id=array("TECHNICIEN"=>$_SESSION['ID']);
	}
	mysqlinforezo();
	$sql='SELECT USE_ID, USE_LOGIN, USE_PRENOM FROM T_USER WHERE USE_VISIBLE=1 order by USE_PRENOM;';
	$query = mysql_query($sql) or die(sql_error('User', 'MakeCheckBoxUser', 1, $sql));
	$CHECKBOX_USERS='<label for ="Tout cocher">Tous </label> <input type="checkbox" value="Tout cocher" onClick="this.value=check(\'TECHNICIEN[]\')" id="Tout cocher"> <br>';
	while ( $list = mysql_fetch_array( $query ) )
	{
		$u="";
	 	if (in_array ($list['USE_ID'], $default_users_id)) $u="checked";
		$CHECKBOX_USERS.='<label for="'.$list['USE_LOGIN'].'">'.$list['USE_PRENOM'].'</label><input type="checkbox" name="TECHNICIEN[]" value="'.$list['USE_ID'].'" id="'.$list['USE_LOGIN'].'" '.$u.'><br>';
	}
	mysql_close();
	return $CHECKBOX_USERS;
}

function userIndex() {
  if ($_SESSION['rights'] == 0)
    return ("./content/viewtask.php");
  else
    return ("./content/veriflogin.php");
}

function isAllowed($content) {
  $allowedContent = array('viewtask', 
  'showtask', 
  'edit_task', 
  'update_task',
  'quitter'
  );
  if ($_SESSION['rights'] > 0)
    array_push($allowedContent, 
    'viewcustomer', 
    'showcustomer', 
    'a_savoir', 
    'update_a_savoir',
    'upload',
    'add_inter', 
    'new_intervention'
    );
  if ($_SESSION['rights'] > 1)
    array_push($allowedContent,
    'editcustomer',
    'updatecustomer', 
    'updatecontact', 
    'adnewcustomer',
    'print_contrat', 
    'view_devis', 
    'devis', 
    'change_selected_task',
    'print_selected_devis', 
    'change_selected_devis'
    );
  if ($_SESSION['rights'] > 2)
    array_push($allowedContent, 
    'view_factures', 
    'facture', 
    'make_factures_maintenance', 
    'print_selected_factures',
    'edit_task_header',
    'viewAnalyse',
    'makeFacMaintenance',
    'facSsMenu'
    );
  if ($_SESSION['rights'] > 3)
    array_push($allowedContent, 
    'admin',
    'FactureModification'
    );
  if (in_array($content, $allowedContent))
    return true;
  else
    return false;
}

?>

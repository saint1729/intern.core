<?php
error_reporting( ~ E_NOTICE & E_ALL );
require("class/crud.php");


$info = array(
    /**
     *  Show column => visible on read
     *  Insert hide => autoincrement value, the form doesn't display on create
     *  update read only => this value couldn't be update
     */
    'USE_ID' => array(CAPTION => 'ID', SHOWCOLUMN => true, INSERT_HIDE =>true, UPDATE_READ_ONLY => true),
    'USE_PRENOM' => array(CAPTION => 'Prénom', SHOWCOLUMN=>true ),
    'USE_NOM' => array(CAPTION => 'Nom', SHOWCOLUMN=>true ),
    'USE_LOGIN' => array(CAPTION => 'Login', SHOWCOLUMN=>true ),
    'USE_PORTABLE_PRO' => array(CAPTION => 'Portable pro', SHOWCOLUMN=>true ),
    'USE_PORTABLE_PERSO' => array(CAPTION => 'Portable perso', SHOWCOLUMN=>true ),
    'USE_EMAIL' => array(CAPTION => 'Email', SHOWCOLUMN=>true ),
    'country' => array(CAPTION => 'Contry', TABLE => "table_2", ID => "countryId", TEXT => "countryName", SHOWCOLUMN=>false),
    'USER_RIGHTS' => array(CAPTION => 'Droits', SHOWCOLUMN=>true,SELECT => range(0,4) ),
    'USE_VISIBLE' => array(CAPTION => 'Visible', SHOWCOLUMN=>true,SELECT => range(0,1) ),
    'USE_PWD' => array(CAPTION => 'Mot de passe', SHOWCOLUMN=>true, PASSWORD => true, UPDATE_READ_ONLY => false),

    EDIT_TEXT => "Modifier",
    DELETE_TEXT => "Supprimer",
    CHPASSWD_TEXT => "Changer le mot de passe",
    EDIT_LINK => "?contenu=admin&page=users&action=update&id=%USE_ID",
    DELETE_LINK => "?contenu=admin&page=users&action=delete&id=%USE_ID",
    CHPASSWD_LINK => "?contenu=admin&page=users&action=chpasswd&id=%USE_ID"
);
$crud = new crud( DB_STRING,"T_USER",$info);
?>
<h2>Utilisateurs</h2>
<h3><a href='?contenu=admin&page=users&action=new'>Nouvel utilisateur</a> | <a href='?contenu=admin&page=users'>Voir</a></h3>

<?php
switch ( $_GET['action'] ) {
    case 'new':
        if ( $crud->create() ) {
            echo "Nouvel utilisateur enregistré";
        }
        break;
    case 'delete':
        if ( $crud->delete(array('USE_ID' => $_GET['id'])) == true)
            echo "Utilisateur Supprimé";
        break;
    case 'update':
        if ( $crud->update(array('USE_ID' => $_GET['id']) ) == true)
            echo "Modifiation enregistrée";
        break;
    case 'chpasswd':
        if ( $crud->chpasswd(array('USE_ID' => $_GET['id']) ) == true)
            echo "Mot de pas changé";
        break;
    default:
        $crud->read();
        break;
}
?> 

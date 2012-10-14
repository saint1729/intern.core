<?php
error_reporting( ~ E_NOTICE & E_ALL );
require("class/crud.php");


$info = array(
    /**
     *  Show column => visible on read
     *  Insert hide => autoincrement value, the form doesn't display on create
     *  update read only => this value couldn't be update
     */
    'CLI_ID' => array(CAPTION => 'ID', SHOWCOLUMN => true, INSERT_HIDE =>true, UPDATE_READ_ONLY => true),
    'CLI_SOCIETE' => array(CAPTION => 'Client', SHOWCOLUMN=>true ),
    'CLI_TYPE' => array(CAPTION => 'Type', SHOWCOLUMN=>true ),
    'CLI_PWD' => array(CAPTION => 'Mot de passe', SHOWCOLUMN=>true, PASSWORD=>true ),

    EDIT_TEXT => "Modifier",
    DELETE_TEXT => "Supprimer",
    EDIT_LINK => "?contenu=admin&page=clients&action=update&id=%CLI_ID",
    DELETE_LINK => "?contenu=admin&page=clients&action=delete&id=%CLI_ID"
);
$crud = new crud(DB_STRING ,"T_CLIENT",$info);
?>
<h2>Clients</h2>
<h3><a href='?contenu=admin&page=clients'>Voir</a></h3>

<?php
switch ( $_GET['action'] ) {
    case 'delete':
        if ( $crud->delete(array('CLI_ID' => $_GET['id'])) == true)
            echo "Utilisateur Supprimé";
        break;
    case 'update':
        if ( $crud->update(array('CLI_ID' => $_GET['id']) ) == true)
            echo "Modifiation enregistrée";
        break;
    default:
        $crud->read();
        break;
}
?> 

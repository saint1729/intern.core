<?php
error_reporting( ~ E_NOTICE & E_ALL );
require("class/crud.php");


$info = array(
    /**
     *  Show column => visible on read
     *  Insert hide => autoincrement value, the form doesn't display on create
     *  update read only => this value couldn't be update
     */
    'TYP_ID' => array(CAPTION => 'ID', SHOWCOLUMN => true, INSERT_HIDE =>true, UPDATE_READ_ONLY => true),
    'TYP_TYPE' => array(CAPTION => 'Libelle', SHOWCOLUMN=>true ),
    'TYP_TVA_TAUX' => array(CAPTION => 'Taux TVA', SHOWCOLUMN=>true ),
    'TYP_TVA_COMPTE' => array(CAPTION => 'Compte TVA', SHOWCOLUMN=>true ),
    'TYP_COMPTE' => array(CAPTION => 'Compte', SHOWCOLUMN=>true ),
    'TYP_COEF' => array(CAPTION => 'Coeficient', SHOWCOLUMN=>true ),
    'TYP_PUHT' => array(CAPTION => 'Prix Unitaire', SHOWCOLUMN=>true ),

    EDIT_TEXT => "Modifier",
    DELETE_TEXT => "Supprimer",
    EDIT_LINK => "?contenu=admin&page=compta&action=update&id=%TYP_ID",
    DELETE_LINK => "?contenu=admin&page=compta&action=delete&id=%TYP_ID",
);
$crud = new crud(DB_STRING ,"T_TYPE",$info);
?>
<h2>Comptabilité</h2>
<h3><a href='?contenu=admin&page=compta&action=new'>Nouveau type</a> | <a href='?contenu=admin&page=compta'>Voir</a></h3>

<?php
switch ( $_GET['action'] ) {
    case 'new':
        if ( $crud->create() ) {
            echo "Nouveau Type enregistré";
        }
        break;
    case 'delete':
        if ( $crud->delete(array('TYP_ID' => $_GET['id'])) == true)
            echo "Type Supprimé";
        break;
    case 'update':
        if ( $crud->update(array('TYP_ID' => $_GET['id']) ) == true)
            echo "Modifiation enregistrée";
        break;
    default:
        $crud->read();
        break;
}
?> 

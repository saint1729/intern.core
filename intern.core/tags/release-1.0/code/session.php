<?php
include ("./lib/client.php");
include ("./lib/affectation.php");
include ("./lib/devis.php");
include ("./lib/facture.php");
session_start();
header("Content-type: text/html; charset=UTF-8");
/*
si la variable de session login n'existe pas cela siginifie que le visiteur
n'a pas de session ouverte, il n'est donc pas logué ni autorsé
acceder  l'espace membres
*/
if(!isset($_SESSION['login']) AND !isset($_SESSION['societe']))
{	
	include("header.php");
	include("title.php");
  echo '<p class="errlog">Vous n\'êtes pas autorisé à accéder à cette zone ceci est certainement du à l\'expiration de votre variable de session , merci de vous reidentifier</p>';
  include('./content/login.php');
  include('./foot.php');
  exit;
}
elseif(isset($_SESSION['login']))
{
	$login = $_SESSION['login'];
	mysqlinforezo();
	//on verifie qui est connecté
	$sql = "SELECT USE_ID, USE_LOGIN, USE_PWD from T_USER WHERE USE_LOGIN='$login'";
	$query = mysql_query($sql) or die( 'Erreur: impossible de vérifier qui est connecté' );
	$dt=mysql_fetch_array($query);
}
if(isset($_SESSION['societe']))
{
	$login = $_SESSION['societe'];
	mysqlinforezo();
	//on verifie qui est connecté
	$sql = "SELECT USE_ID, USE_LOGIN, USE_PWD from T_USER WHERE USE_LOGIN='$login'";
	$query = mysql_query($sql) or die( 'Erreur: impossible de vérifier qui est connecté' );
	$dt=mysql_fetch_array($query);
}
?> 

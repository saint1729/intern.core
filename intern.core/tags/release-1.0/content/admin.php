<?php
if ($_SESSION['rights'] < 4) {
  echo "Vous n'avez pas l'autorisation";
  exit;
}
$action = 'index';
$page = 'users';
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}
echo "<h1>Administration</h1>";
include "adm-menu.php";
include "admin/$page/index.php";
?>

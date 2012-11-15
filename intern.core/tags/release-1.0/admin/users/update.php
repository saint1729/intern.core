<?php
echo "modifiaction de l'utilisateur";
$user = new user($_GET['use_id']);
echo $user->user2form();

?>

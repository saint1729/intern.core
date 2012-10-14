<h2>Utilisateurs</h2>
<p>Cette page permet de supprimer les dernieres factures et de revenir au numéro de factures suivant la dernière factures conservée</p>
<p>Exemple: facture 131 suprimera toutes les factures après la facture 131 (c'est a dire de 132 à la dernière facture) et le numéro de la prochaine facture crée sera 132</p>
<p>Inutile de précisez qu'il faut être extremement vigilant lors de l'utilisation de cette page</p>
<?php
  if(isset($_POST['sure']))
    echo delete_fac_after($_POST['snofacture']);
  if ( isset($_POST['ok'])) {
    if (isset($_POST['nofacture']))
    {
      $facture = intval($_POST['nofacture']);
      echo "supprimer les facture apres la factures n° $facture";
      echo '
        <form method="post" action="#">
          <input type="hidden" value="'.$facture.'" name="snofacture">
          <input type="submit" value="Je suis sur" name="sure">
        </form>
      ';
    } else {
      echo "il faut choisir un n° de facture";
    }
  } else {
    echo '
<form method="post" action="#">
  <div>
    <label for="nofacture" color="red">Supprimer les Factures apres la n°: </label>
    <input name="nofacture" size="5" value="" maxlength="5" id="nofacture" type="text">
    <input type="submit" name="ok" value="Ok">
  </div>
</form>';
  }
?> 

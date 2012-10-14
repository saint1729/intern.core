liste des fichiers modele editable:
<ul>
  <li><a href="?contenu=admin&page=tpl&file=intro.tpl">intro.tpl</a>(introduction des contrats)</li>
  <li><a href="?contenu=admin&page=tpl&file=lettre_AR.tpl">lettre_AR.tpl</a>(modele de relance avec AR)</li>
  <li><a href="?contenu=admin&page=tpl&file=lettre_simple.tpl">lettre_simple.tpl</a>(modele de relance simple)</li>
  <li><a href="?contenu=admin&page=tpl&file=maintenance.tpl">maintenance.tpl</a></li>
  <li><a href="?contenu=admin&page=tpl&file=rezobackup.tpl">rezobackup.tpl</a></li>
  <li><a href="?contenu=admin&page=tpl&file=rezobox.tpl">rezobox.tpl</a></li>
  <li><a href="?contenu=admin&page=tpl&file=footer.tpl">footer.tpl</a> (texte affiché en bas des factures et des devis)</li>
</ul>

<?php
$file = $_GET['file'];
$content = ($_POST['edit']);
if ( isset($file) ) {
  if ( isset($content) ) {
    file_put_contents("tpl/$file",stripslashes($content));
    echo "modification enregistrée";
  } else {
    echo "<h2>$file</h2>";
    $content = file_get_contents("tpl/$file");
    echo '
      <form method="post" action="?contenu=admin&page=tpl&file='.$file.'">
        <textarea name="edit" rows="20" cols="80">'.$content.'</textarea>
        <input type="submit" value="OK"/>
      </form>
      '; 
  }
}
?>

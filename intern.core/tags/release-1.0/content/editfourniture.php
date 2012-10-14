<?php
$fou_id =$_POST['fou_id'];
$no_serie_id = $_POST['no_serie_id'];
$no_serie = $_POST['no_serie'];
	update_no_serie($no_serie_id , $no_serie);
	echo MakeFormFourniture($fou_id);
?> 

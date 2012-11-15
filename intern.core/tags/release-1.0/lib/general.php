<?php
	function set_int_date_intervention($date, $temps)
	{
		$bon_format = '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{1,4}$';
		if (ereg($bon_format, $date))
		{
			$table_date = split("/", $date);
			$jour = $table_date[0];
			$mois = $table_date[1];
			$anne = $table_date[2];

			if (checkdate($mois, $jour, $anne))
			{
				$bon_format = '^[0-9]{1,2}[hH:][0-9]{0,2}$';
				if (ereg($bon_format, $temps))
				{
					$table_temps = split("[h:]",$temps);
					$heure = $table_temps[0];
					$minute = $table_temps[1];
					if ($heure < 24 && $minute < 60)
					{
						$this->int_date_intervention=mktime($heure, $minute, 0, $mois, $jour, $anne);
						$result = 0;
					}
					else
					{
						$result .= "Erreur: heure non valide, le format est correct mais cette heure n'existe pas";
					}
				}
				else
				{
					$result .= "Erreur le format de l'heure n'est pas bon: hhhmm ou hh:mm ou hhHmm ou h:m ou h:mm";
				}
			}
			else
			{
				$result = "Ereur: date non valide, le format est bon mais ce jour n'existe pas";
			}
		}
		else
		{
			$result = "Erreur: le format de la date n'est pas bon: dd/mm/yyyy ou dd/mm/yyyy ou d/m/yy";
		}	
		return $result;
	}
?>

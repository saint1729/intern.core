<?php

  class type {
    private $id;
    private $type;
    private $tva_taux;
    private $tva_compte;
    private $compte;
    private $coef;
    private $puht;

    public function __construct($attributes) {
      $this->id = $attributes['TYP_ID'];
      $this->type = $attributes['TYP_TYPE'];
      $this->tva_taux = $attributes['TYP_TVA_TAUX'];
      $this->tva_compte= $attributes['TYP_TVA_COMPTE'];
      $this->compte = $attributes['TYP_COMPTE'];
      $this->coef = $attributes['TYP_COEF'];
      $this->puht = $attributes['TYP_PUHT'];
    }

    public function show() {
      echo '<br>id = '. $this->id;
      echo '<br>type = '. $this->type;
      echo '<br>tva_taux = '. $this->tva_taux;
      echo '<br>tva_compte = '. $this->tva_compte;
      echo '<br>compte = '. $this->compte;
      echo '<br>coef = '. $this->coef;
      echo '<br>puht = '. $this->puht;
    }

    public function javaChoixType() {
      $JAVA = '
					if (document.forms["edit_devis"].elements[type].value == '.$this->id.') {
						document.forms["edit_devis"].elements[paht].value = "'.$this->puht.'";
						document.forms["edit_devis"].elements[pvht].value = "'.$this->puht * $this->coef.'";
					}';
      return ($JAVA);
    }

    public function javaChoixPrixAchat() {
      $JAVA = '
					if (document.forms["edit_devis"].elements[type].value == '.$this->id.') {
						document.forms["edit_devis"].elements[pvht].value = document.forms["edit_devis"].elements[paht].value * '.$this->coef.';
          }
      ';
      return ($JAVA);
    }

  }

  class list_type {
    private $types;

    public function __construct() {
      $sql = "SELECT * FROM T_TYPE;";
      mysqlinforezo();
      $res = mysql_query($sql);
      while ($type = mysql_fetch_assoc($res)) {
        $this->types[] = new type($type);
      }
    }

    public function show() {
      foreach ($this->types as $type) {
        $type->show();
      }
    }

    public function javaChoixType() {
      $JAVA = '
				function ChoixType(no_ligne) {
					paht = "lde_prix_achat" + no_ligne;
					pvht = "lde_prix_vente" + no_ligne;
					type = "lde_type" + no_ligne;
          total = "lde_total" + no_ligne;
          qtt = "lde_qtt" + no_ligne;
      ';
      foreach ($this->types as $type) {
        $JAVA .= $type->javaChoixType();
      }
      $JAVA .= "\n".'document.forms["edit_devis"].elements[total].value = document.forms["edit_devis"].elements[pvht].value * document.forms["edit_devis"].elements[qtt].value;';
      $JAVA .= '}';
      return ($JAVA);
    }

    public function javaChoixPrixAchat() {
      $JAVA = '
        function ChoixPrixAchat(no_ligne) {
					paht = "lde_prix_achat" + no_ligne;
					pvht = "lde_prix_vente" + no_ligne;
					type = "lde_type" + no_ligne;
          total = "lde_total" + no_ligne;
          qtt = "lde_qtt" + no_ligne;
      ';
      foreach ($this->types as $type) {
        $JAVA .= $type->javaChoixPrixAchat();
      }
      $JAVA .= "\n".'document.forms["edit_devis"].elements[total].value = document.forms["edit_devis"].elements[pvht].value * document.forms["edit_devis"].elements[qtt].value;';
      $JAVA .= '}';
      return ($JAVA);
    }

  }

?>

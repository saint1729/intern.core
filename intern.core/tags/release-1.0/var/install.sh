#!/usr/bin/env bash

function createDb {
  dbName=$1
  dbUser=$2
  dbPwd=$3
  rootPwd=$4
  echo "Creation de la base de donnée $dbName"
  echo "noter la commande sur la ligne suivante qui permet de se connecter en direct a la base de donne en cas de besoin:"
  echo "mysql -u $dbUser -p$dbPwd $dbName"
  echo "create database $dbName;" | mysql -u root --password=$rootPwd
  echo "grant all on $dbName.* to $dbUser@localhost identified by '$dbPwd';" | mysql -u root --password=$rootPwd
  echo "grant all on $dbName.* to $dbUser@localhost identified by '$dbPwd';" | mysql -u root --password=$rootPwd
  echo "import de la stucture des tables"
  mysql -u $dbUser -p$dbPwd $dbName < struct.mysql
  echo "creation de l'administrateur: login: admin, mot de passe: admin"
  mysql -u $dbUser -p$dbPwd $dbName < firstUser.mysql
  echo "creation des premier types de lignes pour devis et factures: Titre, Option et Maintenance"
  mysql -u $dbUser -p$dbPwd $dbName < firstTypes.mysql
}

function createConfigFile {
  dbName=$1
  dbUser=$2
  dbPwd=$3
  mkdir ../config
  cp config/conf.inc.php ../config/
  sed -i 's/\[DB_USER\]/'$dbUser'/g' ../config/conf.inc.php
  sed -i 's/\[DB_PASSWORD\]/'$dbPwd'/g' ../config/conf.inc.php
  sed -i 's/\[DB_NAME\]/'$dbName'/g' ../config/conf.inc.php
}

function setPhp5 {
  echo "Nous devons passer root un bref instant pour la creation d'un lien symbolique, merci d' entrer le mot de passe:"
   sudo ln -s /etc/alternatives/php-cgi-bin ../../cgi-bin/php5
   cp .htaccess ../
}

function setCache {
  echo "autorisation d'écriture du cache"
  chmod -R 777 ../class/cache
  echo "autorisation d'écriture des fichiers temporaires"
  chmod 777 ../tmp
}

function copyImages {
  echo "copy des images depuis var/images vers images"
  mkdir ../images
  cp images/baniere.jpg  ../images 
  cp images/logo.gif     ../images
  cp images/logo.jpg     ../images
  mkdir ../images/interface  
  cp images/interface/droite_off.gif ../images/interface/
  cp images/interface/favicone2.ico  ../images/interface/
  cp images/interface/favicon.gif    ../images/interface/
  cp images/interface/fd_menu_actus_off.gif  ../images/interface/
  cp images/interface/fd_menu_forma_off.gif  ../images/interface/
  cp images/interface/fd_menu_horiz_vide.gif ../images/interface/
  cp images/interface/gauche_off.gif         ../images/interface/
  cp images/interface/picto_generez.gif      ../images/interface/
  cp images/interface/droite_on.gif          ../images/interface/
  cp images/interface/favicone.ico           ../images/interface/
  cp images/interface/favicon.ppm            ../images/interface/
  cp images/interface/fd_menu_adresse_off.gif ../images/interface/
  cp images/interface/fd_menu_horiz.gif      ../images/interface/
  cp images/interface/fd_top.gif             ../images/interface/
  cp images/interface/gauche_on.gif          ../images/interface/
  cp images/interface/picto_generez.jpg      ../images/interface/
}

function copyTpl {
  echo "Copie des Modele de var/tpl/ vers tpl/"
  mkdir ../tpl
  cp tpl/intro.tpl  ../tpl/
  cp tpl/lettre_AR.tpl ../tpl/
  cp tpl/lettre_simple.tpl ../tpl/
  cp tpl/maintenance.tpl  ../tpl/
  cp tpl/rezobackup.tpl ../tpl/
  cp tpl/rezobox.tpl  ../tpl/
  cp tpl/footer.tpl ../tpl/
  chmod o+w ../tpl/*
}

function setKey {
  user = $1;
  password = $2;
  database = $3;
  echo "Veuillez entrer le mot de passe pour crypter le champ a savoir"
  read key
  echo "INSERT INTO T_KEY (KEY_KEY) VALUES (AES_ENCRYPT('Pas d\'information pour le moment', '$key'););" | mysql -u $user --password=$password $database
  echo "la cle de cryptage est enregistree"
}


function main {
  echo "Bienvennue dans le script d'installation de ig"
  echo "Veuillez entrer le nom de la base de donnée à créer:"
  read dbName
  echo "Veuillez entrer le nom de l'utilisateur pour la base de donnée"
  read dbUser
  echo "Le mot de passe de l'utilisateur de la base de donnée sera généré automatiquement, vous pourrez le retrouver dans le fichier config/conf.inc.php"
  dbPwd=$(makepasswd)
  echo "voici le mot de passe qui à été généré: $dbPwd"
  echo "Veuiller entrer le mot de passe root de mysql pour que nous puissions créer la base de donnée"
  stty -echo
  read rootPwd
  stty echo
  createDb $dbName $dbUser $dbPwd $rootPwd
  createConfigFile $dbName $dbUser $dbPwd
  setCache
  copyImages
  copyTpl
  setKey $dbUser $dbPwd $dbName
  echo "Fin du script d'installation"
  setPhp5
}
main

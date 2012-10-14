<?php
/* DATABASE CONFIGURATION */
  define('DB_SERVER','localhost');
  define('DB_USER','[DB_USER]');
  define('DB_PASSWORD','[DB_PASSWORD]');
  define('DB_NAME','[DB_NAME]');

/* SITE CONFIGURATION */
  define('BASE_URL','[BASE_URL]');
  define('SOCIETE','[SOCIETE]');

/* TYPES CONFIGURATION */
  define('T_MAINTENANCE', 3);

/* DO NOT MODIFY BELOW THIS POINT*/
  define ('DB_STRING', "mysql://". DB_USER .":". DB_PASSWORD ."@". DB_SERVER ."/". DB_NAME );


  /*DEBUG CONFIGURATION */
  ini_set  ('xdebug.show_local_vars'  , 1  );  
  ini_set  ('error_reporting'  , E_ALL );
  ini_set  ('xdebug.dump.SERVER' , '*');
  ini_set  ('xdebug.dump.GET' , '*');
  ini_set  ('xdebug.dump.POST' , '*');
  ini_set  ('xdebug.dump.COOKIE' , '*');
  ini_set  ('xdebug.dump.FILES' , '*');
  ini_set  ('xdebug.dump.ENV' , '*');
  ini_set  ('xdebug.dump.SESSION' , '*');
  ini_set  ('xdebug.dump.REQUEST' , '*');
  set_time_limit(1);

?>

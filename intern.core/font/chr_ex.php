<?php

while( @$i++ < 32 )
    $a = chr( mt_rand(0, 255) );

file_put_contents( 'filename.txt', $a );

?>
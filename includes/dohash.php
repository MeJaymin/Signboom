<?php

  function doHash($val1,$val2){
    $key = "FlashJunkieAndKurtz1000000";

    return md5( $key.$val1.$val2 );
  }

  function doLinkHash($val){
    $key = "FlashJunkieAndKurtz1000000";

    return md5( $key.$val );
  }

  function doHashThreeVal($val1,$val2,$val3){
    $key = "FlashJunkieAndKurtz1000000";

    return md5( $key.$val1.$val2.$val3 );
  }
 
?>

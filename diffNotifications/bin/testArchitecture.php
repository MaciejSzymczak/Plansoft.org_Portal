<?php

function is_32bit(){
  return PHP_INT_SIZE === 4;
}

if ( is_32bit() ) {
    print "IS 32";
} else {
    print "IS 64";
}
?>

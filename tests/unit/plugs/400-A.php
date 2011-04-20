<?php

class A_Test
{

    function canProcess($arg) {
        return ($arg == 1);
    }

    function process($arg) {
        return get_class($this) . "_$arg";
    }

}

?>


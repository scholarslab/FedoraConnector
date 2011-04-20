<?php

class C_Test
{

    function canProcess($arg) {
        return ($arg == 3);
    }

    function process($arg) {
        return get_class($this) . "_$arg";
    }

}

?>


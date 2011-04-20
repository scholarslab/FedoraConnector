<?php

class B_Test
{

    function canProcess($arg) {
        return ($arg == 2);
    }

    function process($arg) {
        return get_class($this) . "_$arg";
    }

}

?>


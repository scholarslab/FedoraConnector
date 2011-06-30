<?php

class D_Test
{

    function canProcess($arg) {
        return ($arg == 4);
    }

    function process($arg) {
        return get_class($this) . "_$arg";
    }

}

?>

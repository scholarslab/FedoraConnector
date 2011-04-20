<?php

class Default_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/unknown');
    }

    function display($datastream) {
        return 'Default_Disseminator';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'Default_Disseminator Preview';
    }
}

?>

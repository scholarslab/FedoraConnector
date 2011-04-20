<?php

class TextPlain_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/plain');
    }

    function display($datastream) {
        return 'TextPlain_Disseminator';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'TextPlain_Disseminator Preview';
    }
}

?>

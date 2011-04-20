<?php

class Default_Renderer extends FedoraConnector_AbstractRenderer
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/unknown');
    }

    function display($datastream) {
        return 'Default_Renderer';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'Default_Renderer Preview';
    }
}

?>

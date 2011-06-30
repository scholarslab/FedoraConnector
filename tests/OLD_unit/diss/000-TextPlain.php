<?php

class TextPlain_Renderer extends FedoraConnector_AbstractRenderer
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/plain');
    }

    function display($datastream) {
        return 'TextPlain_Renderer';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'TextPlain_Renderer Preview';
    }
}

?>

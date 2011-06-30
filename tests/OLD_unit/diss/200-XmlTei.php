<?php

class XmlTei_Renderer extends FedoraConnector_AbstractRenderer
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/xml');
    }

    function display($datastream) {
        return 'XmlTei_Renderer';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'XmlTei_Renderer Preview';
    }
}

?>

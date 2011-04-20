<?php

class XmlTei_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'text/xml');
    }

    function display($datastream) {
        return 'XmlTei_Disseminator';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'XmlTei_Disseminator Preview';
    }
}

?>

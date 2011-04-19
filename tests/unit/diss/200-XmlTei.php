<?php

class XmlTei_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canHandle($mime, $datastream) {
        return ($mime == 'text/xml');
    }

    function handle($mime, $datastream) {
        return 'XmlTei_Disseminator';
    }

    function canPreview($mime, $datastream) {
        return XmlTei_Disseminator::canHandle($mime, $datastream);
    }

    function preview($mime, $datastream) {
        return 'XmlTei_Disseminator Preview';
    }
}

?>

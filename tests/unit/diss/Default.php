<?php

class Default_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canHandle($mime, $datastream) {
        return ($mime == 'text/unknown');
    }

    function handle($mime, $datastream) {
        return 'Default_Disseminator';
    }

    function canPreview($mime, $datastream) {
        return Default_Disseminator::canHandle($mime, $datastream);
    }

    function preview($mime, $datastream) {
        return 'Default_Disseminator Preview';
    }
}

?>

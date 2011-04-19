<?php

class TextPlain_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canHandle($mime, $datastream) {
        return ($mime == 'text/plain');
    }

    function handle($mime, $datastream) {
        return 'TextPlain_Disseminator';
    }

    function canPreview($mime, $datastream) {
        return TextPlain_Disseminator::canHandle($mime, $datastream);
    }

    function preview($mime, $datastream) {
        return 'TextPlain_Disseminator Preview';
    }
}

?>

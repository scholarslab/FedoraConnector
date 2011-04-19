<?php

class ImageGif_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canHandle($mime, $datastream) {
        return ($mime == 'image/gif');
    }

    function handle($mime, $datastream) {
        return 'ImageGif_Disseminator';
    }

    function canPreview($mime, $datastream) {
        return ImageGif_Disseminator::canHandle($mime, $datastream);
    }

    function preview($mime, $datastream) {
        return 'ImageGif_Disseminator Preview';
    }
}

?>

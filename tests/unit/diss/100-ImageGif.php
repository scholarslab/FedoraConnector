<?php

class ImageGif_Disseminator extends FedoraConnector_AbstractDisseminator
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'image/gif');
    }

    function display($datastream) {
        return 'ImageGif_Disseminator';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'ImageGif_Disseminator Preview';
    }
}

?>

<?php

class ImageGif_Renderer extends FedoraConnector_AbstractRenderer
{
    function canDisplay($datastream) {
        return ($datastream->mime_type == 'image/gif');
    }

    function display($datastream) {
        return 'ImageGif_Renderer';
    }

    function canPreview($datastream) {
        return $this->canDisplay($datastream);
    }

    function preview($datastream) {
        return 'ImageGif_Renderer Preview';
    }
}

?>

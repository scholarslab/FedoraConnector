<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_Render
{


    /**
     * Set renderer directory, instantiate plugins classes.
     *
     * @param string $rendererDir The directory containing the renderers.
     */
    public function __construct($rendererDir = null)
    {

        $this->rendererDir = isset($rendererDir) ?
            $rendererDir :
            FEDORA_DIR . '/renderers';

        $this->displayPlugins = new FedoraConnector_Plugins(
            $this->rendererDir,
            'Renderer',
            'canDisplay',
            'display'
        );

    }


    /**
     * Render a datastream.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @return string|null The output of the renderer.
     */
    public function display($object, $params = null) {

        foreach (explode(',', $object->dsids) as $dsid) {

            // Get mime type.
            $mimeType = $object->getServer()->getMimeType(
                $object->pid, $dsid
            );

            // Try to get renderer.
            $renderer = $this->displayPlugins->getPlugin($mimeType);

            // Render.
            if (!is_null($renderer)) {
                return $renderer->display($object, $params);
            }

        }

    }


}

<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Top-level render executor.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class FedoraConnector_Render
{

    /**
     * Set renderer directory, instantiate plugins classes.
     *
     * @param string $rendererDir The directory containing the
     * renderers. Defaults to FedoraConnector/renderers.
     *
     * @return void.
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
     *
     * @return string|null The output of the renderer.
     */
    public function display($object, $params = null) {

        // Walk dsids.
        foreach (explode(',', $object->dsids) as $dsid) {

            // Get mime type.
            $mimeType = $object->getServer()->getMimeType(
                $object->pid, $dsid);

            // Try to get renderer.
            $renderer = $this->displayPlugins->getPlugin($mimeType);

            // Render.
            if (!is_null($renderer)) {
                return $renderer->display($object, $params);
            }

        }

    }

}

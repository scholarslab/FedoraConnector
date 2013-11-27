<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_ServersController
    extends Omeka_Controller_AbstractActionController
{


    /**
     * Cache the servers table.
     */
    public function init()
    {
        $this->_servers = $this->_helper->db->getTable(
            'FedoraConnectorServer'
        );
    }


    /**
     * Show servers.
     */
    public function browseAction()
    {
        $this->view->servers = $this->_servers->findAll();
    }


    /**
     * Add server.
     */
    public function addAction()
    {

        // Create server and form.
        $server = new FedoraConnectorServer;
        $form = new FedoraConnector_Form_Server;

        // If a form as been posted.
        if ($this->_request->isPost()) {

            // Get post.
            $post = $this->_request->getPost();

            // If form is valid.
            if ($form->isValid($post)) {

                // Create server.
                $this->_servers->updateServer($server, $post);

                // Redirect to browse.
                $this->_helper->redirector('browse');

            }

            // If form is invalid.
            else {
                $form->populate($post);
            }

        }

        // Push form to view.
        $this->view->form = $form;

    }


    /**
     * Edit server.
     */
    public function editAction()
    {

        // Get server.
        $server = $this->_servers->find(
            $this->_request->id
        );

        // Get form.
        $form = new FedoraConnector_Form_Server;

        // Populate the form.
        $form->populate(array(
            'name' => $server->name,
            'url' => $server->url
        ));

        // If a form as been posted.
        if ($this->_request->isPost()) {

            // Get post.
            $post = $this->_request->getPost();

            // If form is valid.
            if ($form->isValid($post)) {

                // Create server.
                $this->_servers->updateServer($server, $post);

                // Redirect to browse.
                $this->_helper->redirector('browse');

            }

            // If form is invalid.
            else {
                $form->populate($post);
            }

        }

        // Push form to view.
        $this->view->form = $form;

    }


    /**
     * Sets the add success message.
     *
     * @param Omeka_Record $server The server.
     *
     * @return string The message.
     */
    protected function _getAddSuccessMessage($server)
    {
        return __('The server "%s" was successfully added!', $server->name);
    }


    /**
     * Sets the edit success message.
     *
     * @param Omeka_Record $server The server.
     *
     * @return string The message.
     */
    protected function _getEditSuccessMessage($server)
    {
        return __('The server "%s" was successfully changed!', $server->name);
    }


    /**
     * Sets the delete success message.
     *
     * @param Omeka_Record $server The server.
     *
     * @return string The message.
     */
    protected function _getDeleteSuccessMessage($server)
    {
        return __('The server "%s" was successfully deleted!', $server->name);
    }


    /**
     * Sets the delete confirm message.
     *
     * @param Omeka_Record $server The server.
     *
     * @return string The message.
     */
    protected function _getDeleteConfirmMessage($server)
    {
        return __('This will delete the server "%s".', $server->name);
    }


}

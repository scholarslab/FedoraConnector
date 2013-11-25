<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Servers controller.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_ServersController extends Omeka_Controller_AbstractActionController
{

    /**
     * Initialize.
     *
     * @return void
     */
    public function init()
    {
        $this->_table = $this->_helper->db->getTable('FedoraConnectorServer');
    }

    /**
     * Show servers.
     *
     * @return void
     */
    public function browseAction()
    {
        $this->view->servers = $this->_table->findAll();
    }

    /**
     * Add server.
     *
     * @return void
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
                $this->_table->updateServer($server, $post);

                // Redirect to browse.
                $this->redirect->goto('browse');

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
     *
     * @return void
     */
    public function editAction()
    {

        // Get server.
        $server = $this->_table->find(
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
                $this->_table->updateServer($server, $post);

                // Redirect to browse.
                $this->redirect->goto('browse');

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

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */


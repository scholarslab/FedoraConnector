<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Servers controller integration tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_ServersController extends Omeka_Controller_Action
{

    /**
     * Initialize.
     *
     * @return void
     */
    public function init()
    {
        $this->serversTable = $this->getTable('FedoraConnectorServer');
    }

    /**
     * Show servers.
     *
     * @return void
     */
    public function browseAction()
    {
        $this->view->servers = $this->serversTable->findAll();
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
                $this->serversTable->updateServer($server, $post);

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
        $server = $this->serversTable->find(
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
                $this->serversTable->updateServer($server, $post);

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
     * Add new server.
     *
     * @return void
     */
    public function insertAction()
    {

        // Get the data, instantiate validator.
        $data = $this->_request->getPost();
        $form = $this->_doServerForm();

        // Are all the fields filled out?
        if ($form->isValid($data)) {

            if (!$this->getTable('FedoraConnectorServer')->checkServerUrlFormat($data['url'])) {
                $this->flashError('Server URL must be of format "http://[host]/fedora/"');
                $this->_redirect('fedora-connector/servers/create');
            }

            else if ($this->getTable('FedoraConnectorServer')->checkServerNameUnique($data['name'])) {
                $this->flashError('A server already exists with that name.');
                $this->_redirect('fedora-connector/servers/create');
            }

            else {

                // Create server, process success.
                if ($this->getTable('FedoraConnectorServer')->createServer($data)) {
                    $this->flashSuccess('Server created.');
                    $this->redirect->goto('browse');
                } else {
                    $this->flashError('Error: The server was not created');
                    $this->redirect->goto('browse');
                }

            }

        }

        else {

            $this->flashError('Enter a name and URL.');
            $this->_redirect('fedora-connector/servers/create');

        }

    }

    /**
     * Confirm delete, do delete.
     *
     * @return void
     */
    public function deleteAction()
    {

        $id = $this->_request->id;
        $server = $this->getTable('FedoraConnectorServer')->find($id);
        $post = $this->_request->getPost();

        if (isset($post['deleteconfirm_submit'])) {

            if ($this->getTable('FedoraConnectorServer')->deleteServer($id)) {
                $this->flashSuccess('Server ' . $server->name . ' deleted');
                $this->redirect->goto('browse');
            } else {
                $this->flashError('Error: Server ' . $server->name . ' was not deleted');
                $this->redirect->goto('browse');
            }

        }

        $this->view->name = $server->name;

    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */


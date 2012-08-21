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
     * Show form to edit existing server.
     *
     * @return void
     */
    public function editAction()
    {

        $id = $this->_request->id;
        $server = $this->getTable('FedoraConnectorServer')->find($id);

        // Get the form.
        $form = $this->_doServerForm('edit', $id);

        // Fill it with the data.
        $form->populate(array(
            'name' => $server->name,
            'url' => $server->url,
            'is_default' => $server->is_default
        ));

        $this->view->form = $form;
        $this->view->server = $server;

    }

    /**
     * Process edit form - delete or save.
     *
     * @return void
     */
    public function updateAction()
    {

        // Get the data, instantiate validator.
        $data = $this->_request->getPost();
        $form = $this->_doServerForm();

        // If delete was hit, do the delete.
        if (isset($data['delete_submit'])) {
            $this->_redirect('fedora-connector/servers/delete/' . $data['id']);
            exit();
        }

        // Are all the fields filled out?
        if ($form->isValid($data)) {

            // If save was hit, do save.
            if (isset($data['edit_submit'])) {

                if (!$this->getTable('FedoraConnectorServer')->checkServerUrlFormat($data['url'])) {
                    $this->flashError('Server URL must be of format "http://[host]/fedora/"');
                    $this->_redirect('fedora-connector/servers/edit/' . $data['id']);
                    exit();
                }

                if ($this->getTable('FedoraConnectorServer')->saveServer($data)) {

                    $this->flashSuccess('Information for server ' . $data['name'] . ' saved');
                    $this->redirect->goto('browse');

                } else {

                    $this->flashError('Error: Information for server ' . $data['name'] . ' not saved');
                    $this->redirect->goto('browse');

                }

            }

        }

        else {

            $this->flashError('The server must have a name and a URL.');
            $this->_redirect('fedora-connector/servers/edit/' . $data['id']);

        }

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

    /**
     * Build the form for server add/edit.
     *
     * @param $mode 'create' or 'edit.'
     * @param $server_id The id of the server for hidden input in edit case.
     *
     * @return void
     */
    protected function _doServerForm($mode = 'create', $server_id = null)
    {

        $form = new Zend_Form();

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
            ->setLabel('Name:')
            ->setAttrib('size', 35);

        $url = new Zend_Form_Element_Text('url');
        $url->setRequired(true)
            ->setLabel('URL:')
            ->setAttrib('size', 35);

        $is_def = new Zend_Form_Element_Checkbox('is_default');
        $is_def->setLabel('Is this the default server?');

        $form->addElement($name);
        $form->addElement($url);
        $form->addElement($is_def);

        if ($mode == 'create') {

            $submit = new Zend_Form_Element_Submit('create_submit');
            $submit->setLabel('Create');

            $form->addElement($submit);
            $form->setAction('insert')->setMethod('post');

        }

        else if ($mode == 'edit') {

            $id = new Zend_Form_Element_Hidden('id');
            $id->setValue($server_id);

            $submit = new Zend_Form_Element_Submit('edit_submit');
            $submit->setLabel('Save');

            $delete = new Zend_Form_Element_Submit('delete_submit');
            $delete->setLabel('Delete');

            $form->addElement($id);
            $form->addElement($submit);
            $form->addElement($delete);
            $form->setAction('update')->setMethod('post');

        }

        return $form;

    }

    /**
     * This takes the uploaded form data and either creates or updates a 
     * server.
     *
     * @param Zend_Form $form The form to pull data from.
     *
     * @return Omeka_Record The new/updated server instance.
     */
    private function _updateServer($form) {
        // XXX some -> models/FedoraConnector/Server.php
        $data = $form->getValues();

        $version = $this->_getServerVersion($data['url']);
        if ($version !== null) {
            $data = array(
                'name'       => $data['name'],
                'url'        => $data['url'],
                'is_default' => $data['is_default'],
                'version'    => $version
            );
            if ($data['method'] == 'update') {
                $data['id'] = $data['id'];
            }

            try {
                $db = get_db();

                // If the new server is the default, clear is_default
                // for the existing ones.
                if ($data['is_default'] == '1') {
                    $this->_resetIsDefault();
                }

                $db->insert('fedora_connector_servers', $data);

                $this->flashSuccess('Server updated.');
                $this->redirect->goto('index');

            } catch (Exception $e) {
                $this->flashError($e->getMessage());
            }

        } else {
            $this->flashError(
                'Server URL cannot be validated.  '
                . 'Not receiving Fedora repositoryVersion response.'
            );
            $this->view->form = $form;
        }
    }

    /**
     * This queries the Fedora Commons URL and returns the repository version.
     *
     * @param string $url The server's base URL.
     *
     * @return string The repository's server string, or null if none is found.
     */
    private function _getServerVersion($url) {
        // XXX -> models/FedoraConnector/Server.php
        $nodes = getQueryNodes(
            "{$url}describe?xml=true",
            "//*[local-name() = 'repositoryVersion']"
        );

        $version = null;
        foreach ($nodes as $node) {
            $version = $node->nodeValue;
        }

        return $version;
    }

    /**
     * This resets the is_default values for all servers.
     *
     * @param Omeka_Db $db The database.
     *
     * @return void
     */
    private function _resetIsDefault($db) {
        // XXX -> models/FedoraConnector/ServerTable.php
        $db
            ->getTable('FedoraConnector_Server')
            ->update(
                array('is_default' => '0'),
                "is_default = '1'"
            );
    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */


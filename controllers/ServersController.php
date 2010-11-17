<?php

/**
 * @copyright  Scholars' Lab 2010
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package VraCoreElements
 * @author Ethan Gruber: ewg4x at virginia dot edu
 */

//Require Zend Form Elements
require "Zend/Form/Element.php";

class FedoraConnector_ServersController extends Omeka_Controller_Action
{  
    /*public function createAction(){
    	$agent = array();
    	$form = $this->agentForm($agent);
		$this->view->form = $form;
    }*/
    
	public function indexAction(){
		$db = get_db();
    	
    	$currentPage = $this->_getParam('page', 1);
    	
    	
    	$count = $db->getTable('FedoraConnector_Server')->count();
    	$this->view->servers =  FedoraConnector_Server::getServer($currentPage);
    	$this->view->count = $count;
		
        /** 
         * Now process the pagination
         * 
         **/
        $paginationUrl = $this->getRequest()->getBaseUrl().'/servers/index/';

        //Serve up the pagination
        $pagination = array('page'          => $currentPage, 
                            'per_page'      => 10, 
                            'total_results' => $count, 
                            'link'          => $paginationUrl);
        
        Zend_Registry::set('pagination', $pagination);
    }
    
    public function createAction(){
    	$server = array();
    	$form = $this->serverForm($server);
		$this->view->form = $form;
    }
    
	public function editAction(){
		$db = get_db();
		$serverId = $this->_getParam('id');
		$count = count($db->getTable('FedoraConnector_Server')->findAll());
		$server = $db->getTable('FedoraConnector_Server')->find($serverId);
		$form = $this->serverForm($server);
		$this->view->count = $count;
		$this->view->id = $serverId;
		$this->view->form = $form;
    }
    
    public function deleteAction()
    {
        if ($user = $this->getCurrentUser()) {
			$serverId = $this->_getParam('id');
			$db = get_db();
            $server = $db->getTable('FedoraConnector_Server')->find($serverId);
            //$entries = $db->getTable('ElementText')->findBySql('text = ?', array($serverId));
            
			$server->delete();
			/*foreach ($entries as $entry){
                $entry->delete();
               }*/
			$this->flashSuccess('The server was successfully deleted!');
			$this->redirect->goto('index'); 
        }
        
        $this->_forward('forbidden');
    }
    
	public function updateAction(){
		$form = $this->serverForm($server);
		
    	if ($_POST) {
    		if ($form->isValid($this->_request->getPost())) {    
    			//get posted values		
				$uploadedData = $form->getValues();
				if ($uploadedData['method'] == 'create'){
					$data = array('name'=>$uploadedData['name'],
						'url'=>$uploadedData['url'],
						'is_default'=>$uploadedData['is_default']);
				} elseif ($uploadedData['method'] == 'update'){
					$data = array('id'=>$uploadedData['id'],
						'name'=>$uploadedData['name'],
						'url'=>$uploadedData['url'],
						'is_default'=>$uploadedData['is_default']);
				}
				
				//get db
				try{		
					$db = get_db();
					
					//if is_default is set to true, iterate through existing rows in the table to set is_default to 0 for all other servers
					if ($uploadedData['is_default'] == '1'){
						$servers = $db->getTable('FedoraConnector_Server')->findBySql('is_default = ?', array('1'));
						foreach ($servers as $server){
							$db->insert('fedora_connector_servers', array('name'=>$server->name, 'url'=>$server->url, 'id'=>$server->id, 'is_default'=>'0'));
						}
					}
					$db->insert('fedora_connector_servers', $data);		
					$this->flashSuccess('Server updated.');				
					$this->redirect->goto('index');							
					
				} catch (Exception $e) {
					$this->flashError($e->getMessage());
        		}
    		}
    		else {
    			$this->flashError('URL and server name are required.');
    			$this->view->form = $form;
    		}
    		
    	}
    	else {
    			$this->flashError('Failed to gather posted data.');
    			$this->view->form = $form;
    	}
    }

    private function serverForm($server){
	    $form = new Zend_Form();  	
	    $form->setMethod('post');
	    $form->setAction('update');
	    $form->setAttrib('enctype', 'multipart/form-data');	
	    
	    $url = new Zend_Form_Element_Text('url');
		$url->setLabel('URL:');
		$url->setRequired(true);
		if ($server != NULL){
			$url->setValue($server->url);
		}
		$form->addElement($url);
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name:');
		$name->setRequired(true);
		if ($server != NULL){
			$name->setValue($server->name);
		}
		$form->addElement($name);
		
				
		$isDefault = new Zend_Form_Element_Checkbox('is_default');
		$isDefault->setLabel('Is Default Server:');
		if ($server != NULL){
			$isDefault->setValue($server->is_default);
		}
		$form->addElement($isDefault);
		
		$id = new Zend_Form_Element_Hidden('id');		
		if ($server != NULL){
			$id->setValue($server->id);
		}
		$form->addElement($id);
		
		 //define method	   
    	$method = new Zend_Form_Element_Hidden('method');
		if ($server != NULL){
    		$method->setValue('update');
    	 } else {
    	 	$method->setValue('create');
    	 }
    	$form->addElement($method);
	    
		//Submit button
	    $form->addElement('submit','submit');
	    $submitElement=$form->getElement('submit');
	    $submitElement->setLabel('Submit');    
		
	    return $form;
    }
}


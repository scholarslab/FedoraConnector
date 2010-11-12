<?php

/**
 * @version $Id$
 * @copyright UVaLib DRS R & D, 2009
 * @package Fedora
 **/

define('FEDORA_CONNECTOR_PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
define('FEDORA_CONNECTOR_PLUGIN_DIR', dirname(__FILE__));

//define('FEDORA_REPO_ROOT', 'http://localhost:8080/fedora');

//hooks
add_plugin_hook('install', 'fedora_connector_install');
add_plugin_hook('uninstall', 'fedora_connector_uninstall');
add_plugin_hook('admin_theme_header', 'fedora_connector_admin_header');
add_plugin_hook('config_form', 'fedora_connector_config_form');
add_plugin_hook('config', 'fedora_connector_config');
//add_plugin_hook('before_save_item','fedora_connector_item_to_object');
//add_plugin_hook('define_routes', 'fedora_connector_routes');

//filters
add_filter('admin_items_form_tabs', 'fedora_connector_item_form_tabs');


function fedora_connector_install()  {
	$db = get_db();
	$db->exec("CREATE TABLE IF NOT EXISTS `{$db->prefix}fedora_connector_datastreams` (
				`id` int(10) unsigned NOT NULL auto_increment,
				`item_id` int(10) unsigned,
				`pid` tinytext collate utf8_unicode_ci,
				`datastream` tinytext collate utf8_unicode_ci,
				`mime_type` tinytext collate utf8_unicode_ci,	          
		       PRIMARY KEY  (`id`)
		       ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

	set_option('fedora_connector_server', 'http://localhost:8080/fedora/');
}

function fedora_connector_uninstall() {
	$db = get_db();
	$sql = "DROP TABLE IF EXISTS `{$db->prefix}fedora_connector_datastreams`";
	$db->query($sql);
	
	//delete option
	delete_option('fedora_connector_server');
}

function fedora_connector_admin_header($request)
{
	if ($request->getModuleName() == 'fedora-connector') {
		echo '<link rel="stylesheet" href="' . html_escape(css('fedora_connector_main')) . '" />';
    }
}

/**
 * Add Fedora Datastreams tab to Edit Items form page * 
 */

function fedora_connector_item_form_tabs($tabs)
{
   // insert the map tab before the Miscellaneous tab
   $item = get_current_item();
   $ttabs = array();
   foreach($tabs as $key => $html) {
       if ($key == 'Tags') {
           $ttabs['Fedora Datastreams'] = fedora_connector_pid_form($item);
       }
       $ttabs[$key] = $html;
   }
   $tabs = $ttabs;
   return $tabs;
}

function fedora_connector_config_form()
{  
    $server = get_option('fedora_connector_server');
     
?>
    <div class="field">
        <label for="fedora_connector_server">Feodra Server</label>
        <?php echo __v()->formText('fedora_connector_server', $server, null);?>
        <p class="explanation">URL to the Fedora server.  The path should include the name of the Tomcat application (usually 'fedora') and trailing forward slash.  Example: http://yourfedoraserver.com:8080/fedora/</p>
    </div>
<?php
}

function fedora_connector_config()
{
    set_option('fedora_connector_server', $_POST['fedora_connector_server']);
}

//Helpers

function fedora_connector_pid_form($item) {
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item->id));

	ob_start();
	$ht .= ob_get_contents();
    ob_end_clean();
	
	$ht .= '<div id="omeka-map-form">';
	//if there are datastreams, display the table
	if ($datastreams[0]->pid != NULL){
		$ht .= '<table><thead><th>PID</th><th>Datastream ID</th><th>mime-type</th><th>Delete?</th></thead>';
		foreach ($datastreams as $datastream){
			$delete_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/delete/';
			$add_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
			$ht.= '<tr><td>' . $datastream->pid . '</td><td>' . link_to_fedora_datastream($datastream->pid, $datastream->datastream) . '</td><td>' . $datastream->mime_type . '</td><td><a href="' . $delete_url . '?id=' . $datastream->id . '&item_id=' . $item->id . '">Delete</a></td></tr>';
		}
		$ht .= '</table>';
		$ht .= '<p><a href="' . $add_url . '?id=' . $item->id . '">Add another</a>?</p>';
		//$ht .= render_fedora_datastream('holsinger:1', 'JP2K', array('size'=>'screen'));
	} else {
		//otherwise link to add a new datastream
		$add_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
		$ht .= '<p>There are no Fedora datastreams associated with this item.  Why don\'t you <a href="' . $add_url . '?id=' . $item->id . '">add one</a>?</p>';
	}	
	$ht .= '</div>';
    return $ht;
}
/****
 * render_fedora_datastreams_for_item
 * renders all datastreams in the item view
 ****/
function render_fedora_datastreams_for_item ($item_id, $options=array()){
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item_id));
	$html = '';
	foreach($datastreams as $datastream){
		$html .= render_fedora_datastream($datastream->pid, $datastream->datastream, $options);
	}
	return $html;
}

function link_to_fedora_datastream($pid, $datastreamId){
	$html = '';
	$server = get_option('fedora_connector_server');	
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('pid = ? AND datastream = ?', array($pid, $datastreamId));	
	foreach ($datastreams as $datastream){
		$url = $server . 'objects/' . $datastream->pid . '/datastreams/' . $datastream->datastream . '/content';
		$html .= '<a href="' . $url . '" target="_blank">' . $datastream->datastream . '</a>';
	}
	return $html;
}

/****
 * render_fedora_datastream
 * accepts fedora PID and datastream ID.
 * Switch cases, depending on mime_type.  Datastream IDs are arbitrary so
 * mime-type disseminators need to be extensible. 
 ****/
function render_fedora_datastream ($pid, $datastreamId, $options=array()){
	$html = '';
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('pid = ? AND datastream = ?', array($pid, $datastreamId));
	foreach ($datastreams as $datastream){
		$mime_type = $datastream->mime_type;
		
		//switch based on mime-types
		switch($mime_type){
			case 'image/jp2':
				$html .= fedora_disseminator_imagejp2($datastream,$options);
				break;
			case 'image/jpeg':
				$html .= fedora_disseminator_imagejpeg($datastream,$options);
				break;
		}
	}
	return $html;
}

/****
 * DISSEMINATORS 
 ****/

//image/jpeg
function fedora_disseminator_imagejpeg($datastream,$options){
	$server = get_option('fedora_connector_server');
	$url = $server . 'objects/' . $datastream->pid . '/datastreams/' . $datastream->datastream . '/content';
}

//JP2K = image/jp2
function fedora_disseminator_imagejp2 ($datastream,$options){
	$server = get_option('fedora_connector_server');
	$size = $options['size'];
	$url = $server . 'get/' . $datastream->pid . '/djatoka:jp2SDef/getRegion';
	switch($size){
			case 'thumb':
				$html = '<img alt="image" src="' . $url . '?scale=120,120"/>';
				break;
			case 'screen':
				$html = '<img alt="image" src="' . $url . '?scale=600,600"/>';
				break;
			default:
				$html = '<img alt="image" src="' . $url . '?scale=400,400"/>';
		}	
	return $html;
}

/**
 * Add the routes from routes.ini in this plugin folder.
 *
 * @return void
 **/
/*function fedora_connector_routes($router) {
	$router->addConfig(new Zend_Config_Ini(FEDORA_CONNECTOR_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}*/

/*function fedora_connector_item_to_object($item) {
	$logger = Omeka_Context::getInstance()->getLogger();
	
	if ( $myitem->getItemType()->name != 'Fedora object') return;
	$identifiers = $myitem->getElementTextsByElementNameAndSetName( 'Fedora PID', 'Item Type Metadata');
	$pid = $identifiers[0]->text;
	$members = getMembers($pid);
	$logger->log($members);
}*/
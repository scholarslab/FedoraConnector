<?php

/**
 * @copyright  Scholars' Lab 2010
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package FedoraConnector
 * @author Ethan Gruber: ewg4x at virginia dot edu
 **/
//include the disseminators and importers which are stored in separate files
require "Disseminators.php";
require "Importers.php";

define('FEDORA_CONNECTOR_PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
define('FEDORA_CONNECTOR_PLUGIN_DIR', dirname(__FILE__));

//hooks
add_plugin_hook('install', 'fedora_connector_install');
add_plugin_hook('uninstall', 'fedora_connector_uninstall');
add_plugin_hook('before_delete_item', 'fedora_connector_before_delete_item');
add_plugin_hook('admin_theme_header', 'fedora_connector_admin_header');
add_plugin_hook('define_acl', 'fedora_connector_define_acl');
add_plugin_hook('config_form', 'fedora_connector_config_form');
add_plugin_hook('config', 'fedora_connector_config');
//add_plugin_hook('before_save_item','fedora_connector_item_to_object');
//add_plugin_hook('define_routes', 'fedora_connector_routes');

//filters
add_filter('admin_items_form_tabs', 'fedora_connector_item_form_tabs');
add_filter('admin_navigation_main', 'fedora_connector_admin_navigation');

function fedora_connector_install()  {
	$db = get_db();
	$db->exec("CREATE TABLE IF NOT EXISTS `{$db->prefix}fedora_connector_datastreams` (
				`id` int(10) unsigned NOT NULL auto_increment,
				`item_id` int(10) unsigned,
				`server_id` int(10) unsigned,
				`pid` tinytext collate utf8_unicode_ci,
				`datastream` tinytext collate utf8_unicode_ci,
				`mime_type` tinytext collate utf8_unicode_ci,
				`metadata_stream` tinytext collate utf8_unicode_ci,
		       PRIMARY KEY  (`id`)
		       ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
	
	$db->exec("CREATE TABLE IF NOT EXISTS `{$db->prefix}fedora_connector_servers` (
				`id` int(10) unsigned NOT NULL auto_increment,
				`url` tinytext collate utf8_unicode_ci,
				`name` tinytext collate utf8_unicode_ci,
				`version` tinytext collate utf8_unicode_ci,
				`is_default` tinyint(1) unsigned NOT NULL,
		       PRIMARY KEY  (`id`)
		       ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

	$db->insert('fedora_connector_servers', array('url'=>'http://localhost:8080/fedora/', 'name'=>'Default Fedora Server', 'is_default'=>1));
	set_option('fedora_connector_omitted_datastreams', 'RELS-EXT,RELS-INT,AUDIT');
}

function fedora_connector_uninstall() {
	$db = get_db();
	$datastreams = "DROP TABLE IF EXISTS `{$db->prefix}fedora_connector_datastreams`";
	$servers = "DROP TABLE IF EXISTS `{$db->prefix}fedora_connector_servers`";
	$db->query($datastreams);
	$db->query($servers);
	
	//if TeiDisplay is installed, remove Fedora TEI datastreams from its table
	if (function_exists('tei_display_installed')){
		$teiFiles = $db->getTable('TeiDisplay_Config')->findBySql('is_fedora_datastream = ?', array(1));
		foreach ($teiFiles as $teiFile){
				$teiFile->delete();
		}
	}
}

//delete all datastreams associated with item on item deletion
function fedora_connector_before_delete_item($item)
{
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item['id']));
	foreach ($datastreams as $datastream){
		$datastream->delete();
	}
}

function fedora_connector_admin_header($request)
{
	if ($request->getModuleName() == 'fedora-connector') {
		echo '<link rel="stylesheet" href="' . html_escape(css('fedora_connector_main')) . '" />';
    }
}

function fedora_connector_define_acl($acl)
{
    $acl->loadResourceList(array('FedoraConnector_Server' => array('index', 'status')));
     $acl->loadResourceList(array('FedoraConnector_Datastream' => array('index', 'status')));
}

function fedora_connector_admin_navigation($tabs)
{
    if (get_acl()->checkUserPermission('FedoraConnector_Server', 'index')) {
        $tabs['Fedora Servers'] = uri('fedora-connector/servers/');        
    }
    if (get_acl()->checkUserPermission('FedoraConnector_Datastream', 'browse')) {
        $tabs['Fedora Objects'] = uri('fedora-connector/datastreams/browse/');        
    }
    return $tabs;
}

function fedora_connector_config_form()
{      
    $omittedDatastreams = get_option('fedora_connector_omitted_datastreams');
?>
	<div class="field">
		<label for="fedora_connector_server">Omitted Datastreams:</label>   
		 <?php echo __v()->formText('fedora_connector_omitted_datastreams', $omittedDatastreams, null);?>    
		<p class="explanation">List datastream IDs, comma-separated, that should be omitted from the datastream selection checkbox list and object metadata dropdown menu.  Default: RELS-EXT,RELS-INT,AUDIT.</p>
	</div>
<?php
}

function fedora_connector_config()
{
	set_option('fedora_connector_omitted_datastreams', $_POST['fedora_connector_omitted_datastreams']);
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

function fedora_connector_pid_form($item) {
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item->id));

	ob_start();
	$ht .= ob_get_contents();
    ob_end_clean();
	
	$ht .= '<div id="omeka-map-form">';
	//if there are datastreams, display the table
	if ($datastreams[0]->pid != NULL){
		$ht .= '<table><thead><th>ID</th><th>PID</th><th>Datastream ID</th><th>mime-type</th><th>Object Metadata</th><th>Preview</th><th>Delete?</th></thead>';
		foreach ($datastreams as $datastream){
			$delete_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/delete/';		
			$add_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
			$ht.= '<tr><td>' . $datastream->id . '</td><td>' . $datastream->pid . '</td><td>' . link_to_fedora_datastream($datastream->id) . '</td><td>' . $datastream->mime_type . '</td><td>' . $datastream->metadata_stream . ' ' . fedora_connector_importer_link($datastream) . '</td><td>' . (strstr($datastream['mime_type'], 'image/') ? render_fedora_datastream_preview($datastream) : '') . '</td><td><a href="' . $delete_url . '?id=' . $datastream->id . '">Delete</a></td></tr>';
		}
		$ht .= '</table>';
		$ht .= '<p><a href="' . $add_url . '?id=' . $item->id . '">Add another</a>?</p>';
	} else {
		//otherwise link to add a new datastream
		$add_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
		$ht .= '<p>There are no Fedora datastreams associated with this item.  Why don\'t you <a href="' . $add_url . '?id=' . $item->id . '">add one</a>?</p>';
	}	
	$ht .= '</div>';
    return $ht;
}

/*****************************
 * HELPERS
 *****************************/
function fedora_connector_installed(){
	return 'active';
}
/****
 * Link to a fedora datastream.  Commonly used on Edit Item page under Fedora Datastreams tab.
 ****/
function link_to_fedora_datastream($id){
	$html = '';
	$db = get_db();
	$datastream = $db->getTable('FedoraConnector_Datastream')->find($id);
	$url = fedora_connector_content_url($datastream);
	$html .= '<a href="' . $url . '" target="_blank">' . $datastream->datastream . '</a>';
	return $html;
}

/***
 * List Fedora datastreams: used on Admin show item page 
 ****/
function list_fedora_datastreams($item){
	$db = get_db();
	$datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item->id));
	$html = '';
	if ($datastreams[0]->pid != NULL){
		foreach ($datastreams as $datastream){
			$html .= '<h4>PID: ' . $datastream->pid . '</h4>';
			$html .= '<ul>';			
			$html .= '<li>Datastream: ' . link_to_fedora_datastream($datastream->id) . '</li>';
			$html .= '<li>Metadata: ' . $datastream->metadata_stream . '</li>';
			$html .= '</ul';
		}
	} else {
		$html .= '<p>There are no datastreams for this item yet. ' . link_to_item('Add a Datastream', array(), 'edit') . '.</p>';
	}
	return $html;
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
		$html .= render_fedora_datastream($datastream->id, $options);
	}
	return $html;
}

/****
 * Get the URL of the server by passing in the server_id from the $datastream
 ****/
function fedora_connector_get_server($datastream){
	$db = get_db();
	$server = $db->getTable('FedoraConnector_Server')->find($datastream->server_id)->url;
	return $server;
}
/****
 * Get the URL of the datastream by passing in the server_id from the $datastream.
 * The script will query the Fedora servers table and return the version of the repository
 * in which the Object is located.  The version determines the service.
 * Fedora 2.x -- get, Fedora 3.x -- objects  
 ****/
function fedora_connector_content_url($datastream){
	$db = get_db();
	$server = $db->getTable('FedoraConnector_Server')->find($datastream->server_id);
	$serverUrl = $server->url;
	
	switch ($server->version){
		case preg_match('/^2\./'):
			$service = 'get';
			break;
		default:
			$service = 'objects';
			break; 
	}
	
	$url = $serverUrl . $service . '/' . $datastream->pid . '/datastreams/' . $datastream->datastream . '/content';
	return $url;
}

/****
 * Get the URL of the object metadata datastream by passing in the server_id from the $datastream.
 * The script will query the Fedora servers table and return the version of the repository
 * in which the Object is located.  The version determines the service.
 * Fedora 2.x -- get, Fedora 3.x -- objects  
 ****/
function fedora_connector_metadata_url($datastream){
	$db = get_db();
	$server = $db->getTable('FedoraConnector_Server')->find($datastream->server_id);
	$serverUrl = $server->url;
	
	switch ($server->version){
		case preg_match('/^2\./'):
			$service = 'get';
			break;
		default:
			$service = 'objects';
			break; 
	}
	
	$url = $serverUrl . $service . '/' . $datastream->pid . '/datastreams/' . $datastream->metadata_stream . '/content';
	return $url;
}
/****
 * Generate the link to import metadata for the object only if the XML metadata has an importer function associated with it
 ****/
function fedora_connector_importer_link($datastream){
	$import_url = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/import/';
	$importers = fedora_connector_list_importers();
	if (in_array($datastream->metadata_stream, $importers)){
		$html = '[<a href="' . $import_url . '?id=' . $datastream->id . '">import</a>]';
	}	
	return $html;
}
function render_fedora_datastream_preview($datastream){
	$db = get_db();
	$mime_type = $datastream->mime_type;
	//render images only
	if (strstr($mime_type, 'image/')){
		switch($mime_type){
			case 'image/jp2':
				$html = fedora_disseminator_imagejp2($datastream,array('size'=>'thumb'));
				break;
			default:
				$url = fedora_connector_content_url($datastream);
				$html = '<img alt="image" src="' . $url . '" class="fedora-preview"/>';
		}
	}
	
	return $html;
}
/*************************
 * DISSEMINATOR AND IMPORTER INITIATORS
 * disseminator and importer functions contained in separate PHP files
 *************************/
/****
 * render_fedora_datastream
 * accepts ID and options in an array
 * Switch cases, depending on mime_type.  Datastream IDs are arbitrary so
 * mime-type disseminators need to be extensible. 
 ****/
function render_fedora_datastream ($id, $options=array()){
	$html = '';
	$db = get_db();
	$datastream = $db->getTable('FedoraConnector_Datastream')->find($id);	
	$mime_type = $datastream->mime_type;
	
	//switch based on mime-types
	switch($mime_type){
		case 'image/jp2':
			$html .= fedora_disseminator_imagejp2($datastream,$options);
			break;
		case 'image/jpeg':
			$html .= fedora_disseminator_imagejpeg($datastream,$options);
			break;
		// TEI XML: change datastream string from TEI to another id, if applicable
		case strstr($mime_type, 'text/xml'):	
			if ($datastream->datastream == 'TEI' && function_exists('tei_display_installed')){
				$html .= fedora_disseminator_tei($datastream,$options);
			} else {
				$html .= '<b>There is no FedoraConnector disseminator for this mime-type</b>';
			}
			break;
		default:
			$html .= '<b>There is no FedoraConnector disseminator for this mime-type</b>';
	}
	return $html;
}
/****
 * List available Importers
 * This loads Importers.php into a string and extracts all fedora_importer_DATASTREAM functions to
 * dynamically generate a list of metadata datastreams that have had importation handlers written
 ****/
function fedora_connector_list_importers(){
	$pathToFile = FEDORA_CONNECTOR_PLUGIN_DIR . DIRECTORY_SEPARATOR . "Importers.php";	
	$string = file_get_contents($pathToFile);

	preg_match_all('/fedora_importer_([A-Z]+)/', $string, $matches);
	$importers = array();
	
	foreach ($matches[1] as $match){
		$importers[] = $match;
	}
	return $importers;
}
/****
 * Initialize importation based on metadata_stream
 ****/
function fedora_connector_import_metadata($datastream){
	$importerFunction = 'fedora_importer_' . $datastream->metadata_stream;
	$importerFunction ($datastream);	
    return;    
}
# Fedora Connector

[![Build Status](https://secure.travis-ci.org/scholarslab/FedoraConnector.png)](http://travis-ci.org/scholarslab/FedoraConnector)

FedoraConnector makes it possible to connect an Omeka site with [Fedora Commons] repositories. The plugin allows you to link Omeka
items with "datastreams" on the Fedora repository and
automatically populate the Dublin Core fields for the Omeka item with
the values defined by the datastream.

The plugin introduces two basic taxonomies - Servers and Datastreams. A
server corresponds to a Fedora repository, while a datastream represents
an individual bundle of metadata contained within a specific object in
the repository.

The basic workflow for importing Fedora data into Omeka is as follows:

- <strong>Create server records for Fedora Commons repositories</strong>: Set the name and base
  URL for the external Fedora Commons repository;

- <strong>Link Fedora datastreams to an Omeka item</strong>: After
  choosing the
  item that you want to associate the new datastreams with, enter the
PID of the object on the Fedora repository and select one or more of the
component datastreams;

- <strong>Import datastreams</strong>: The plugin fetches the data from Fedora and populates the
  Dublin Core fields for the item with the information from the
datastream.

[Fedora Commons]: http://www.fedora-commons.org/

## Requirements

The plugin assumes that your Fedora Commons repository is active and
accessible, either on localhost (the default) or the web. Currently,
FedoraConnector can only interact with Fedora servers that do not place
authorization checks on remote requests to access object datastreams.
Refer to the [Fedora Commons documentation] on installing, configuring,
and running a digital repository.

[Fedora Commons documentation]: https://wiki.duraspace.org/display/FCR30/Fedora+Repository+3.4.2+Documentation

## Installing and Configuring

1. Clone the FedoraConnector folder into the "plugins" folder of the root
Omeka installation. (see [Installing a Plugin])

2. In the Omeka administrative interface, click on the orange "Settings"
  button at the top right of the screen, go to the "Plugins" tab, and
click the "Install" button next to the listing for Fedora Connector.

3. If the installation is successful, you will be automatically taken to
  the plugin's configuration form, which allows you to specifiy
datastreams that should be omitted from selection menus on a system-wide
basis. These values are comma delimited. The datastreams omitted by
default are "RELS-EXT," "RELS-INT," and "AUDIT."

[Installing a Plugin]: http://omeka.org/codex/Installing_a_Plugin

## Usage

Under the "Fedora Connector" tab in the main horizontal menu in the
administrative interface, there are two sub-tabs:

### Servers

Use this interface to add new Fedora servers or edit existing servers.

To add a server:

1. Click the "Add a Server" button at the top right;

2. Enter a name for the server. This name is native to the Omeka record
   for the server, and doesn't need to correspond to any kind of
external naming convention on the server itself;

3. Enter the base URL for the server. This must have the format
   "http://[HOST]/fedora/";

4. Click the checkbox at the bottom of the form is you want the server
   to be the default. If a default server is set, this server is
automatically set as the starting selection in the dropdown box in the
workflow used to add datastreams. If a default server is already set and
you check this box for the new server, the plugin will automatically
unset the original default and make the new server the default.

5. Click the "Create" button.

To edit a server:

1. Click on either the link for the "Name" of the server in the left
  column or the green "Edit" button in the right column;

2. Make changes to the fields. As with the
  create server workflow, if you check the "Is this the default server"
option, the plugin will unset the old default and make the edited server
the new default;

3. Click the "Save" button.

To delete a server:

1. Click the red "Delete" button either in the main servers listing view
  or in the edit view for an individual server.

### Datastreams

A Fedora datastream is a digital object that represents or is associated with an intellectual object. For example, a digitized photograph may have three datastreams: a digital image, a Dublin Core XML file, and a MODS metadata file which contains more than the basic information encoded in DC. More than one datastream can be associated with an Omeka item. For example, a thumbnail and larger sized image my be datastreams associated with a single Fedora object.

To add datastreams:

1. Click the "Add Datastreams" button at the top right;

2. Find the item that you want to add the new datastreams to. Click on
  the column headings to sort the list and use the search box to find
individual items. Once you find the item, click the corresponding "Select ->" button. 

    <strong>NOTE</strong>: You can also add a datastream to an item by way of the Fedora Connector tab on the item's "Edit" screen. Find the item through the main Items interface (the first tab on the horizontal administrative menu), click "Edit," and click the tab for "Fedora Datastreams." Then click through to add new datastreams.

3. Select the server from the drop-down box, enter the PID for the
   target object in the Fedora repository, and click "Continue."

4. Select the metadata format and check each of the component
   datastreams that you want to create records for. Checking a
datastream will not automatically import its data at this point - it
just creates a record inside of Omeka that defines the connection
between the datastream and the item, and allows you to import the data
at any point.

5. Click the "Continue" button to add the datastreams.

### Import Datastreams

Once a datastream is associated with an item, the last step is to
actually import the datastream data and write it into the Omeka item
record. This is a one-click process that can be performed from two
locations:

- From the "Datastreams" tab under the main "Fedora Connector" tab, just
  click the green "Import" button in the right column.

    Or:

- Find the Omeka item in the main "Items" tab on the right of the main
  horizontal administrative menu, click "Edit," and open the "Fedora
Connector" tab. Click the "Import" link for the datastream that you want
to import.

## Extending the plugin to accept new metadata formats

As of July 2011, FedoraConnector can automatically process metadata from
Fedora datastreams if it is in Dublin Core or MODS formats. However, the
plugin is engineered using a modular "sub-plugin" structure that makes
it possible to easily program interfaces to any metadata format. Once a
new importer is defined and placed in the Importers directory,
FedoraConnector will automatically detect its presence and deploy it
when a user attempts to import a datastream with the format accommodated by
the new importer.

All impoters inherit from an abstract class called FedoraConnector_AbstractImporter, which is located in the /libraries/FedoraConnector/ folder. All of the utility functions that perform the actual import are defined in the abstract class, and the concrete child classes need to define just two functions: canImport() and getQueries().

canImport() takes a FedoraConnectorDatastream object and just defines the name of the new format that is being
handled by the importer. This is the function that FedoraConnector uses
to populate the list of allowed metadata formats when a new datastream
is added. So, for example, the core DC importer just checks to see if
the $datastream->metadata_stream attribute is "DC," and, if so, returns
true. Use this syntax:

        return ($datastream->metadata_stream == '[NAME_OF_FORMAT]');

The real work is done in the getQueries() function, which takes the name
of a Dublin Core element and returns the XPath query to find and extract
the corresponding element in the markup schema in question. This
function should use a switch-case construct to run through the possible
tag mappings and return the query that will yield an appropriate value
for the Dublin Core element in question. For example, the first three
tag mappings of the MODS converted look like this:

        switch ($name) {

            case 'Title':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="titleInfo"]'
                    . '/*[local-name()="title"]'
                );

                break;

            case 'Creator':

                $queries = array(
                    '//*[local-name()="mods"]'
                    . '/*[local-name()="name"][*[local-name()="role"] = "creator"]'
                );

                break;

            case 'Subject':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="subject"]'
                    . '/*[local-name()="topic"]'
                );

                break;

In this way, any conceivable metadata format can be mapped onto Omeka's
native Dublin Core item representations.

## Extending the plugin to render new data types

Likewise, FedoraConnector has a modular system for defining "Renderers,"
which control the display format for data imported from Fedora. Like the
importers, concrete renderer classes inherit from an asbtract class,
this time called FedoraConnector_AbstractRenderer.

For renderers, four functions need to be defined: canDisplay(),
canPreview(), display(), and preview(). canDisplay() and canPreview()
each take a FedoraConnectorDatastream object and return true if the
renderer can handle the datastream's mime_type. Use this syntax to run
the mime_type through a regular expression and return a boolean:

        return (bool)(preg_match('/^image\//', $datastream->mime_type));

Assuming you plan to populate functions display() and preview(), canPreview() can usually just invoke canDisplay().

The display() and preview() functions, meanwhile, take the datastream
object and return HTML markup to be displayed. For example, the core
image renderer just returns an image tag:

        function display($datastream) {

            $url = $datastream->getContentUrl();
            $html = "<img alt='image' src='{$url}' />";
            return $html;

        }

# Fedora Connector
[![Dependency Status](https://gemnasium.com/scholarslab/FedoraConnector.png)](https://gemnasium.com/scholarslab/FedoraConnector)
[![Build Status](https://secure.travis-ci.org/scholarslab/FedoraConnector.png)](http://travis-ci.org/scholarslab/FedoraConnector)

FedoraConnector connects items in an Omeka collection with objects in [Fedora Commons][fedora-commons] repositories. The plugin makes it possible to create one-to-one associations between Omeka items and Fedora objects and automatically populate the Dublin Core record for the item with values extracted from datastreams associated with the Fedora object.

The plugin also exposes a system of extensible "sub-plugins" that makes it possible to easily add custom importers and renderers to handle different datastream formats (basic Dublin Core, MODS, and image-type handlers come pre-installed).

All of this works together to allow you to include metadata and media from a Fedora store in your Omeka exhibits. It also seemlessly integrates with the [ExhibitBuilder plugin](http://omeka.org/add-ons/plugins/exhibit-builder/) to display media from Fedora on the exhibit pages.

Here's the basic workflow:

- <strong>Add server records that point to Fedora Commons repositories</strong>: Different items in the same Omeka collection can pull from objects in different Fedora repositories.

- <strong>Associate Omeka items with Fedora objects</strong>: Each item in the Omeka collection can be linked to one or more of the datastreams provided by a single Fedora object.

- <strong>Populate item records with Fedora data</strong>: The plugin retrieves data from the Fedora datastreams and maps it onto the fields in the Dublin Core record for the item. For datastreams that do not deliver plain-text data than can be written directly into the Omeka elements, the plugin can also generate new markup to display content (like images) pulled directly from the repository.

## Requirements

Currently, FedoraConnector can only interact with Fedora servers that do not place authorization checks on remote requests to access object datastreams. Refer to the [Fedora Commons documentation][fedora-docs] on installing, configuring, and running a digital repository.

## Installing and Configuring

1. Copy the FedoraConnector folder into the "plugins" folder of the root Omeka installation. (see [Installing a Plugin][omeka-plugins])

2. In the Omeka administrative interface, click on the "Settings" button at the top right of the screen, go to the "Plugins" tab, and click the "Install" button next to the listing for Fedora Connector.

## Usage

### Add Servers

Before you can associate items with Fedora objets, you have to enter information for at least one Fedora server. To add a server:

1. Click the "Fedora Connector" tab in the main administrative menu bar.

2. Enter a name for the server. This is a non-public value used for content management in the administrative interface.

3. Enter the base URL for the server.

5. Click the "Save" button.

If the fields are valid, you will be redirected to the "Browse Servers" page, where you should see a listing for the new server. If Fedora Connector succeeds in connecting to the server, the "Status" column will read "Online." If the status is listed as "Offline," something went wrong and the plugin is unable to pull information from the server. Check that the server is running and that the URL field is entered correctly.

To edit a server:

1. Click on the "Edit" link under "Actions."

2. Make edits to the form and click the "Save" button.

To delete a server:

1. Click the "Delete" button under "Actions."

### Connect Omeka items with Fedora objects

With a server (or multiple servers) created, you can link items in the Omeka collection with remote Fedora objects. Go to the "Edit Item" view by adding a new item or editing an existing item. click on the "Fedora" tab on the left vertical menu.

1. Select a server in the "Server" dropdown.

2. Enter the PID of the Fedora object that you want to link the item with. When you type or paste a PID into the field, the full list of datastreams provided by the object will appear in the "Datastreams" box. Change the server and PID at any point to get an updated list of datastreams.

3. Select one or more datastreams. More than one datastream can be selected by holding down the Control (Windows) or Command (Mac) button.

4. You can go ahead and save the item at this point and the current configuration of the Fedora options will be saved. If you selected datastreams that deliver non-text content of a format that can be accommodated by one of the classes in the /Renderers folder, the content will automatically appear at the bottom of the item show views.

For datastreams like Dublin Core and MODS that deliver plain-text elements that can be mapped onto Omeka Dublin Core elements, the plugin will copy over the Fedora values as new element texts on the local item record. Check the "Import now?" checkbox to execute this import process when the item form is adder or saved (when you click on the "Save Changes" button).

Once you've connected an Omeka item with a Fedora object, the connection can be edited at any point - just change the values in the Server, PID, and Datastreams inputs, and the plugin will immediately update to render the selected datastreams on the new object.

Note that plain-text datastreams like Dublin Core and MODS that create _copies_ of the Fedora values in the Omeka item record won't be automatically updated if the Fedora object association is changed after the imported values are written to the database.

[plugin]: http://omeka.org/add-ons/plugins/fedoraconnector/
[fedora-commons]: http://www.fedora-commons.org/
[fedora-docs]: https://wiki.duraspace.org/display/FCR30/Fedora+Repository+3.4.2+Documentation
[omeka-plugins]: http://omeka.org/codex/Installing_a_Plugin

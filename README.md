# Fedora Connector

FedoraConnector makes it possible to connect an Omeka site with one more
more [Fedora Commons] repositories. This allows users to link Omeka
items with "datastreams" on a Fedora Commons repository and
automatically populate the Dublin Core fields for the Omeka item with
the values defined by the datastream.

The plugin introduces two basic taxonomies - Servers and Datastreams. A
server corresponds to a Fedora repository, while a datastream represents
an individual bundle of metadata contained within a specific object in
the repository, each of which is defined by a unique PID number.

The basic workflow for importing Fedora data into Omeka looks like this:

- <strong>Create server records for Fedora Commons repositories</strong>: In the Omeka plugin, create a simple record with the title and base
  URL for the external Fedora Commons repository;

- <strong>Add one or more datastreams to an Omeka item</strong>: After selecting the
  item that you want to associate the new datastreams with, enter the
PID of the object on the Fedora repository, and select one or more of the
component datastreams;

- <strong>Import the datastream</strong>: This is a one-click process that fetches the data from Fedora and populates the
  Dublin Core fields for the item in Omeka with the fields from the
datastream.

[Fedora Commons]: http://www.fedora-commonsorg/

## Requirements

The plugin assumes that your Fedora Commons repository is active and
accessible, either on localhost (the default) or the web. Currently,
FedoraConnector can only interact with Fedora servers that do not place
authorization checks on remote requests to access object datastreams.
Refer to the Fedora Commons documentation on installing, configuring,
and running a digital repository.

## Installing and Configuring

- Clone the FedoraConnector folder into the "plugins" folder of the root
Omeka installation. (see [Installing a Plugin])

- In the Omeka administrative interface, click on the orange "Settings"
  button at the top right of the screen, go to the "Plugins" tab, and
click the "Install" button next to the listing for Fedora Connector.

- If the installation is successful, you will be automatically taken to
  the plugin's configuration form, which allows you to specifiy
datastreams that should be omitted from selection menus on a system-wide
basis. These values are comma delimited. The datastreams omitted by
default are "RELS-EXT," "RELS-INT," and "AUDIT."

[Installing a Plugin]: http://omeka.org/codex/Installing_a_Plugin

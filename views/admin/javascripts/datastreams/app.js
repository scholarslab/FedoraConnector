/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2; */

/**
 * Datastreams management application.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

FedoraDatastreams = new Backbone.Marionette.Application();

// ---------------------
// Component namespaces.
// ---------------------
FedoraDatastreams.Controllers = {};
FedoraDatastreams.Views = {};


// ---------------
// Initialization.
// ---------------

/*
 * Instantiate form.
 *
 * @return void.
 */
FedoraDatastreams.addInitializer(function() {
  new FedoraDatastreams.Views.Form({
    el: '#fedora-metadata'
  });
});

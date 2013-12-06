
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


FedoraDatastreams = new Backbone.Marionette.Application();


// Component namespaces.
FedoraDatastreams.Controllers = {};
FedoraDatastreams.Views = {};


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

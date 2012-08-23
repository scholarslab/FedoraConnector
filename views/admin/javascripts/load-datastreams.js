/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2; */

/**
 * Datastreams load routine.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

Fedora = {};
$ = jQuery;

// Get script path.
Fedora.scripts = $('script');
Fedora.src = Fedora.scripts[Fedora.scripts.length-1].src.replace(/[^\/]+.js/, '');

var a = Fedora.src+'datastreams/';
var v = Fedora.src+'vendor/';

load(

  // Underscore.
  v+'underscore/underscore.js'

).then(

  // Backbone.
  v+'backbone/backbone.js'

).then(

  // Marionette.
  v+'backbone/marionette.js'

).then(

  // Application.
  a+'app.js'

).then(

  // Views.
  a+'views/form-view.js'

).thenRun(function() {

  // Run.
  $(function() {
    FedoraDatastreams.start();
  });

});


/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Fedora = {};
$ = jQuery;

// Get script path.
Fedora.scripts = $('script');
Fedora.js = Fedora.scripts[Fedora.scripts.length-1].src.replace(/[^\/]+.js/, '');

var a = Fedora.js+'datastreams/';
var v = Fedora.js+'vendor/';

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

/*
 * Include dependencies.
 */

var a = 'javascripts/oversoul/';
var v = 'javascripts/vendor/';

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

).thenRun(function() {

  // Run.
  $(function() {
    FedoraDatastreams.start();
  });

});

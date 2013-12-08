
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


var FC = (function(FC) {


  /**
   * Mock the server, start Fedora.
   *
   * @param {String} fixture: The application HTML fixture.
   */
  FC.startApplication = function(fixture) {

    // Un-mock the server, load HTML.
    if (this.server) this.server.restore();
    loadFixtures(fixture);

    // Re-mock the server.
    this.server = sinon.fakeServer.create();

    // Start the application.
    this.stopApplication();
    Fedora.start();

  };


  /**
   * Recursively stop all modules and remove all event bindings.
   */
  FC.stopApplication = function() {

    // Stop the modules.
    _.each(Fedora.submodules, function(m) { m.stop(); });
    Fedora._initCallbacks.reset();

    // Clear the event channels.
    Fedora.commands.removeAllHandlers();
    Fedora.reqres.removeAllHandlers();
    Fedora.vent._events = {};

  };


  return FC;


})(FC || {});

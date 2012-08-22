/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */

/**
 * Form controller.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

FedoraDatastreams.Controllers.Form = (function(Backbone, Ov) {

  var Form = {};


  // ---------------
  // Initialization.
  // ---------------

  /*
   * Instantiate timer and points.
   *
   * @return void.
   */
  FedoraDatastreams.addInitializer(function() {
    Form.Form = new FedoraDatastreams.Views.Form({
      el: '#fedora-datastreams-metadata'
    });
  });

  return Form;

});

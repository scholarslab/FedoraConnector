/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */

/**
 * Form view.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

FedoraDatastreams.Views.Form = Backbone.View.extend({

  events: {
    'keyup input[name="pid"]': 'getDatastreams'
  },

  /*
   * Get markup.
   *
   * @return void.
   */
  initialize: function() {

    // Get input and uri's.
    this.input = this.$el.find('input[name="pid"]');
    this.datastreamsUri = this.$el.find('input[name="datastreamsuri"]').val();

  },

  /*
   * Get datastreams.
   *
   * @return void.
   */
  getDatastreams: function() {

    // Fetch datastreams.
    $.ajax({
      url: this.datastreamsUri,
      success: function(data) { console.log(data); }
    });

  }

});

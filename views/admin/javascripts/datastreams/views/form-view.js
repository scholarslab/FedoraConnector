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
    'keyup input[@name="pid"]': 'processKeystroke'
  },

  /*
   * Get markup.
   *
   * @return void.
   */
  initialize: function() {
    this.input = this.$el.find('input[@name="pid"]');
    console.log('test');
  },

  /*
   * Clear out the stacks.
   *
   * @return void.
   */
  processKeystroke: function() {
    console.log(this.input.val());
  }

});

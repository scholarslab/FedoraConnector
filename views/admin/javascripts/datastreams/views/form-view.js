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

    // Get inputs.
    this.server = this.$el.find('select[name="server"]');
    this.datastream = this.$el.find('select[name="dsids[]"]');
    this.pid = this.$el.find('input[name="pid"]');

    // Get values in hidden fields.
    this.datastreamsUri = this.$el.find('input[name="datastreamsuri"]').val();
    this.savedDsids = this.$el.find('input[name="saveddsids"]').val();

    // If the pid is populated, get datastreams.
    if (this.pid.val() !== '') this.getDatastreams();

  },

  /*
   * Get datastreams.
   *
   * @return void.
   */
  getDatastreams: function() {

    var params = {
      server: this.server.val(),
      pid: this.pid.val()
    };

    // Fetch datastreams.
    $.ajax({
      url: this.datastreamsUri,
      dataTyle: 'json',
      data: params,
      success: _.bind(function(data) {
        this.renderDatastreams(data);
      }, this)
    });

  },

  /*
   * Render datastreams.
   *
   * @param {Object} data: The JSON.
   *
   * @return void.
   */
  renderDatastreams: function(data) {

    // Clear select.
    this.datastream.empty();

    // Render options.
    _.each(data, _.bind(function(node) {
      var option = $('<option>').text(node.label).val(node.dsid);
      this.datastream.append(option);
    }, this));

    // Populated saved dsid.
    if (!_.isEmpty(this.savedDsids)) {
      this.datastream.val(this.savedDsids.split(','));
    }

  }

});

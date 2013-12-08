
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Fedora.module('Datastreams', function(Datastreams) {


  Datastreams.View = Backbone.View.extend({


    events: {
      'change input[name="pid"]': 'getDatastreams',
      'keyup input[name="pid"]':  'getDatastreams'
    },


    /*
     * Get markup.
     */
    initialize: function() {

      // Get inputs.
      this.server = this.$('select[name="server"]');
      this.datastreams = this.$('select[name="dsids[]"]');
      this.pid = this.$('input[name="pid"]');

      // Get values in hidden fields.
      this.datastreamsUrl = this.$('input[name="datastreamsuri"]').val();
      this.savedDatastreams = this.$('input[name="saveddsids"]').val();

      // If the pid is populated, get datastreams.
      if (this.pid.val() !== '') this.getDatastreams();

    },


    /*
     * Get datastreams.
     */
    getDatastreams: function() {

      var params = {
        server: this.server.val(),
        pid: this.pid.val()
      };

      $.ajax({

        dataType: 'json',
        url: this.datastreamsUrl,
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
     */
    renderDatastreams: function(data) {

      // Clear select.
      this.datastreams.empty();

      // Render options.
      _.each(data, _.bind(function(node) {
        var option = $('<option>').text(node.label).val(node.dsid);
        this.datastreams.append(option);
      }, this));

      // Populated saved dsid.
      if (!_.isEmpty(this.savedDatastreams)) {
        this.datastreams.val(this.savedDatastreams.split(','));
      }

    }


  });


});

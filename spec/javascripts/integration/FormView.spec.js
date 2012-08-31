/*
 * Integration tests for form view.
 */

describe('Form View', function() {

  // Mock datastreams.
  var datastreamsJson = {
    status: 200,
    responseText: readFixtures('datastreams-json.html')
  };

  describe('Item add', function() {

    var form, pid, datastreams, e;

    // Get fixtures.
    beforeEach(function() {

      // Mock keypress.
      e = $.Event('keyup');

      // Load fixtures.
      loadFixtures('item-add.html');

      // Install AJAX mock.
      jasmine.Ajax.useMock();

      // Get markup.
      datastreams = $('#dsids');
      pid = $('#pid');

      // Instantiate form view.
      form = new FedoraDatastreams.Views.Form({
        el: '#fedora-metadata'
      });

    });

    it('should load datastreams when a PID is entered', function() {

      // Mock keypress.
      pid.val('pid:test');
      pid.trigger(e);

      // Inject fixture.
      request = mostRecentAjaxRequest();
      request.response(datastreamsJson);

      // Get select options.
      var options = datastreams.find('option');
      expect($(options[0]).attr('value')).toEqual('DC');

    });

  });

  describe('Item edit', function() {

    // Get fixtures.
    beforeEach(function() {
      loadFixtures('item-edit.html');
    });

    it('should auto-load datastreams for the saved PID');

  });

});

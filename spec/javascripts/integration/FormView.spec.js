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

      // Load fixtures, wrap form.
      loadFixtures('item-add.html');

      // Install AJAX mock.
      jasmine.Ajax.useMock();

      // Get markup.
      datastreams = $('#dsids');
      pid = $('#pid');

      // Instantiate form view.
      form = new FedoraDatastreams.Views.Form({
        el: '#datastream-form'
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
      expect($(options[0]).text()).toEqual('Dublin Core Record');
      expect($(options[1]).attr('value')).toEqual('descMetadata');
      expect($(options[1]).text()).toEqual('MODS descriptive metadata');
      expect($(options[2]).attr('value')).toEqual('rightsMetadata');
      expect($(options[2]).text()).toEqual('Hydra-compliant access rights metadata');
      expect($(options[3]).attr('value')).toEqual('POLICY');
      expect($(options[3]).text()).toEqual('Fedora-required policy datastream');
      expect($(options[4]).attr('value')).toEqual('RELS-INT');
      expect($(options[4]).text()).toEqual('Datastream Relationships');
      expect($(options[5]).attr('value')).toEqual('technicalMetadata');
      expect($(options[5]).text()).toEqual('Technical metadata');
      expect($(options[6]).attr('value')).toEqual('RELS-EXT');
      expect($(options[6]).text()).toEqual('Object Relationships');
      expect($(options[7]).attr('value')).toEqual('solrArchive');
      expect($(options[7]).text()).toEqual('Index Data for Posting to Solr');
      expect($(options[8]).attr('value')).toEqual('content');
      expect($(options[8]).text()).toEqual('JPEG-2000 Binary');

    });

  });

  describe('Item edit', function() {

    // Get fixtures.
    beforeEach(function() {

      // Load fixtures, wrap form.
      loadFixtures('item-edit.html');

      // Install AJAX mock.
      jasmine.Ajax.useMock();

      // Get markup.
      datastreams = $('#dsids');
      pid = $('#pid');

      // Instantiate form view.
      form = new FedoraDatastreams.Views.Form({
        el: '#datastream-form'
      });

      // Inject fixture.
      request = mostRecentAjaxRequest();
      request.response(datastreamsJson);

    });

    it('should auto-load datastreams for the saved PID', function() {

      // Get select options.
      var options = datastreams.find('option');
      expect($(options[0]).attr('value')).toEqual('DC');
      expect($(options[0]).text()).toEqual('Dublin Core Record');
      expect($(options[1]).attr('value')).toEqual('descMetadata');
      expect($(options[1]).text()).toEqual('MODS descriptive metadata');
      expect($(options[2]).attr('value')).toEqual('rightsMetadata');
      expect($(options[2]).text()).toEqual('Hydra-compliant access rights metadata');
      expect($(options[3]).attr('value')).toEqual('POLICY');
      expect($(options[3]).text()).toEqual('Fedora-required policy datastream');
      expect($(options[4]).attr('value')).toEqual('RELS-INT');
      expect($(options[4]).text()).toEqual('Datastream Relationships');
      expect($(options[5]).attr('value')).toEqual('technicalMetadata');
      expect($(options[5]).text()).toEqual('Technical metadata');
      expect($(options[6]).attr('value')).toEqual('RELS-EXT');
      expect($(options[6]).text()).toEqual('Object Relationships');
      expect($(options[7]).attr('value')).toEqual('solrArchive');
      expect($(options[7]).text()).toEqual('Index Data for Posting to Solr');
      expect($(options[8]).attr('value')).toEqual('content');
      expect($(options[8]).text()).toEqual('JPEG-2000 Binary');

    });

    it('should automatically select saved dsids', function() {
      expect(datastreams.val()).toEqual(['DC', 'content']);
    });

  });

});

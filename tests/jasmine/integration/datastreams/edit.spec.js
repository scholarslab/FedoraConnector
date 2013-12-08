
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

describe('Datastreams | Item Edit', function() {


  var fixtures = {
    datastreams: readFixtures('datastreams.json')
  };


  beforeEach(function() {
    FC.startApplication('item-edit.html');
    FC.respondLast200(fixtures.datastreams);
  });


  it('should automatically reload datastreams for a saved PID', function() {

    // ------------------------------------------------------------------------
    // When a Fedora PID has already been provided for an item, datastreams
    // for the saved PID should be automatically reloaded and repopulated in
    // the "Datstreams" select when the item edit form is opened.
    // ------------------------------------------------------------------------

    // Alias the "Datastreams" element.
    var datastreams = Fedora.Datastreams.__view.datastreams;

    // Should list datastream options.
    var ds = datastreams.find('option');
    expect($(ds[0]).attr('value')).toEqual('DC');
    expect($(ds[0]).text()).toEqual('Dublin Core Record');
    expect($(ds[1]).attr('value')).toEqual('descMetadata');
    expect($(ds[1]).text()).toEqual('MODS descriptive metadata');
    expect($(ds[2]).attr('value')).toEqual('rightsMetadata');
    expect($(ds[2]).text()).toEqual('Hydra-compliant access rights metadata');
    expect($(ds[3]).attr('value')).toEqual('POLICY');
    expect($(ds[3]).text()).toEqual('Fedora-required policy datastream');
    expect($(ds[4]).attr('value')).toEqual('RELS-INT');
    expect($(ds[4]).text()).toEqual('Datastream Relationships');
    expect($(ds[5]).attr('value')).toEqual('technicalMetadata');
    expect($(ds[5]).text()).toEqual('Technical metadata');
    expect($(ds[6]).attr('value')).toEqual('RELS-EXT');
    expect($(ds[6]).text()).toEqual('Object Relationships');
    expect($(ds[7]).attr('value')).toEqual('solrArchive');
    expect($(ds[7]).text()).toEqual('Index Data for Posting to Solr');
    expect($(ds[8]).attr('value')).toEqual('content');
    expect($(ds[8]).text()).toEqual('JPEG-2000 Binary');

    // Should apply saved datastream selection.
    expect(datastreams.val()).toEqual(['DC', 'content']);

  });


});

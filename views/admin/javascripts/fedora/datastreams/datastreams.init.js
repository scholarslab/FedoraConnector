
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Fedora.module('Datastreams', function(Datastreams) {


  Datastreams.addInitializer(function() {
    Datastreams.__view = new Datastreams.View({ el: '#fieldset-fedora' });
  });


});

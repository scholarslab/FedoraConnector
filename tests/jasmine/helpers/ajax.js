
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


var FC = (function(FC) {


  /**
   * Get the most recent request.
   *
   * @return {Object} request: The sinon request.
   */
  FC.getLastRequest = function() {
    return _.last(this.server.requests);
  };


  /**
   * Get the parameters from the most recent request.
   *
   * @return {Object} params: The parameters.
   */
  FC.getLastRequestParams = function() {
    return $.parseJSON(this.getLastRequest().requestBody);
  };


  /**
   * Inject AJAX mock into a sinon request.
   *
   * @param {Object} request: The sinon request.
   * @param {Object} response: The response body.
   * @param {String} type: The content type.
   */
  FC.respond200 = function(request, response, type) {
    request.respond(
      200, { 'Content-Type': type || 'application/json' }, response
    );
  };


  /**
   * Respond 200 to the last AJAX call.
   *
   * @param {Object} response: The response body.
   * @return {Object} response: The last request.
   */
  FC.respondLast200 = function(response) {
    var request = this.getLastRequest();
    this.respond200(request, response);
    return request;
  };


  return FC;


})(FC || {});

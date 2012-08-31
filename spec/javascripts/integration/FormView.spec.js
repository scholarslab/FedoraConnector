/*
 * Integration tests for form view.
 */

describe('Form View', function() {

  describe('Item add', function() {

    // Get fixtures.
    beforeEach(function() {
      loadFixtures('item-add.html');
    });

    it('test', function() {
      expect(1).toEqual(1);
    });

  });

  describe('Item edit', function() {

    // Get fixtures.
    beforeEach(function() {
      loadFixtures('item-edit.html');
    });

  });

});

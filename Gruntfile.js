
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


module.exports = function(grunt) {

  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jasmine');
  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-shell');

  grunt.initConfig({

    bower: {
      install: {
        options: {
          copy: false
        }
      }
    },

    shell: {

      options: {
        stdout: true
      },

      phpunit: {
        command: '../../vendor/bin/phpunit',
        options: {
          execOptions: {
            cwd: 'tests/phpunit'
          }
        }
      },

    },

    clean: {
      pkg: 'pkg'
    },

    concat: {
      datastreams: {
        src: [
          'bower_components/underscore/underscore.js',
          'bower_components/backbone/backbone.js',
          'views/admin/javascripts/datastreams.js'
        ],
        dest: 'views/admin/javascripts/payloads/datastreams.js'
      }
    },

    uglify: {
      datastreams: {
        src: '<%= concat.datastreams.dest %>',
        dest: '<%= concat.datastreams.dest %>'
      }
    },

    watch: {
      datastreams: {
        files: 'views/admin/javascripts/*.js',
        tasks: 'concat'
      }
    }

  });

  // Run application tests.
  grunt.registerTask('default', 'test');

  // Build the application.
  grunt.registerTask('build', [
    'bower',
    'concat'
  ]);

  // Spawn a release package.
  grunt.registerTask('package', [
    'uglify',
    'clean:pkg',
    'compress'
  ]);

  // Run the PHPUnit suite.
  grunt.registerTask('phpunit', [
    'shell:phpunit'
  ]);

  // Run all test suites.
  grunt.registerTask('test', [
    'phpunit'
  ]);

};

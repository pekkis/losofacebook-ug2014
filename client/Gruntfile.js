module.exports = function(grunt) {

    require('jit-grunt')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        revision: "r" + process.cwd().split('/').pop(),

        less: {

            common: {
                files: {
                    "web/client.css": [
                        "client.less"
                    ]
                }
            }
        },

        watch: {

            less: {
                files: ['**/*.less'],
                tasks: ['less'],
                options: {
                    spawn: false
                }
            },

            jsx: {
                files: ['components/**/*.js'],
                tasks: ['shell:browserify'],
                options: {
                    spawn: false
                }
            }
        },

        shell: {
            browserify: {
                options: {
                    stderr: false
                },
                command: ['browserify -t reactify -o web/bundle.js client.js'].join(' && ')
            }
        }
    });

    grunt.registerTask('css', ['less']);
    grunt.registerTask('default', ['css']);
};

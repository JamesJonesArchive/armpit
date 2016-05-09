// Generated on 2015-02-03 using generator-angular-php 0.6.2
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Configurable paths for the application
    var appConfig = {
        name: require('./package.json').name,
        version: require('./package.json').version
    };

    // Define the configuration for all the tasks
    grunt.initConfig({
        // Project settings
        appEnv: appConfig,
        clean: {
            deploy: {
                src: ['deploy/*.rpm', 'deploy/*.deb']
            }
        },
        chmod: {
            options: {
                mode: '755'
            },
            pharbits: {
                // Target-specific file/dir lists and/or options go here.
                src: ['bin/armpit.phar']
            }
        },
        shell: {
            options: {
                stdout: true,
                stderr: true,
                failOnError: true
            },
            buildcmd: {
                command: './build.sh'
            },
            mkdeploy: {
                command: 'mkdir -p deploy'
            },
            fpmrpm: {
                "command": [
                    [
                        '/usr/local/bin/fpm -s dir -t rpm -n \'<%= appEnv.name %>\' -v <%= appEnv.version %> ',
                        '"php"',
                        '"php-common"',
                        '"php-mysqlnd"',
                        '"php-pdo"',
                        '"php-devel"',
                        '"php-pear"',
                        '"php-gd"',
                        '"php-mcrypt"',
                        '"php-xml"',
                        '"php-mbstring"',
                        '"php-xml"',
                        '"php-cli"',
                        '"php-pecl-mongo"',
                        '"mongo-tools"',
                        '"mongodb"',
                        '"mongodb-server"',
                        '"libmongodb"'
                    ].join(' -d '),
                    '--after-install app/setupconfig.sh -p deploy ./bin/armpit.phar=/usr/local/bin/armpit'
                ].join(' ')
            }
        }
    });

    grunt.registerTask('build', [
        "shell:buildcmd"
    ]);

    grunt.registerTask('buildrpm', [
        "chmod:pharbits",
        "clean:deploy",
        "shell:mkdeploy",
        "shell:fpmrpm"
    ]);

};

const chalk = require( 'chalk' );
const fs = require( 'fs' );
const semver = require( 'semver' );
const _ = require( 'lodash' );
//const textReplace = require('grunt-text-replace/lib/grunt-text-replace');

module.exports = function gruntInit( grunt ) {
	// Project configuration.

	var jsDocPath = 'docs/javascript',
		wpVersion = {},
		currentVersion = require( './package.json' ).version;
	const gruntConfig = {
		pkg:    grunt.file.readJSON( 'package.json' ),
		uglify: {
			options: {
				preserveComments: 'some',
			},
			header: {
				options: {
					banner:    '/*! <%= pkg.name %> build on <%= grunt.template.today("yyyy-mm-dd hh:MM:ss") %> for v<%= pkg.version %> */',
					sourceMap: false,
					footer:    '/**/',
				},
				files: [
					{
						expand: true,
						src:    [
							'js/dist/**.js',
							'!js/dist/**.min.js',
						],
						rename: ( dst, src ) => src.replace( /.js$/, '.min.js' ),
					},
				],
			},
		},
		jsdoc: {
			dist: {
				src:     [ 'js/src/**/*.js' ],
				options: {
					private:     true,
					destination: `${jsDocPath}/jsdoc`,
				},
			},
		},
		eslint: {
			options: {
				format: 'stylish',
				fix:			true,
				useEslintrc:	false,
			},
			info_browser: {
				options: {
					configFile: 'lint/eslint-browser.json',
					silent:     true,
					fix:		true,
				},
				src: [
					'js/src/**.js',
					'!js/src/**.min.js',
				],
			},
			strict_browser: {
				options: {
					configFile: 'lint/eslint-browser.json',
				},
				src: [
					'js/**.js',
					'!js/**.min.js',
				],
			},
		},
		babel: {
			options: {
				sourceMap: true,
				presets:   [ 'es2015' ],
			},
			dist: {
				files: [{
					expand: true,
					cwd : 'js/src',
					src:    [
						'**/*.js',
						'!**/*.min.js',
					],
					dest: 'js/dist',
					ext:  '.js',
				}],
			},
		},
		phplint: {
			check: [
				'class/**/*.php',
				'*.php',
			],
		},
		docco: {
			debug: {
				src: [
					'js/src/**/*.js',
					'tests/**/*.js',
				],
				options: {
					output: `${jsDocPath}/docco`,
				},
			},
		},
		phpdoc: {
			dist: {
				options: {
					verbose: true
				},
				src: [
					'class/**/*.php',
					'*.php',
				],
				dest: 'docs/php',
			}
		},
	};
	grunt.initConfig(gruntConfig);

	// Load the plugin that provides the 'uglify' task.
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-text-replace' );
	grunt.loadNpmTasks( 'grunt-changed' );
	grunt.loadNpmTasks( 'grunt-jsdoc' );
	grunt.loadNpmTasks( 'grunt-docco' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-phpdoc' );
	grunt.loadNpmTasks( 'grunt-babel' );


	grunt.registerTask(
		'build',
		[
			'refreshResources',
			'documentate',
		]
	);
	grunt.registerTask(
		'documentate',
		[
			'jsdoc',
			'docco',
			'phpdoc',
		]
	);
	grunt.registerTask(
		'refreshScripts',
		[
			'eslint:info_browser',
			'babel:dist',
			'changed:uglify:header',
		]
	);
	grunt.registerTask(
		'refreshResources',
		[
			'refreshScripts',
		]
	);
	grunt.registerTask(
		'lint',
		[
			'eslint:info_browser',
			'eslint:info_nodejs',
			'lesslint:info',
			'phplint',
		]
	);
};

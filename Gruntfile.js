'use strict';

module.exports = grunt => {
	// Project configuration.

	const jsDocPath = 'docs/javascript';
	const gruntConfig = {
		pkg:    grunt.file.readJSON( 'package.json' ),
		uglify: {
			options: {
				preserveComments: 'some',
				banner:           '/*! <%= pkg.name %> build on <%= grunt.template.today("yyyy-mm-dd hh:MM:ss") %> for v<%= pkg.version %> */',
				sourceMap:        true,
				footer:           '/**/',
			},
			dist: {
				files: [
					{
						expand: true,
						src:    [
							'js/dist/**/*.js',
							'!js/dist/**/*.min.js',
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
					destination: `${ jsDocPath }/jsdoc`,
				},
			},
		},
		eslint: {
			options: {
				format:      'stylish',
				fix:         true,
				useEslintrc: false,
				configFile:  'lint/eslint.json',
				maxWarnings: -1,
				quiet:       true,
				silent:      true,
			},
			info_browser: {
				src: [
					'Gruntfile.js',
					'js/src/**.js',
					'!js/src/**.min.js',
				],
			},
		},
		babel: {
			options: {
				sourceMap: true,
				presets:   [
					[ 'env', {
						modules: 'umd',
						//modules: 'systemjs',
						targets: {
							browsers: [ 
								'>1%',
								'last 4 versions',
								'Firefox ESR',
								'not ie < 9',
							],
						},
						//						uglify:      false,
						loose:       false,
						debug:       false,
						useBuiltIns: 'usage',
					}],
				],
			},
			dist: {
				files: [{
					expand: true,
					cwd:    'js/src',
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
					output: `${ jsDocPath }/docco`,
				},
			},
		},
		phpdoc: {
			dist: {
				options: {
					verbose: true,
				},
				src: [
					'class/**/*.php',
					'*.php',
				],
				dest: 'docs/php',
			},
		},
	};
	grunt.initConfig( gruntConfig );

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
		'documentate',
		[
			'jsdoc',
			'docco',
			'phpdoc',
		]
	);
	grunt.registerTask(
		'buildScripts',
		[
			'eslint:info_browser',
			'babel:dist',
			'uglify:dist',
		]
	);
	grunt.registerTask(
		'build',
		[
			'buildScripts',
		]
	);
	grunt.registerTask(
		'lint',
		[
			'eslint:info_browser',
			'lesslint:info',
			'phplint',
		]
	);
};

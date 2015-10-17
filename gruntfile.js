
module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		jsScripts: ['source/js/vendor/parallax.js','source/js/my_scripts.js'],

		stripCssComments: {
			dist: {
				files: {
					'build/css/my_styles.css': 'build/css/my_styles.css'
				}
			}
		},

		compress: {
			main: {
				options: {
					archive: 'web.zip'
				},
				files: [
					{expand: true, cwd:'<%= pkg.templateName %>/',src: ['**/*'], dest: '/' }
					/*{src: ['css/styles.min.css'], dest: '/', filter: 'isFile'},
					 {src: ['js/scripts.min.js'], dest: '/', filter: 'isFile'},
					 {src: ['js/picturefill.min.js'], dest: '/', filter: 'isFile'},
					 {src: ['fonts/*'], dest: '/', filter: 'isFile'},
					 {src: ['index.html'], dest: '/'} */
				]
			}
		},

		postcss: {
			options: {
				//map: true, // inline sourcemaps

				// or
				map: {
					inline: false, // save all sourcemaps as separate files...
					annotation: 'build/css/maps/' // ...to the specified directory
				},

				processors: [
					require('postcss-simple-vars')({silent:true}),
					require('autoprefixer')({
						browsers:['last 2 versions']
					})
				]
			},
			build: {
				src: 'source/style.scss',
				dest: 'working/style.css'

			}

		},
		cssnano: {
			options: {
				sourcemap: true,
				discardComments: false
			},
			dist: {
				files: {
					'<%= pkg.templateName %>/style.css': '<%= pkg.templateName %>/style.css'
				}
			}
		},
		jshint: {
			main: ['source/js/my_scripts.js']

		},
		concat: {
			options: {
				sourceMap: true,
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				'<%= grunt.template.today("yyyy-mm-dd") %> */'
			},
			build: {
				//if multiple scripts and order is important, then may need to put in files individually here
				src: '<%= jsScripts %>',
				dest: 'build/js/scripts.js'
			}
		},
		uglify: {
			options: {
				preserveComments: 'some'
			},
			dist: {
				files: {
					'<%= pkg.templateName %>/js/scripts.min.js':
						['build/js/scripts.js']
				}
			}
		},
		responsive_images: {
			myTask: {
				options: {
					sizes: [{
						name: '320',
						width: 320
					},{
						name: '640',
						width: 640
					},{
						name: "1024",
						width: 1024,
						/*suffix: "_x2", */
						quality: 60
					}]
				},
				files: [{
					expand: true,
					src: ['source/images/**/*.{jpg,png}'],
					cwd: 'working/images',
					dest: 'working/r-images'
				}]
			}
		},
		copy : {
			buildPhp: {
				files: [
					{expand: true, src: ['**/*.php'], cwd: 'source/', dest: 'build/'}
				],
				options: {
					process: function(content, path) {
						var pkg = grunt.config('pkg');
						return grunt.template.process(content, {data: {'pkg': pkg, 'environment':'build'}});
					}
				}
			},
			buildCss: {
				files: {'build/style.css' : 'working/style.css' },
				options: {
					process: function(content,path){
						var pkg = grunt.config('pkg');
						return grunt.template.process(content,{data: {'pkg': pkg, 'environment':'build'}});
					}
				}

			},
			distPhp: {
				files: [
					{expand: true, src: ['**/*.php'], cwd: 'source/', dest: '<%= pkg.templateName %>/'}
				],
				options: {
					process: function(content, path) {
						var pkg = grunt.config('pkg');
						content = grunt.template.process(content, {data: {'pkg': pkg, 'environment':'dist'}});
						pkg.templatePrefix = pkg.templatePrefix ? pkg.templatePrefix : 'jht_';
						content = content.replace(/source__/gm, pkg.templatePrefix + '_');
						return content;
					}
				}
			},
			distCss: {
				files: {'<%= pkg.templateName %>/style.css' : 'working/style.css' },
				options: {
					process: function(content,path){
						var pkg = grunt.config('pkg');
						return grunt.template.process(content,{data: {'pkg': pkg, 'environment':'dist'}});
					}
				}

			}
		},
		// deploy via rsync
		rsync: {
			prod: {
			options: {
			 args: ['--verbose', '-e ssh --rsync-path=bin/rsync'],
			 src: "./<%= pkg.templateName %>",
			 dest: "~/html/wp-content/themes/",
			 host: "jerodh@jhtechservices.com",
			 recursive: true,
			 syncDest: true,
			 }
			 },


			buildImages : {
				options: {
					src: "./source/images/",
					dest: "./build/images/",
					exclude: ['.git*'],
					recursive: true,
					syncDest: true
				}
			},
			buildFonts : {
				options: {
					src: "./source/fonts/",
					dest: "./build/fonts/",
					exclude: ['.git*'],
					recursive: true,
					syncDest: true
				}
			},
			distFonts : {
				options: {
					src: "./build/fonts/",
					dest: "./<%= pkg.templateName %>/fonts/",
					exclude: ['.git*'],
					recursive: true,
					syncDest: true
				}
			}
		},

		imagemin: {                          // Task
			options: {
				optimizationLevel: 7,
				progressive: true
			},
			main: {                         // Another target
				files: [{
					expand: true,                  // Enable dynamic expansion
					cwd: 'build/images/',                // Src matches are relative to this path
					src: ['**/*.{png,jpg,gif}'],   // Actual patterns to match
					dest: '<%= pkg.templateName %>/images'                  // Destination path prefix
				}]
			}
		},
		watch: {
			css: {
				files: ['source/style.scss'],
				tasks: ['postcss','copy:buildCss'],
				options: {
					spawn: false
				}
			},
			js: {
				files: ['source/js/**/*.js'],
				tasks: ['jshint','concat'],
				options: {
					spawn: false
				}
			},
			php: {
				files: ['source/**/*.php'],
				tasks: ['newer:copy:buildPhp'],
				options: {
					spawn: false
				}
			},
			images: {
				files: ['source/images/**/*.jpg'],
				tasks: ['rsync:buildImages'],
				options: {
					spawn: false
				}
			},
			fonts: {
				files: ['source/fonts/**'],
				tasks: ['rsync:buildFonts'],
				options: {
					spawn: false
				}
			}
		}

	});

	grunt.loadNpmTasks('grunt-strip-css-comments');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-simple-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-responsive-images');
	grunt.loadNpmTasks('grunt-rsync');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-newer');
	grunt.loadNpmTasks('grunt-cssnano');
	grunt.loadNpmTasks('grunt-contrib-clean');


	grunt.registerTask(	'build', ['postcss','jshint','concat', 'rsync:buildImages', 'rsync:buildFonts',
						'newer:copy:buildPhp','copy:buildCss']);

	grunt.registerTask('dist', ['build','uglify','copy:distCss','cssnano','newer:imagemin','rsync:distFonts','newer:copy:distPhp']);
	grunt.registerTask('zip', ['compress']);
	grunt.registerTask('default',['simple-watch']);
	grunt.registerTask('responsive',['responsive_images']);

};

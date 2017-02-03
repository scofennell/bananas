/**
 * Register our NPM and Grunt tasks.
 * 
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

module.exports = function( grunt ) {

	grunt.initConfig({

		// The grunt plugin for running arbitrary CLI commands.
		exec : {

			// This command runs our unit tests and saves the results to the /reports folder.
			runmytests : {
				command : 'phpunit --coverage-html ./tests/reports/ --whitelist ./inc/'
			}

		},

		// The grunt plugin for running tasks when git events occur.
		githooks : {
		
			// We want to run the exec plugin -- which runs our unit tests -- before doing any git commit.	
			all : {
				'pre-commit' : 'exec',
			}
		
		}

	});

	// The grunt plugin for doing arbitrary CLI commands.
	grunt.loadNpmTasks( 'grunt-exec' );

	// The grunt plugin for binding tasks to git actions.
	grunt.loadNpmTasks( 'grunt-githooks' );

	// Bind our CLI command to our git action.
	grunt.registerTask( 'default', [ 'exec', 'githooks' ] );  
	
};
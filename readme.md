### Grunting a WordPress Theme ###

Initial beginnings of a gruntfile that will help in developing a WordPress Theme.

The `source` directory contains all the source files.  This is where you edit files.

The `build` directory is created from `grunt build` and is used to test your theme.  This directory should be in the themes directory of your WordPress environment.
Using a symlink is possible.  The `grunt simple-watch` process will watch for changes in the source directory
of `style.scss`, any php files, any js files, or any images.  If they change, respective grunt process are run to
update the files in the `build` folder.

`package.json` has a couple added fields.  The first one is `templateName` and is used to name the folder that will
contain the theme to distribute.  The second one is `templatePrefix` and is used to rename functions in the php files.
For instance, when coding in the source files, prefix the function with `source__` and when the `grunt dist` process
is run to create the theme folder, it will go through all php files and change `source__` to the templatePrefix.
So if the templatePrefix is defined as `eth2`, the function `source__enqueue_scripts` will become
`etch2_enqueue_scripts`

The `grunt dist` will create the directory containing the theme to distribute.  It will be named with the string
defined in `package.json` as `templateName`.

The `style.css` comments defining the template name, author, etc will be automatically filled in using
the data from `package.json`.  The template name will be the `templateName` for the distribution directory.
The `build` directory `style.css` file will have the same template name but with 'build' appended to it.

One can use the [lodash template syntax](http://lodash.com/docs) in your php files to change what gets enqueued.
For example, in the `build` directory, the javascript is not minified, but in the distribution theme directory
it is.  Using `<% if(environment==='dist') {print('script.min.js');} %>` in a php file on can change what it loads.
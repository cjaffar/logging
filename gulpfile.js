var gulp = require('gulp'),
   sass = require('gulp-sass'),
   del = require('del')
   rename = require('gulp-rename'),
   minifycss = require('gulp-cssnano'),
   minify = require("gulp-uglify"), //minify is giving errors 
   concat = require("gulp-concat")
   ;

const paths = {
	css : {
		src : "resources/sass/",
		dest : "public/assets/css/"
	},
	js : {
		src:"resources/js/",
		dest:"public/assets/js/"
	},
	node : {
		src : "node_modules/"
	}
};

gulp.task('styles', function() {

   return gulp.src( paths.css.src + 'app.scss' )
      .pipe(sass()) //.on('error', sass.logError))
      .pipe(rename('main.css'))
      .pipe(gulp.dest( paths.css.dest ))
      .pipe(minifycss())
      .pipe(rename('main.min.css'))
      .pipe(gulp.dest( paths.css.dest ))
});

gulp.task('js', function(){
	return gulp.src(
		[
			paths.node.src + 'jquery/dist/jquery.js',
			paths.node.src + 'bootstrap/dist/js/bootstrap.bundle.js',
			paths.node.src + 'moment/min/moment.min.js',
			paths.node.src + 'lightpick/lightpick.js',
			paths.node.src + 'parsleyjs/dist/parsley.js',
			paths.node.src + 'sweetalert2/dist/sweetalert2.min.js',
			paths.node.src + 'datatables.net/js/jquery.dataTables.js',
			paths.node.src + 'datatables.net-bs/js/dataTables.bootstrap.js',
			paths.js.src + 'default.js'
		]
	)
	.pipe( concat('default.js') )
	.pipe( gulp.dest( paths.js.dest ) )
	// .pipe( gulp.minify( ) )
	// .pipe( gulp.dest(paths.js.dest) )
});

gulp.task('default', function() {
	gulp.watch( [ paths.css.src + '*/*.scss', paths.css.src + '*.scss' ] , gulp.series( 'styles' ) );
	gulp.watch( [ paths.js.src + '*.js' ], gulp.series('js') );
});


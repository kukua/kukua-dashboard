/* CI Gulpfile */
var gulp = require('gulp');

var public_html = false;
var defaultTasks = ['watch'];

var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');
var concat      = require('gulp-concat');
var gulpIgnore  = require('gulp-ignore');
var less        = require('gulp-less');
var plumber     = require('gulp-plumber');
var sourcemaps  = require('gulp-sourcemaps');
var notifier    = require('node-notifier');
var notify      = require('gulp-notify');
var recess      = require('gulp-recess');

var root = ((public_html) ? './public_html/' : './');
var dist_path = root + '/assets/dist';
var js_plugin_path = './bower_components/';
var js_script_path = root + '/assets/js/';

var js_files = [
    js_plugin_path + 'jquery/dist/jquery.js',
    js_plugin_path + 'bootstrap/dist/js/bootstrap.js',
    js_plugin_path + 'moment/min/moment-with-locales.js',
    js_plugin_path + 'bootstrap-daterangepicker/daterangepicker.js',
    js_plugin_path + 'highcharts/highcharts.src.js',
    js_plugin_path + 'highcharts/highcharts-more.src.js',
    js_plugin_path + 'mailcheck/src/mailcheck.js',
	js_plugin_path + 'datatables.net/js/jquery.dataTables.js',
	js_plugin_path + 'datatables.net-bs/js/dataTables.bootstrap.js',
	js_plugin_path + 'google-maps/lib/Google.js',

    js_script_path + 'chart.js',
    js_script_path + 'graph.js',
    js_script_path + 'sensors.js',
    js_script_path + 'mailcheck.js',
    js_script_path + 'helpers.js',
	js_script_path + 'map.js'
];

/* process the js files and make it a minified js */
gulp.task('js', function() {
    gulp.src(js_files)
        .pipe(plumber({errorHandler: onError}))
        .pipe(sourcemaps.init())
        .pipe(concat('main.js'))
        //.pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(dist_path));
});

/* process the less file and make it a minified css */
gulp.task('less', function() {
    gulp.src(root + '/assets/less/main.less')
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sourcemaps.init())
        .pipe(less({
            compress: true,
        }))
        .pipe(rename({
            basename: 'main',
            suffix: '.min'
        }))
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(dist_path));
});

gulp.task('build', function() {
    gulp.start('less', 'js');
});

gulp.task('fonts', function() {
    return gulp.src(['bower_components/bootstrap/dist/fonts/*.*'])
        .pipe(gulp.dest(root + '/assets/fonts'));
});

/* look for changes in the less folder */
gulp.task('watch', function() {
    notifier.notify({
        title: 'Gulp',
        message: 'Gulp is up and running!',
        wait: false
    });

    gulp.watch(root + '/assets/js/*.js', ['js']);
    gulp.watch(root + '/assets/less/*.less', ['less']);
    gulp.watch(root + '/assets/less/*/*.less', ['less']);
});

gulp.task('lint-less', function() {
    gulp.src(root + '/assets/less/main.less')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(less({
            compress: false
        }))
        .pipe(recess({
            strictPropertyOrder: false,
            noOverqualifying: false,
            zeroUnits: false
        }))
        .pipe(recess.reporter());
});

gulp.task('lint', function() {
    gulp.start('lint-less');
});

var onError = function (err) {
    notifier.notify({
        title: 'Gulp',
        message: 'Error while compiling',
        wait: false
  });
  console.log(err);
};

/* run the watch task */
gulp.task('default', ['watch']);

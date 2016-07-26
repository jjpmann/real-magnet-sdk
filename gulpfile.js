//gulpfile.js
var phpunit = require('gulp-phpunit');

var gulp = require('gulp'),
    notify  = require('gulp-notify'),
    phpunit = require('gulp-phpunit');

gulp.task('phpunit', function() {
    var options = {debug: false, notify: true};
    gulp.src('tests/*.php')
        .pipe(phpunit('', options))
        .on('error', notify.onError({
            title: "Failed Tests!",
            message: "Error(s) occurred during testing..."
        }));
});

gulp.task('default', function(){
    gulp.run('phpunit');
    gulp.watch(['src/*.php','tests/*.php'], function(){
        gulp.run('phpunit');
    });
});
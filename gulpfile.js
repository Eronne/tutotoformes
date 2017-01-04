let gulp = require('gulp'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	autoprefixer = require('gulp-autoprefixer'),
	uglifycss = require('gulp-uglifycss');
	

gulp.task('js', function () {
	return gulp.src(['./web/js/jquery-2.1.4.js', './web/js/tinymce.js', './web/js/prism.js', './web/js/jquery.sticky-kit.min.js', './dist/web/js/app.js'])
		.pipe(concat('bundle.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./web/dist'));
});

gulp.task('css', function() {
	return gulp.src('./web/styles/app.css')
		.pipe(autoprefixer({
			browsers: ['last 2 versions'],
			cascade: false
		}))
		.pipe(uglifycss({
			"maxLineLen": 80,
			"uglyComments": true
		}))
		.pipe(gulp.dest('./web/dist'))
});

gulp.task('bundle', ['js', 'css']);

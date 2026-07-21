"use strict"

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const rename = require("gulp-rename");
const sourcemaps = require("gulp-sourcemaps");

function compileSassMin() {
    return gulp.src('./src/sass/**/*.scss')
        .pipe(sourcemaps.init())

        // estilo css minificado
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))

        // renomeia o arquivo de saída
        .pipe(rename(function (path) {
            path.basename = "theme.min";
            path.extname = ".css"
        }))

        .pipe(sourcemaps.write('.'))

        // destino
        .pipe(gulp.dest('assets/css'))
};

function compileSassExpanded() {
    return gulp.src('./src/sass/**/*.scss')

        // estilo css expandido
        .pipe(sass({ outputStyle: 'expanded' }).on('error', sass.logError))

        // renomeia o arquivo de saída
        .pipe(rename(function (path) {
            path.basename = "theme";
            path.extname = ".css"
        }))

        // destino
        .pipe(gulp.dest('assets/css'))
};

const compileSass = gulp.parallel(compileSassMin, compileSassExpanded)

function watchSass() {
    gulp.watch('./src/sass/**/*.scss', compileSass)
}

gulp.task('default', watchSass)
gulp.task('sass', compileSass)

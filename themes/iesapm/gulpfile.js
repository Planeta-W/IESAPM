"use strict"

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const rename = require("gulp-rename");

function compileSass() {
    return gulp.src('./src/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))

        // estilo css
        .pipe(sass({ outputStyle: 'compressed' })) // compressed expanded

        // renomeia o arquivo de saída
        .pipe(rename(function (path) {
            //path.dirname += "./assets/css";
            path.basename = "theme.min";
            path.extname = ".css"
        }))

        // destino
        .pipe(gulp.dest('assets/css'))
};

function watchSass() {
    gulp.watch('./src/sass/**/*.scss', compileSass)
}

gulp.task('default', watchSass)
gulp.task('sass', compileSass)

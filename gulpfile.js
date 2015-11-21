'use strict';

const source = require('vinyl-source-stream');
const es = require('event-stream');
const globby = require('globby');
const browserify = require('browserify');
const logger = require('gulp-logger');
const rename = require('gulp-rename');
const buffer = require('vinyl-buffer');
const sourcemaps = require('gulp-sourcemaps');
const gulp = require('gulp');

const builder = require('birchpress-builder')(gulp);

const productConfig = {

  coreMainSrc: [
    'index.php',
    'brithon-crmx.php',
    'loader.php',
    'package.php'
  ]

};

builder.setProductConfig(productConfig);

const bundleFiles = ['modules/**/assets/js/apps/**/index.js'];

gulp.task('bundle', function() {
  const tasks = globby.sync(bundleFiles).map(indexjs => {
    return browserify(indexjs, {
        debug: true
      })
      .transform('babelify', {
        presets: ['react']
      })
      .transform('browserify-shim', {
        global: true
      })
      .transform('pkgify')
      .bundle()
      .pipe(source(indexjs))
      .pipe(logger({
        extname: '.bundle.js',
        showChange: true
      }))
      .pipe(rename({
        extname: '.bundle.js'
      }))
      .pipe(buffer())
      .pipe(sourcemaps.init({loadMaps: true}))
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest('./'));
  });

  return es.merge(...tasks);
});

'use strict';

const gulp = require('gulp');

const builder = require('birchpress-builder')(gulp);

const productConfig = {

  coreMainSrc: [
    'index.php',
    'brithon-crmx.php',
    'loader.php',
    'package.php'
  ],

  corePublishSrc: [
    'screenshots/**/*',
    'readme.txt'
  ],

  shouldBrowserify: true,

  coreBrowserifyDirs: [],

  coreBrowserifyRecursiveDirs: ['modules/**/assets/js/apps/']
};

builder.setProductConfig(productConfig);

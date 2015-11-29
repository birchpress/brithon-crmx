'use strict';

var React = require('react');
var Immutable = require('immutable');
var birchpress = require('birchpress');

var registrationAppComponent = null;

var ns = birchpress.provide('brithoncrmx.subscriptions.apps.admin.subscriptions', {

  __init__: function() {
    birchpress.addAction('birchpress.initFrameworkAfter', ns.run);
  },

  run: function() {}

});

module.exports = ns;

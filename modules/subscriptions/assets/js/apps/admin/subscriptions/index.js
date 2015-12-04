'use strict';

var React = require('react');
var Immutable = require('immutable');
var birchpress = require('birchpress');
var SubsciptionStore = require('brithoncrmx/subscriptions/stores/SubscriptionStore');

var subscriptionsAppComponent = null;

var ns = birchpress.provide('brithoncrmx.subscriptions.apps.admin.subscriptions', {

  __init__: function() {
    birchpress.addAction('birchpress.initFrameworkAfter', ns.run);
  },

  run: function() {
    var AccountInfoPanel = require('brithoncrmx/subscriptions/components/admin/subscriptions/AccountInfoPanel');
    var accountInfoContainer = document.getElementById('birchpress-account-info');
    var globalParams = brithoncrmx_subscriptions_apps_admin_subscriptions;
    var accountData = Immutable.fromJS({
      ajaxUrl: globalParams.ajax_url
    });
    var subscriptionStore = SubsciptionStore(accountData);

    function getProps() {
      return {
        store: subscriptionStore,
        cursor: subscriptionStore.getCursor()
      };
    }

    if (!subscriptionsAppComponent && accountInfoContainer) {
      subscriptionsAppComponent = React.render(
        React.createElement(AccountInfoPanel, getProps()),
        accountInfoContainer
      );
    }

    subscriptionStore.addAction('onChangeAfter', function() {
      subscriptionsAppComponent.setProps(getProps());
    });
  }

});

module.exports = ns;

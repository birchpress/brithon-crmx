'use strict';

var React = require('react');
var ImmutableRenderMixin = require('react-immutable-render-mixin');
var birchpress = require('birchpress');

var ReactMixinCompositor = birchpress.react.MixinCompositor;

var clazz = birchpress.provide('brithoncrmx.subscriptions.components.admin.subscriptions.AccountInfoPanel', {

  __mixins__: [ReactMixinCompositor],

  getReactMixins: function(component) {
    return [ImmutableRenderMixin];
  },

  renderLayer: function(component) {
    var BasicInfo = require('brithoncrmx/subscriptions/components/admin/subscriptions/BasicInfo');
    var CustomerInfo = require('brithoncrmx/subscriptions/components/admin/subscriptions/CustomerInfo');
    var store = component.props.store;
    var data = store.getCursor().get('accountInfo');

    if (!data) {
      store.getAccountInfo();
    }

    if (data) {
      var customerData = data.customer_data;
    }

    return (
      <div>
        <BasicInfo data={ data } />
        <CustomerInfo data={ customerData } />
      </div>
      );
  },

  render: function(component) {
    var adminPanel = component.renderLayer();
    return (<div id="admin_panel" key="admin_panel_div">
              { adminPanel }
            </div>);
  }

});

module.exports = clazz;

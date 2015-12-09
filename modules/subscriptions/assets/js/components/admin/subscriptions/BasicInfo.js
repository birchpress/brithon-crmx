'use strict';

var React = require('react');
var ImmutableRenderMixin = require('react-immutable-render-mixin');
var birchpress = require('birchpress');

var ReactMixinCompositor = birchpress.react.MixinCompositor;

var clazz = birchpress.provide('brithoncrmx.subscriptions.components.admin.subscriptions.BasicInfo', {

  __mixins__: [ReactMixinCompositor],

  getReactMixins: function(component) {
    return [ImmutableRenderMixin];
  },

  renderLayer: function(component) {
    if (!component.props.data) {
      return (<span />);
    }
    var data = component.props.data;
    return (
      <div>
        <p>
          <b>Your name</b>:&nbsp;
          { data.first_name }&nbsp;
          { data.last_name }
          <br /> <b>UID</b>:&nbsp;
          { data.uid }
          <br /> <b>Email</b>:&nbsp;
          { data.email }
          <br /> <b>Organization</b>:&nbsp;
          { data.organization }
        </p>
      </div>
      );
  },

  render: function(component) {
    var basicInfoPanel = component.renderLayer();
    return (<div id="basic_info" key="basic_info_div">
              <h3>Basic info</h3>
              { basicInfoPanel }
            </div>
      );
  }

});

module.exports = clazz;

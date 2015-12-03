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
      return <span />;
    }
    var data = component.props.data;
  },

  render: function(component) {
    var registerForm = component.renderLayer();
    return (<div id="reg" key="regdiv">
              <a
                 href="javascript:;"
                 role="button"
                 key="reglink"
                 onClick={ component.handleClick }>
                { component.__('Register') }
              </a>
              { registerForm }
            </div>
      );
  }

});

module.exports = clazz;

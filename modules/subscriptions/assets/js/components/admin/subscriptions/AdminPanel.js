'use strict';

var React = require('react');
var ImmutableRenderMixin = require('react-immutable-render-mixin');
var birchpress = require('birchpress');

var ReactMixinCompositor = birchpress.react.MixinCompositor;

var clazz = birchpress.provide('brithoncrmx.subscriptions.components.admin.subscriptions.AdminPanel', {

  __mixins__: [ReactMixinCompositor],

  getReactMixins: function(component) {
    return [ImmutableRenderMixin];
  },

  renderLayer: function(component) {
    var Button = require('brithoncrm/registration/components/common/Button');
    var Input = require('brithoncrm/registration/components/common/DataInput');
    var store = component.props.store;
    if (!component.state.shown) {
      return <span />;
    }
    return (
      <Modal onRequestClose={ component.handleClick }>
        <h2>{ component.__('Welcome to register') }</h2>
        <form>
          <div className="row">
            <Input
                   type="text"
                   name="first_name"
                   id=""
                   className="width-1-2"
                   placeholder={ component.__('First Name') }
                   value={ store.getCursor().get('first_name') }
                   onChange={ component.handleChange } />
            <Input
                   type="text"
                   name="last_name"
                   id=""
                   className="width-1-2"
                   placeholder={ component.__('Last Name') }
                   value={ store.getCursor().get('last_name') }
                   onChange={ component.handleChange } />
          </div>
          <div className="row">
            <Input
                   type="text"
                   name="email"
                   id=""
                   className=""
                   placeholder={ component.__('Email address') }
                   value={ store.getCursor().get('email') }
                   onChange={ component.handleChange } />
          </div>
          <div className="row">
            <Input
                   type="text"
                   name="org"
                   id=""
                   className="width-1-1"
                   placeholder={ component.__('Organization') }
                   value={ store.getCursor().get('org') }
                   onChange={ component.handleChange } />
          </div>
          <div className="row">
            <Input
                   type="password"
                   name="password"
                   id=""
                   className="width-1-1"
                   placeholder={ component.__('Password') }
                   value={ store.getCursor().get('password') }
                   onChange={ component.handleChange } />
          </div>
          <div className="row align-center">
            <Button
                    type="submit"
                    id=""
                    className=""
                    text={ component.__('Submit') }
                    onClick={ component.buttonClick } />
            <Button
                    type="reset"
                    id=""
                    className=""
                    text={ component.__('Reset') } />
          </div>
        </form>
      </Modal>
      );
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
            </div>);
  }

});

module.exports = clazz;

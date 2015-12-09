'use strict';

var React = require('react');
var ImmutableRenderMixin = require('react-immutable-render-mixin');
var birchpress = require('birchpress');

var ReactMixinCompositor = birchpress.react.MixinCompositor;

var clazz = birchpress.provide('brithoncrmx.subscriptions.components.admin.subscriptions.CustomerInfo', {

  __mixins__: [ReactMixinCompositor],

  getReactMixins: function(component) {
    return [ImmutableRenderMixin];
  },

  renderLayer: function(component) {
    if (!component.props.data) {
      return (<span />);
    }
    var data = component.props.data;
    var taxableAddresses = data.taxable_address.join(', ');
    var locationBlock;

    return (
      <div>
        <p>
          <b>Your location:</b>&nbsp;
          { data.city ? data.city : 'Unknown City' },&nbsp;
          { data.state ? data.state : 'Unknown State' },&nbsp;
          { data.country ? data.country : 'Unknown country' }
          <br /> <b>Post Code / Zipcode:</b>&nbsp;
          { data.postcode ? data.postcode : 'N/A' }
        </p>
        <p>
          <b>Address:</b>
          <br />
          { data.address ? data.address : 'N/A' }
          <br />
          { data.address_2 ? data.address : '' }
        </p>
        <p>
          <b>Shipping information:</b>
          <br />
          { data.shipping_address ? data.shipping_address : 'No shipping address' }
          <br />
          { data.shipping_address_2 ? data.shipping_address_2 : '' }
          <br />
          { data.shipping_city ? data.shipping_city : 'Unknown City' },&nbsp;
          { data.shipping_state ? data.shipping_state : 'Unknown State' },&nbsp;
          { data.shipping_country ? data.shipping_country : 'Unknown Country' }
          <br />
          { data.shipping_postcode ? data.shipping_postcode : '' }
        </p>
        <p>
          <b>Taxable addresses:</b>
          <br />
          { taxableAddresses }
        </p>
        <p>
          <b>Extra info</b>
          <br /><b>Is paying</b>:&nbsp;
          { data.is_paying ? 'Yes' : 'No' }
          <br /><b>Is outside your base country</b>:&nbsp;
          { data.is_outside_base ? 'Yes' : 'No' }
          <br /><b>Is your VAT exempted</b>:&nbsp;
          { data.is_vat_exempt ? 'Yes' : 'No' }
        </p>
      </div>
      );
  },

  render: function(component) {
    var customerInfoPanel = component.renderLayer();
    return (<div id="customer_info" key="customer_info_div">
              <h3>Customer Info</h3>
              { customerInfoPanel }
            </div>
      );
  }

});

module.exports = clazz;

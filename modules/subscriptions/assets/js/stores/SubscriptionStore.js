'use strict';

var birchpress = require('birchpress');
var Immutable = require('immutable');

var ImmutableStore = birchpress.stores.ImmutableStore;

var clazz = birchpress.provide('brithoncrmx.subscriptions.stores.SubscriptionStore', {

  __construct__: function(self, data) {
    var immutableStore = ImmutableStore(Immutable.fromJS(data));

    immutableStore.addAction('onChangeAfter', function(newCursor) {
      self.onChange();
    });
    self._immutableStore = immutableStore;
  },

  getCursor: function(self) {
    return self._immutableStore.getCursor();
  },

  onChange: function(self) {},

  getAccountInfo: function(self) {
    var url = self.getCursor().get('ajaxUrl');
    self.postApi(url, {
      action: 'brithoncrmx_get_user_info'
    }, function(err, r) {
      if (err) {
        alert(err.message);
      }

      self.getCursor().set('accountInfo', r);
    });
  },

  getSubscriptionInfo: function(self) {
    var url = self.getCursor().get('ajaxUrl');
    self.postApi(url, {
      action: 'brithoncrmx_get_user_subscriptions'
    }, function(err, r) {
      if (err) {
        alert(err.message);
      }

      self.getCursor().set('subscriptionInfo', r);
    });
  },

  _ajax: function(self, method, url, data, callback) {
    jQuery.ajax({
      type: method,
      url: url,
      data: data,
      dataType: 'json'
    }).done(function(r) {
      if (r && (r.message || r.error)) {
        return callback && callback(r);
      }
      return callback && callback(null, r);
    }).fail(function(jqXHR, textStatus) {
      return callback && callback({
          error: 'HTTP ' + jqXHR.status,
          message: 'Network error (HTTP ' + jqXHR.status + ')'
        });
    });
  },

  postApi: function(self, url, data, callback) {
    if (arguments.length === 2) {
      callback = data;
      data = {};
    }
    self._ajax('POST', url, data, callback);
  },

  __: function(self, string) {
    return self.getAttr('component').__(string);
  }
});

module.exports = clazz;
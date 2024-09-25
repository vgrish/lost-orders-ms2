window.__ = function (s, v) {
  if (s && s.charAt(0) === ':') {
    s = 'lost-orders-ms2.' + s.slice(1)
  }

  if (v != null && typeof v == 'object') {
    var t = '' + MODx.lang[s]
    for (var k in v) {
      t = t.replace('[[+' + k + ']]', v[k])
    }
    return t
  } else return MODx.lang[s]
}
var lostordersms2 = function (config) {
  config = config || {}
  lostordersms2.superclass.constructor.call(this, config)
}
Ext.extend(lostordersms2, Ext.Component, {
  page: {},
  window: {},
  grid: {},
  tree: {},
  panel: {},
  combo: {},
  field: {},
  config: {},
  view: {},
  tools: {},
})
Ext.reg('lostordersms2', lostordersms2)

lostordersms2 = new lostordersms2()

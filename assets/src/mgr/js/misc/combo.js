lostordersms2.combo.Search = function (config) {
  config = config || {}
  Ext.applyIf(config, {
    xtype: 'twintrigger',
    ctCls: 'x-field-search',
    allowBlank: true,
    msgTarget: 'under',
    emptyText: _('search'),
    name: 'query',
    triggerAction: 'all',
    clearBtnCls: 'x-field-search-clear',
    searchBtnCls: 'x-field-search-go',
    onTrigger1Click: this._triggerSearch,
    onTrigger2Click: this._triggerClear,
  })
  lostordersms2.combo.Search.superclass.constructor.call(this, config)
  this.on('render', function () {
    this.getEl().addKeyListener(
      Ext.EventObject.ENTER,
      function () {
        this._triggerSearch()
      },
      this,
    )
  })
  this.addEvents('clear', 'search')
}
Ext.extend(lostordersms2.combo.Search, Ext.form.TwinTriggerField, {
  initComponent: function () {
    Ext.form.TwinTriggerField.superclass.initComponent.call(this)
    this.triggerConfig = {
      tag: 'span',
      cls: 'x-field-search-btns',
      cn: [
        {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
        {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls},
      ],
    }
  },

  _triggerSearch: function () {
    this.fireEvent('search', this)
  },

  _triggerClear: function () {
    this.fireEvent('clear', this)
  },
})
Ext.reg('lostordersms2-combo-search', lostordersms2.combo.Search)
Ext.reg('lostordersms2-field-search', lostordersms2.combo.Search)

lostordersms2.combo.DateTime = function (config) {
  config = config || {}

  Ext.applyIf(config, {
    timePosition: 'right',
    allowBlank: true,
    hiddenFormat: 'Y-m-d H:i:s',
    dateFormat: MODx.config['manager_date_format'] || 'Y-m-d',
    timeFormat: 'H:i',
    cls: 'date-combo',
    ctCls: 'date-combo',
    timeWidth: 90,
    dateWidth: 120,
    timeIncrement: 60,
    selectOnFocus: true,
  })

  lostordersms2.combo.DateTime.superclass.constructor.call(this, config)
}
Ext.extend(lostordersms2.combo.DateTime, Ext.ux.form.DateTime, {})
Ext.reg('lostordersms2-combo-datetime', lostordersms2.combo.DateTime)

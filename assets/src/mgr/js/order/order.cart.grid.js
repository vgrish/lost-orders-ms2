lostordersms2.grid.OrderCart = function (config) {
  config = config || {}
  config.compact = config.compact || false

  this.sm = new Ext.grid.CheckboxSelectionModel()

  var columns = this.getColumns(config)

  Ext.applyIf(config, {
    url: lostordersms2.config['connectorUrl'],
    baseParams: {
      action: 'Order/Cart/GetList',
      cart: 1,
      uuid: config.uuid || 0,
      rendered: false,
    },
    autosave: false,
    fields: this.getFields(config),
    columns: columns,
    tbar: this.getTopBar(config),
    listeners: this.getListeners(config),

    sm: this.sm,

    plugins: [],

    clicksToEdit: 0,
    autoHeight: true,

    paging: true,
    pageSize: 20,
    remoteSort: true,
    remoteGroup: true,

    stateful: false,
    sortBy: 'created_at',
    sortDir: 'DESC',

    viewConfig: {
      forceFit: true,
      enableRowBody: true,
      autoFill: true,
      showPreview: true,
      scrollOffset: 0,
    },
    cls: 'lostordersms2-grid main-wrapper modx-grid-small',
    bodyCssClass: 'grid-with-buttons',
    storeBaseParams: {},
  })
  lostordersms2.grid.OrderCart.superclass.constructor.call(this, config)
}
Ext.extend(lostordersms2.grid.OrderCart, MODx.grid.Grid, {
  _loadStore: function () {
    this.store = new Ext.data.JsonStore({
      url: this.config.url,
      baseParams: this.config.baseParams || {action: this.config.action || 'getList'},
      fields: this.config.fields,
      root: 'results',
      totalProperty: 'total',
      idProperty: 'key',
      remoteSort: this.config.remoteSort || false,
      storeId: this.config.storeId || Ext.id(),
      autoDestroy: true,
    })
  },

  windows: {},
  groupField: '',

  getExcludeFields: function (config) {
    var fields = ['actions']

    return fields
  },

  getFields: function (config) {
    config = config || {}

    var fields = lostordersms2.tools.cloneArray(lostordersms2.config.grid_order_cart_fields || [])
    Ext.iterate(config.excludeColumnFields || [], function (field, i) {
      fields.remove(field)
    })
    fields.push('actions')

    return fields
  },

  getTopBarComponent: function (config) {
    return []
  },

  getTopBar: function (config) {
    return []
  },

  getColumns: function (config) {
    var columns = [
      {
        id: 'exp',
        width: 40,
        header: '',
        tpl: '',
        hidden: false,
        sortable: false,
        dataIndex: 'key',
        fixed: true,
        hideable: false,
        menuDisabled: true,
        editor: false,
        lazyRender: true,
        enableCaching: true,
        idProperty: 'idx',
        renderer: function (value, metaData, record, rowIndex) {
          return rowIndex + 1
        },
      },
    ]
    var add = {
      options: {
        width: 100,
        renderer: function (value) {
          return JSON.stringify(value, null, 2)
        },
      },
      id: {
        width: 50,
        renderer: function (value, metaData, record) {
          if (!value) {
            return ''
          }

          var pagetitle = record['json']['pagetitle'] || 'unknown';
          let s = lostordersms2.tools.resourceLink(pagetitle, value, true)


          if (pagetitle) {
            s = String.format('<span class="resource"">({0})</span> ', value) + s;
          }
          return s
        },
      },
    }

    var fields = this.getFields(config)

    Ext.iterate(fields, function (field, i) {
      if (field === 'actions') {
        return false
      }

      add[field] = Ext.apply(
        {
          dataIndex: field,
          sortable: false,
          header: __(':models.product.' + field) ? __(':models.product.' + field) : field,
          tooltip: __(':models.product.' + field + '_desc'),
          width: 15,
        },
        add[field] || {},
      )
    })

    Ext.iterate(fields, function (field, i) {
      if (add[field]) {
        columns.push(add[field])
      }
    })

    return columns
  },

  getListeners: function (config) {
    return {}
  },
})
Ext.reg('lostordersms2-grid-order-cart', lostordersms2.grid.OrderCart)

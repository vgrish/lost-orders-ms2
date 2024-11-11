lostordersms2.grid.Order = function (config) {
  config = config || {}
  config.compact = config.compact || false

  this.sm = new Ext.grid.CheckboxSelectionModel()

  var columns = this.getColumns(config)

  Ext.applyIf(config, {
    url: lostordersms2.config['connectorUrl'],
    baseParams: {
      action: 'Order/GetList',
      order: config.order || 0,
      rendered: false,
    },
    save_action: 'Order/updatefromgrid',
    autosave: true,
    save_callback: this._updateRow,
    fields: this.getFields(config),
    columns: columns,
    tbar: this.getTopBar(config),
    listeners: this.getListeners(config),

    sm: this.sm,

    plugins: [
      /*this.filter,*/
      /*this.exp*/
    ],

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
  lostordersms2.grid.Order.superclass.constructor.call(this, config)

  var grid = this
  this.store.on('load', function (store, records, options) {
    grid.storeBaseParams = options.params
    if (!config.compact && grid.rendered) {
      let params = Ext.apply(
        {},
        {
          action: 'Order/GetStat',
          start: null,
          limit: null,
          sort: null,
          dir: null,
        },
        options.params,
      )

      Object.keys(params).forEach((key) => {
        if (params[key] === null) {
          delete params[key]
        }
      })
      let statCacheKey = lostordersms2.tools.hashCode(JSON.stringify(params))
      if (store.statCacheKey && store.statCacheKey === statCacheKey) {
        return
      }

      MODx.Ajax.request({
        url: lostordersms2.config['connectorUrl'],
        params: params,
        listeners: {
          success: {
            fn: function (r) {
              store.statCacheKey = statCacheKey
              this.updateGridStat(r.results)
            },
            scope: grid,
          },
        },
      })
    }
  })
}
Ext.extend(lostordersms2.grid.Order, MODx.grid.Grid, {
  windows: {},
  groupField: '',

  getExcludeFields: function (config) {
    var fields = ['actions']

    return fields
  },

  getFields: function (config) {
    config = config || {}

    var fields = lostordersms2.tools.cloneArray(lostordersms2.config.grid_order_fields || [])
    Ext.iterate(config.excludeColumnFields || [], function (field, i) {
      fields.remove(field)
    })
    fields.push('actions')

    return fields
  },

  getTopBarComponent: function (config) {
    config = config || {}
    if (config.compact) {
      return []
    }

    var fields = ['menu', 'create', 'update', 'group', 'start', 'end', 'stat', 'left', 'search', 'spacer']

    Ext.iterate(config.excludeTopBarFields || [], function (field, i) {
      fields.remove(field)
    })

    return fields
  },

  getTopBar: function (config) {
    var tbar = []

    var add = {
      menu: {
        text: '<i class="icon icon-cogs"></i> ',
        menu: [
          {
            text: '<i class="icon icon-rotate-right"></i> ' + __(':actions.load'),
            cls: 'lostordersms2-cogs',
            handler: this.loadOrder,
            scope: this,
          },
          '-',
          {
            text: '<i class="icon icon-trash-o"></i> ' + __(':actions.truncate'),
            cls: 'lostordersms2-cogs',
            handler: this.truncate,
            scope: this,
          },
        ],
      },
      update: {
        text: '<i class="icon icon-refresh"></i>',
        handler: this._updateRow,
        scope: this,
      },

      left: '->',

      start: {
        xtype: 'lostordersms2-combo-datetime',
        name: 'processed_at_from',
        custm: true,
        clear: true,
        width: 215,
        value: new Date(lostordersms2.config.grid_order_processed_at_from || '2024').setHours(0, 0, 0, 0),
        listeners: {
          select: {
            fn: this._filterByCombo,
            scope: this,
          },
          change: {
            fn: this._filterByCombo,
            scope: this,
          },
          afterrender: {
            fn: this._filterByCombo,
            scope: this,
          },
        },
      },
      end: {
        xtype: 'lostordersms2-combo-datetime',
        custm: true,
        clear: true,
        name: 'processed_at_to',
        width: 215,
        value: new Date().setHours(23, 59, 59, 0),
        listeners: {
          change: {
            fn: this._filterByCombo,
            scope: this,
          },
          select: {
            fn: this._filterByCombo,
            scope: this,
          },
          afterrender: {
            fn: this._filterByCombo,
            scope: this,
          },
        },
      },
      search: {
        xtype: 'lostordersms2-field-search',
        width: 200,
        listeners: {
          search: {
            fn: function (field) {
              this._doSearch(field)
            },
            scope: this,
          },
          clear: {
            fn: function (field) {
              field.setValue('')
              if (this.filter) {
                this.filter.clearFilters()
              }
              this._clearSearch()
            },
            scope: this,
          },
        },
      },
      spacer: {
        xtype: 'spacer',
        style: 'width:1px;',
      },
      stat: {
        xtype: 'displayfield',
        cls: 'lostordersms2-grid-info',
        id: 'lostordersms2-order-stat',
        html: '',
      },
    }

    var fields = this.getTopBarComponent(config)
    Ext.iterate(fields, function (field, i) {
      if (add[field]) {
        tbar.push(add[field])
      }
    })

    return tbar
  },

  getColumns: function (config) {
    //var columns = [this.exp, this.sm];
    var columns = [
      {
        id: 'exp',
        width: 40,
        header: '<span class="lostordersms2-grid-row-expander" action="expandAll">&#160;</span>',
        tpl: '',
        hidden: false,
        sortable: false,
        dataIndex: '',
        fixed: true,
        hideable: false,
        menuDisabled: true,
        editor: false,
        lazyRender: true,
        enableCaching: true,
        renderer: function () {
          return String.format(
            '<span class="lostordersms2-grid-row-expander" action="expand" title="">&#160;</span><span class="lostordersms2-content-expand" hidden="true"></span>',
          )
        },
      },
    ]

    var add = {
      uuid: {
        width: 80,
        hidden: true,
      },
      session_id: {
        width: 60,
        hidden: true,
      },
      created_at: {
        hidden: true,
      },
      updated_at: {
        hidden: true,
      },
      user_id: {
        width: 100,
        renderer: function (value, metaData, record) {
          var Profile = record['json']['profile'] || {}

          var fullname = Profile['fullname'],
            email = Profile['email'],
            s = lostordersms2.tools.userLink(fullname, record['json']['user_id'], true)

          if (email) {
            s = s + String.format(' <span class="lostordersms2-row-badge"">{0}</span>', email)
          }
          return s
        },
      },
      msorder_id: {
        width: 30,
        renderer: function (value, metaData, record) {
          if (!value) {
            return ''
          }
          var Order = record['json']['order'] || {},
            num = Order['num']

          let color = Order && Order['color'] ? Order['color'] : 'CACACA',
            textColor = '000000'
          if (color) {
            // HEX to RGB
            var r = (g = b = 0)
            r = '0x' + color[0] + color[1]
            g = '0x' + color[2] + color[3]
            b = '0x' + color[4] + color[5]

            r /= 255
            g /= 255
            b /= 255

            // RGB to HEX
            let cmin = Math.min(r, g, b),
              cmax = Math.max(r, g, b),
              delta = cmax - cmin,
              h = (s = l = 0)

            if (delta == 0) {
              h = 0
            } else if (cmax == r) {
              h = ((g - b) / delta) % 6
            } else if (cmax == g) {
              h = (b - r) / delta + 2
            } else {
              h = (r - g) / delta + 4
            }

            h = Math.round(h * 60)

            if (h < 0) {
              h += 360
            }

            l = (cmax + cmin) / 2
            s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1))
            s = +(s * 100).toFixed(1)
            l = +(l * 100).toFixed(1)

            textColor = l > 50 ? '000000' : 'FFFFFF'
          }
          let qtip = Order['name'] || ''

          return String.format(
            '<span class="lostordersms2-row-badge" ext:qtip="{3}" style="background-color:#{0};color:#{1}">{2}</span>',
            color,
            textColor,
            lostordersms2.tools.orderLink(num || value, value, true),
            qtip,
          )

          return lostordersms2.tools.orderLink(num || value, value, true)
        },
      },

      cart_total_cost: {
        renderer: function (value, metaData, record) {
          return lostordersms2.tools.Money(value)
        },
      },

      visits: {
        width: 30,
        renderer: function (value, metaData, record) {
          let textValue = value

          let backColor = value === true ? '' : '2f9bf9',
            textColor = value === true ? '000000' : 'fff'

          let qtip = record['json']['visit_at'] || ''

          return String.format(
            '<span class="lostordersms2-row-badge" ext:qtip="{3}" style="background-color:#{0};color:#{1}">{2}</span>',
            backColor,
            textColor,
            textValue,
            qtip,
          )
        },
      },
      completed: {
        width: 30,
        renderer: function (value, metaData, record) {
          let textValue = value === true ? __(':models.order.completed_1') : __(':models.order.completed_0')

          let backColor = value === true ? '' : '2f9bf9',
            textColor = value === true ? '000000' : 'fff'

          let qtip = record['json']['updated_at'] || record['json']['created_at']

          return String.format(
            '<span class="lostordersms2-row-badge" ext:qtip="{3}" style="background-color:#{0};color:#{1}">{2}</span>',
            backColor,
            textColor,
            textValue,
            qtip,
          )
        },
      },
      sended: {
        renderer: function (value, metaData, record) {
          let prefixBadge = ''
          let textValue = value === true ? __(':models.order.sended_1') : __(':models.order.sended_0')

          let backColor = value === true ? '' : '2f9bf9',
            textColor = value === true ? '000000' : 'fff'

          let qtip = record['json']['sended_at'] || ''

          let again = record['json']['sended_again']
          if (again) {
            prefixBadge = 'x '
          }

          if (again && record['json']['sended_again_at']) {
            qtip += '<br>' + record['json']['sended_again_at']
          }

          return String.format(
            '<span class="lostordersms2-row-badge" ext:qtip="{4}" style="background-color:#{1};color:#{2}">{0}{3}</span>',
            prefixBadge,
            backColor,
            textColor,
            textValue,
            qtip,
          )
        },
      },
      generated: {
        renderer: function (value, metaData, record) {
          let textValue = value === true ? __(':models.order.generated_1') : __(':models.order.generated_0')

          let backColor = value === true ? '' : '2f9bf9',
            textColor = value === true ? '000000' : 'fff'

          let qtip = record['json']['generated_at'] || ''

          return String.format(
            '<span class="lostordersms2-row-badge" ext:qtip="{3}" style="background-color:#{0};color:#{1}">{2}</span>',
            backColor,
            textColor,
            textValue,
            qtip,
          )
        },
      },
      actions: {
        width: 10,
        header: '<i class="icon icon-cogs"></i>',
        tooltip: _('lostordersms2_actions'),
        renderer: lostordersms2.tools.renderActions,
        id: 'actions',
        sortable: false,
        hidden: true,
        groupable: false,
        hideable: false,
        menuDisabled: true,
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
          header: __(':models.order.' + field) ? __(':models.order.' + field) : field,
          tooltip: __(':models.order.' + field + '_desc'),
          width: 30,
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
    return Ext.applyIf(config.listeners || {}, {
      render: function (grid) {
        grid.getStore().baseParams['rendered'] = true
      },
    })
  },

  expandAll: function (grid, elem) {
    var hidden
    var cls = 'lostordersms2-expander-open'
    if (elem && elem.parentElement) {
      var classes = elem.parentElement.className.trim()
      var regex = new RegExp('\\b' + cls + '\\b')
      var hasOne = classes.match(regex)
      cls = cls.replace(/\s+/g, '')
      if (hasOne) {
        cls = classes.replace(regex, '')
        hidden = true
      } else {
        cls = classes + ' ' + cls
        hidden = false
      }
      elem.parentElement.className = cls
    }

    if (hidden === undefined) {
      return
    }

    var store = grid.getStore()
    var row, exp, expand
    for (i = 0; i <= store.getCount(); i++) {
      row = store.getAt(i)
      exp = Ext.fly(grid.view.getRow(i))?.child('td:nth(1) div.x-grid3-col-exp', true)
      if (exp) {
        expand = exp.classList.contains('lostordersms2-expander-open')
        if (hidden === false && expand === false) {
          this.expand(grid, exp.firstChild, row)
        } else if (hidden === true && expand === true) {
          this.expand(grid, exp.firstChild, row)
        }
      }
    }
  },

  expand: function (grid, elem, row) {
    var hidden
    var cls = 'lostordersms2-expander-open'

    if (elem && elem.nextElementSibling) {
      if (elem.nextElementSibling.hidden) {
        hidden = false
      } else {
        hidden = true
      }
      elem.nextElementSibling.hidden = hidden
    }

    if (elem && elem.parentElement) {
      var classes = elem.parentElement.className.trim()
      var regex = new RegExp('\\b' + cls + '\\b')
      var hasOne = classes.match(regex)
      cls = cls.replace(/\s+/g, '')
      if (hasOne) {
        cls = classes.replace(regex, '')
      } else {
        cls = classes + ' ' + cls
      }
      elem.parentElement.className = cls
    }

    var uuid = row.json['uuid']
    var expandId = 'expand-order-product-' + uuid
    var expandGridId = 'expand-grid-order-product-' + uuid

    if (Ext.get(expandId) == null) {
      let div = document.createElement('div')
      div.id = expandId
      div.className = 'lostordersms2-expand-grid'
      elem.closest('table').after(div)
    }

    Ext.get(expandId).dom.style.display = hidden === true ? 'none' : ''
    var expGrid = Ext.getCmp(expandGridId)
    if (expGrid) {
      if (expGrid.getEl()) {
        expGrid.getEl().remove()
      }
    }

    if (hidden === false) {
      expGrid = new lostordersms2.grid.OrderCart({
        renderTo: expandId,
        id: expandGridId,
        compact: true,
        cart: true,
        uuid: uuid || 0,
      })
      expGrid.getEl().swallowEvent(['contextmenu', 'click', 'dblclick', 'mousedown'])
    }
  },

  onClick: function (e) {
    var elem = e.getTarget()
    if (elem.nodeName === 'BUTTON') {
      var row = this.getSelectionModel().getSelected()
      if (typeof row != 'undefined') {
        var action = elem.getAttribute('action')
        if (action == 'showMenu') {
          var ri = this.getStore().find('uuid', row.uuid)
          return this._showMenu(this, ri, e)
        } else if (typeof this[action] === 'function') {
          this.menu.record = row.data
          return this[action](this, e)
        }
      }
    } else if (elem.nodeName === 'SPAN') {
      var row = this.getSelectionModel().getSelected()
      if (typeof row != 'undefined') {
        var action = elem.getAttribute('action')
        if (typeof this[action] === 'function') {
          this.menu.record = row.json
          return this[action](this, elem, row)
        }
      } else {
        var action = elem.getAttribute('action')
        if (typeof this[action] === 'function') {
          return this[action](this, elem)
        }
      }
    }

    return this.processEvent('click', e)
  },

  setAction: function (method, field, value) {
    var ids = this._getSelectedIds()
    if (!ids.length && field !== 'false') {
      return false
    }
    MODx.Ajax.request({
      url: lostordersms2.config.connectorUrl,
      params: {
        action: 'Order/Multiple',
        method: method,
        field_name: field,
        field_value: value,
        ids: Ext.util.JSON.encode(ids),
      },
      listeners: {
        success: {
          fn: function () {
            this.refresh()
          },
          scope: this,
        },
        failure: {
          fn: function (response) {
            MODx.msg.alert(_('error'), response.message)
          },
          scope: this,
        },
      },
    })
  },

  _filterByCombo: function (cb) {
    this.getStore().baseParams[cb.name] = cb.getValue()
    this.getBottomToolbar().changePage(1)
  },

  _doSearch: function (tf) {
    this.getStore().baseParams.query = tf.getValue()
    this.getBottomToolbar().changePage(1)
  },

  _clearSearch: function () {
    this.getStore().baseParams.query = ''
    this.getBottomToolbar().changePage(1)
  },

  _updateRow: function () {
    this.refresh()
  },

  _getSelectedIds: function () {
    var ids = []
    var selected = this.getSelectionModel().getSelections()

    for (var i in selected) {
      if (!selected.hasOwnProperty(i)) {
        continue
      }
      ids.push(selected[i]['json']['uuid'])
    }

    return ids
  },

  updateGridStat: function (data) {
    data = data ? data : []
    var stat = Ext.getCmp('lostordersms2-order-stat')
    if (!stat || !stat.rendered) {
      return
    }

    let output = ['<table><tr>']
    data.forEach((row) => {
      output.push(
        String.format(
          '<td><span ext:qtip="{2}">{1}</span><br>{0}</td>',
          row.name,
          lostordersms2.tools.Money(row.value),
          row.value_percent ? row.value_percent + '%' : '',
        ),
      )
    })
    output.push('</tr></table>')
    stat.update(output.join('\n'))
  },

  _showMenu: function (g, ri, e) {
    e.stopEvent()
    e.preventDefault()
    this.menu.recordIndex = ri
    this.menu.record = this.getStore().getAt(ri).json
    if (!this.getSelectionModel().isSelected(ri)) {
      this.getSelectionModel().selectRow(ri)
    }
    this.menu.removeAll()
    !this.getMenu || ((m = this.getMenu(g, ri, e)) && m.length && 0 < m.length && this.addContextMenuItem(m)),
      (!m || m.length <= 0) && this.menu.record.menu && this.addContextMenuItem(this.menu.record.menu),
      0 < this.menu.items.length && this.menu.showAt(e.xy)
  },

  getMenu: function (grid, rowIndex) {
    var ids = this._getSelectedIds()
    var row = grid.getStore().getAt(rowIndex)
    menu = lostordersms2.tools.getMenu(row.json['actions'], this, ids)
    this.addContextMenuItem(menu)
  },

  removeOrder: function () {
    Ext.MessageBox.confirm(
      __(':actions.remove'),
      __(':actions.confirm.remove'),
      function (val) {
        if (val == 'yes') {
          this.setAction('Order/Remove')
        }
      },
      this,
    )
  },

  viewOrder: function (btn, e) {
    var ids = this._getSelectedIds()
    uuid = ids && ids.length > 0 ? ids[0] : null
    if (!uuid) {
      return
    }

    let params = {
      action: 'Order/View',
      uuid: uuid,
    }

    var action = MODx.config['lost-orders-ms2.action_url']
    var query = new URLSearchParams(params)

    var url = action + (action.indexOf('?') > 0 ? '&' : '?') + query.toString()
    window.open(url)
  },

  sendOrder: function (btn, e) {
    this.setAction('Order/Send', false, false)
  },
  loadOrder: function (btn, e) {
    MODx.Ajax.request({
      url: lostordersms2.config['connectorUrl'],
      params: {
        action: 'Order/Load',
        HTTP_MODAUTH: MODx.siteId,
      },
      listeners: {
        success: {
          fn: function (response) {
            this._updateRow()
          },
          scope: this,
        },
        failure: {
          fn: function (response) {
            MODx.msg.alert(_('error'), response.message)
          },
          scope: this,
        },
      },
    })
  },

  turnOffOrder: function (btn, e) {
    this.setAction('Order/SetProperty', 'completed', 1)
  },

  turnOnOrder: function (btn, e) {
    this.setAction('Order/SetProperty', 'completed', 0)
  },

  truncate: function () {
    Ext.MessageBox.confirm(
      __(':actions.truncate'),
      __(':actions.confirm.truncate'),
      function (val) {
        if (val == 'yes') {
          MODx.Ajax.request({
            url: lostordersms2.config['connectorUrl'],
            params: {
              action: 'Order/Truncate',
            },
            listeners: {
              success: {
                fn: function () {
                  this.refresh()
                },
                scope: this,
              },
              failure: {
                fn: function (response) {
                  MODx.msg.alert(_('error'), response.message)
                },
                scope: this,
              },
            },
          })
        }
      },
      this,
    )
  },
})
Ext.reg('lostordersms2-grid-order', lostordersms2.grid.Order)

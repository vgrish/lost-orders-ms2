lostordersms2.panel.Main = function (config) {
  config = config || {}
  Ext.apply(config, {
    baseCls: 'modx-formpanel',
    cls: 'lostordersms2-formpanel',
    layout: 'anchor',
    hideMode: 'offsets',
    defaults: {collapsible: false, autoHeight: true},
    items: [
      {
        cls: 'lostordersms2-page-header',
        html: '',
      },
      {
        xtype: 'modx-tabs',
        id: 'lostordersms2-main-tabs',
        border: true,
        items: [
          {
            title: __(':models.order.title_many'),
            layout: 'anchor',
            id: 'orders',
            items: [
              {
                html: __(':models.order.intro'),
                bodyCssClass: 'panel-desc',
              },
              {
                xtype: 'lostordersms2-grid-order',
              },
            ],
          },
        ],
      },
    ],
  })
  lostordersms2.panel.Main.superclass.constructor.call(this, config)
}
Ext.extend(lostordersms2.panel.Main, MODx.Panel)
Ext.reg('lostordersms2-panel-main', lostordersms2.panel.Main)

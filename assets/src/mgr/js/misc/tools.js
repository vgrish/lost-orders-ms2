lostordersms2.tools.empty = function (value) {
  return (
    typeof value == 'undefined' ||
    value === 0 ||
    value === '0' ||
    value === '0.0' ||
    value === '0.00' ||
    value === '0.000' ||
    value === null ||
    value === false ||
    (typeof value == 'string' && value.replace(/\s+/g, '') == '') ||
    (typeof value == 'object' && value.length == 0)
  )
}

lostordersms2.tools.cloneArray = function (arr) {
  var i, copy

  if (Array.isArray(arr)) {
    copy = arr.slice(0)
    for (i = 0; i < copy.length; i++) {
      copy[i] = lostordersms2.tools.cloneArray(copy[i])
    }
    return copy
  } else if (typeof arr === 'object') {
    throw 'Cannot clone array containing an object!'
  } else {
    return arr
  }
}

lostordersms2.tools.renderActions = function (value, props, row) {
  var res = []
  var cls,
    icon,
    title,
    action,
    item = ''
  for (var i in row.data.actions) {
    if (!row.data.actions.hasOwnProperty(i)) {
      continue
    }
    var a = row.data.actions[i]
    if (!a['button']) {
      continue
    }

    cls = a['cls'] ? a['cls'] : ''
    icon = a['icon'] ? a['icon'] : ''
    action = a['action'] ? a['action'] : ''
    title = a['title'] ? a['title'] : ''

    item = String.format(
      '<li class="{0}"><button class="btn btn-default_ {1}" action="{2}" title="{3}"></button></li>',
      cls,
      icon,
      action,
      title,
    )

    res.push(item)
  }

  return String.format('<ul class="lostordersms2-row-actions">{0}</ul>', res.join(''))
}

lostordersms2.tools.getMenu = function (actions, grid, selected) {
  var menu = []
  var cls,
    icon,
    title,
    action = ''

  var has_delete = false
  for (var i in actions) {
    if (!actions.hasOwnProperty(i)) {
      continue
    }

    var a = actions[i]
    if (!a['menu']) {
      if (a == '-') {
        menu.push('-')
      }
      continue
    } else if (menu.length > 0 && /^sep/i.test(a['action'])) {
      menu.push('-')
      continue
    }

    if (selected.length > 1) {
      if (!a['multiple']) {
        continue
      } else if (typeof a['multiple'] === 'string') {
        a['title'] = a['multiple']
      }
    }

    cls = a['cls'] ? a['cls'] : ''
    icon = a['icon'] ? a['icon'] : ''
    title = a['title'] ? a['title'] : a['title']
    action = a['action'] ? grid[a['action']] : ''

    menu.push({
      handler: action,
      text: String.format('<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>', cls, icon, title),
      scope: grid,
    })
  }

  return menu
}

lostordersms2.tools.hashCode = function (s) {
  var hash = 0,
    i,
    chr
  if (s.length === 0) return hash
  for (i = 0; i < s.length; i++) {
    chr = s.charCodeAt(i)
    hash = (hash << 5) - hash + chr
    hash |= 0 // Convert to 32bit integer
  }
  return hash
}

lostordersms2.tools.Money = function (v) {
  v = Math.round((v - 0) * 100) / 100
  v = v == Math.floor(v) ? v + '.00' : v * 10 == Math.floor(v * 10) ? v + '0' : v
  v = String(v)
  var ps = v.split('.'),
    whole = ps[0],
    r = /(\d+)(\d{3})/
  while (r.test(whole)) {
    whole = whole.replace(r, '$1' + ' ' + '$2')
  }
  v = whole

  return v
}

lostordersms2.tools.userLink = function (value, id, blank, urlOnly) {
  if (!value) {
    return ''
  } else if (!id) {
    return value
  }

  var url = '?a=security/user/update&id=' + id
  if (urlOnly) {
    return url
  }

  return String.format('<a href="{0}" class="user-link" target="{1}">{2}</a>', url, blank ? '_blank' : '_self', value)
}

lostordersms2.tools.orderLink = function (value, id, blank, urlOnly) {
  if (!value) {
    return ''
  } else if (!id) {
    return value
  }

  var url = '?a=mgr/orders&namespace=minishop2&order=' + id
  if (urlOnly) {
    return url
  }

  return String.format('<a href="{0}" class="order-link" target="{1}">{2}</a>', url, blank ? '_blank' : '_self', value)
}

lostordersms2.tools.resourceLink = function (value, id, blank, urlOnly) {
  if (!value) {
    return ''
  } else if (!id) {
    return value
  }

  var url = 'index.php?a=resource/update&id=' + id
  if (urlOnly) {
    return url
  }

  return String.format(
    '<a href="{0}" class="resource-link" target="{1}">{2}</a>',
    url,
    blank ? '_blank' : '_self',
    value,
  )
}

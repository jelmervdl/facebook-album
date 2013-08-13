# viewport_width = $(window).width()
# ideal_height = parseInt $(window).height() / 2

class Photo
  constructor: (@el) ->
    @link = @el.querySelector 'a'
    @img = @el.querySelector 'img'

    @img.src = @link.href
    @img.setAttribute 'width', '100%'
    @img.setAttribute 'height', '' #auto
    
    @width = @link.getAttribute 'data-width'
    @height = @link.getAttribute 'data-height'
    @aspect_ratio = @width / @height

  resize: (dimensions) ->
    @el.style.width = "#{dimensions.width}px"
    @el.style.height = "#{dimensions.height}px"


order_photos = (photos, viewport_width, ideal_height) ->
  summed_width = photos.reduce ((sum, p) -> sum += p.aspect_ratio * ideal_height), 0
  rows = Math.round summed_width / viewport_width

  console.assert (not isNaN summed_width), 'summed_width is a real value'
  console.assert (not isNaN rows), 'rows is a real value'

  if rows < 1
    # (2a) Fallback to just standard size 
    photos.forEach (photo) -> photo.resize
      width: parseInt ideal_height * photo.aspect_ratio
      height: ideal_height
  else
    # (2b) Distribute photos over rows using the aspect ratio as weight
    weights = photos.map (p) -> parseInt p.aspect_ratio * 100
    partition = linear_partition weights, rows

    # (3) Iterate through partition
    index = 0
    row_buffer = []
    partition.forEach (row) ->
      row_buffer = []
      row.forEach ->
        row_buffer.push photos[index++]
      summed_ratios = row_buffer.reduce ((sum, p) -> sum += p.aspect_ratio), 0
      row_buffer.forEach (photo) ->
        photo.resize
          width: parseInt viewport_width / summed_ratios * photo.aspect_ratio
          height: parseInt viewport_width / summed_ratios

gather_photos = ->
  new Photo li for li in document.querySelectorAll '.photo'
  
layout_photos = (photos) ->
  order_photos photos, window.innerWidth, parseInt window.innerHeight / 2

window.app_init = ->
  photos = gather_photos()

  layout_timeout = null
  window.onresize = ->
    clearTimeout layout_timeout
    layout_timeout = setTimeout (-> layout_photos photos), 50

  layout_photos photos
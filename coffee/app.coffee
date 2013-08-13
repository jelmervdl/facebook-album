# viewport_width = $(window).width()
# ideal_height = parseInt $(window).height() / 2

class Photo
  constructor: (@el) ->
    @link = @el.querySelector 'a'
    @img = @el.querySelector 'img'

    @url = @link.href
    @thumbURL = @img.src;

    @img.src = @url
    @img.setAttribute 'width', '100%'
    @img.setAttribute 'height', '' #auto
    
    @width = @link.getAttribute 'data-width'
    @height = @link.getAttribute 'data-height'
    @aspect_ratio = @width / @height

  caption: ->
    @link.querySelector('figcaption').innerHTML

  resize: (dimensions) ->
    @el.style.width = "#{dimensions.width}px"
    @el.style.height = "#{dimensions.height}px"


class Viewer
  constructor: (@photos) ->    
    @el = document.getElementById 'overlay'

    @image = @el.querySelector '.full-size'
    @background = @el.querySelector '.background'
    @caption = @el.querySelector 'figcaption'

    @nextButton = @el.querySelector '.next-button'
    @prevButton = @el.querySelector '.prev-button'

    @el.addEventListener 'click', (evt) =>
      if evt.target == @el or evt.target == @image
        @hide()

    @nextButton.addEventListener 'click', (evt) =>
      evt.preventDefault()
      @next()

    @prevButton.addEventListener 'click', (evt) =>
      evt.preventDefault()
      @prev()

    document.addEventListener 'keyup', (evt) =>
      if @el.classList.contains 'visible'
        if evt.keyCode == 27
          evt.preventDefault()
          @hide()

        if evt.keyCode == 39
          evt.preventDefault()
          @next()

        if evt.keyCode == 37
          evt.preventDefault()
          @prev()

  show: (photo) ->
    @current = @photos.indexOf photo
    console.assert (@current != -1), 'Photo not found in list'
    @render()

  hide: ->
    @el.classList.remove 'visible'

  next: ->
    @current++
    @render()

  prev: ->
    @current--
    @render()

  render: ->
    photo = @photos[@current]

    @el.className = if photo.aspect_ratio > 1 then 'horizontal' else 'vertical'

    @image.src = photo.url
    @background.src = photo.thumbURL
    @caption.innerHTML = photo.caption()

    @nextButton.disabled = @current == @photos.length - 1
    @prevButton.disabled = @current == 0

    @el.classList.add 'visible'


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
  viewer = new Viewer photos

  # Listen for clicks on photo's
  for photo in photos
    do (photo) ->
      photo.link.addEventListener 'click', (evt) ->
        evt.preventDefault();
        viewer.show photo

  layout_timeout = null
  window.onresize = ->
    clearTimeout layout_timeout
    layout_timeout = setTimeout (-> layout_photos photos), 50

  layout_photos photos
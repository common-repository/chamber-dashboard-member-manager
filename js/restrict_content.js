jQuery(document).ready(function ($) {
  var wWidth = $(window).width();
  var dWidth = wWidth * 0.8;
  var wHeight = $(window).width();
  var dHeight = wHeight * 0.8;
  $( "#cdash_page_display_error" ).dialog({
    modal: true,
    position: { my: 'top', at: 'top+150' },
    width: dWidth,
    height:dHeight
  });
});


/*
 * Dismiss alert messages
 */
$( "alert alert-success" ).on( "click", "[data-dismiss]", function( event ) {
      $(this).remove();
});

/*
 * Hover panel slide
 */
$( "[data-slide-block]" ).hover(
  function() {
  	$( this ).find("[data-slide-content]").stop().animate({ marginBottom: '0px'}, 500)
  	$( this ).find("[data-slide-title]").stop().animate({ marginTop: '-150px'}, 500);
  }, function() {
  	$( this ).find("[data-slide-content]").stop().animate({ marginBottom: '-150px'}, 500);
  	$( this ).find("[data-slide-title]").stop().animate({ marginTop: '0px'}, 500);
  }
);
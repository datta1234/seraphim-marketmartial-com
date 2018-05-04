
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


$( "[data-not-listed-check]" ).on("change", function( event ) {
	if ($("#new_organistation").attr("type") == "hidden") {
		$("#new_organistation").attr("type", "");
	} else {
		$("#new_organistation").attr("type", "hidden");
	}
});

/*
 * Enable Tooltips
 */
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
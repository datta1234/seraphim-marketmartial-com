$( document ).ready(function() {
    
  /*
   * Dismiss alert messages
   */
  $( "alert alert-success" ).on( "click", "[data-dismiss]", function( event ) {
        $(this).remove();
  });

  /*
   * Enable Tooltips
   */
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  /*
   * Hover panel slide
   */
  /*$( "[data-slide-block]" ).hover(
    function() {
      $( this ).find("[data-slide-content]").stop().animate({ marginBottom: '0px'}, 500)
      $( this ).find("[data-slide-title]").stop().animate({ marginTop: '-150px'}, 500);
    }, function() {
      $( this ).find("[data-slide-content]").stop().animate({ marginBottom: '-150px'}, 500);
      $( this ).find("[data-slide-title]").stop().animate({ marginTop: '0px'}, 500);
    }
  );*/

  /*
   * Register - Toggle organisation input state
   */
  $( "[data-not-listed-check]" ).on("change", function( event ) {
    var org_check = $("#new_organistation");
    var org_drop = $("#organisation");
    if (org_check.attr("type") == "hidden") {
      org_drop.prop( "disabled", true );
      org_check.prop( "disabled", false );
      org_check.attr("type", "");
    } else {
      org_check.attr("type", "hidden");
      org_check.prop( "disabled", true );
      org_drop.prop( "disabled", false );
    }
  });

  /*
   * Register - password and confirm match
   */
  var equality_check = false;
  var passElement = $( "#password" );
  var confirmElement = $( "#password-confirm" );

  passElement.on( "input", function() {
    if ( confirmElement && $( this ) ) {
      if ( $( this ).val() == confirmElement.val() && $( this ).val() != '' ) {
        //add class is-valid
        confirmElement.removeClass("is-invalid");
        confirmElement.addClass("is-valid");
        equality_check = true;
      } else {
        confirmElement.removeClass("is-valid");
        confirmElement.addClass("is-invalid");
        equality_check = false;
      }
    }
  });

  confirmElement.on( "input", function() {
    if (passElement && $( this )) {
      if ( $( this ).val() == passElement.val() && $( this ).val() != '' ) {
        $( this ).removeClass("is-invalid");
        $( this ).addClass("is-valid");
        equality_check = true;
      } else {
        $( this ).removeClass("is-valid");
        $( this ).addClass("is-invalid");
        equality_check = false;
      }
    }
  })

});
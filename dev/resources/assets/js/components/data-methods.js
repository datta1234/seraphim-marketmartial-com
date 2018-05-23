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
    * Toggle theme classes
    */
    $( "[data-toggle-theme]" ).on("change", function( event ) {
        $( '#trade_app' ).find( "[data-theme-wrapper]" ).toggleClass( "light-theme" ).toggleClass( "dark-theme" );

    });


});
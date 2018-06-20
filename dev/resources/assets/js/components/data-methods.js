$( document ).ready(function() {
    
    /*/**
     * Dismiss alert messages
     */
    $( "alert alert-success" ).on( "click", "[data-dismiss]", function( event ) {
        $(this).remove();
    });

    /**
     * Enable Tooltips
     */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    /**
     * Toggle theme classes
     */
    $( "[data-toggle-theme]" ).on("change", function( event ) {
        $( '#trade_app' ).find( "[data-theme-wrapper]" ).toggleClass( "light-theme" ).toggleClass( "dark-theme" );
    });

    /**
     * Toggle active Nav class
     */
    $(function() {
        let currentUrl = window.location.href;
        console.log(currentUrl);
        $( ".nav > .nav-item " ).removeClass('active');
        $( ".nav > .nav-item " ).find('a[href="'+ currentUrl +'"]').parent().addClass('active');
    });
});
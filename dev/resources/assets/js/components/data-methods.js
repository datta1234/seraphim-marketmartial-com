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
        $( ".nav > .nav-item " ).removeClass('active');
        $( ".nav > .nav-item " ).find('a[href="'+ currentUrl +'"]').parent().addClass('active');
    });

    /**
     * Checks if the passed check list is all true and enables form submit
     */
    function checkContactForm(check_list, form) {
        if(check_list.contact_name && check_list.contact_email && check_list.contact_message) {
            form.find('button[type="submit"]').prop("disabled", false);
        } else {
            form.find('button[type="submit"]').prop("disabled", true);
        }
    };
    
    /**
     * Disable contact form until all fields are filled in
     */
    $(function() {
        let form_check_list = {
            contact_name: false,
            contact_email: false,
            contact_message: false
        };
        //Listens for input change on name input
        $( '#ContactUsForm' ).find('input#name').on("input", function() {
            console.log("name Type");
            form_check_list.contact_name = ( $(this).val() != '' )? true: false;
            checkContactForm(form_check_list, $('#ContactUsForm'));
        });
        //Listens for input change on email input
        $( '#ContactUsForm' ).find('input#contactEmail').on("input", function() {
            console.log("contact Type");
            form_check_list.contact_email = ( $(this).val() != '' )? true: false;
            checkContactForm(form_check_list, $('#ContactUsForm'));
        });
        //Listens for input change on message textarea
        $( '#ContactUsForm' ).find('textarea#message').on("input", function() {
            console.log("message Type");
            form_check_list.contact_message = ( $(this).val() != '' )? true: false;
            checkContactForm(form_check_list, $('#ContactUsForm'));
        });
    });

    $(function() {
        if(window.location.href.indexOf("#ContactUsForm") !== -1) {
            document.getElementById("name").focus();
        }
    });
});
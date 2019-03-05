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
     * Toggle active Nav class
     */
    $(function() {
        let currentUrl = window.location.href;
        $( ".nav > .nav-item " ).removeClass('active');
        $( ".nav > .nav-item " ).find('a[href="'+ currentUrl +'"]').parent().addClass('active');
    });

    
    /**
     * Disable contact form until all fields are filled in
     */
    $(function() {
        let form_check_list = {
            contact_name: false,
            contact_email: false,
            contact_message: false
        };
        /**
         * Checks if the passed check list is all true and enables form submit
         */
        function checkContactForm(form) {
            form_check_list.contact_name = ( form.find('input#name').val() != '' )? true: false;
            form_check_list.contact_email = ( form.find('input#contactEmail').val() != '' )? true: false;
            form_check_list.contact_message = ( form.find('textarea#contact_message').val() != '' )? true: false;
            if(form_check_list.contact_name && form_check_list.contact_email && form_check_list.contact_message) {
                form.find('button[type="submit"]').prop("disabled", false);
            } else {
                form.find('button[type="submit"]').prop("disabled", true);
            }
        };
        //Listens for input change on name input
        $( '#ContactUsForm' ).find('input#name').on("input", function() {
            checkContactForm($('#ContactUsForm'));
        });
        //Listens for input change on email input
        $( '#ContactUsForm' ).find('input#contactEmail').on("input", function() {
            checkContactForm($('#ContactUsForm'));
        });
        //Listens for input change on message textarea
        $( '#ContactUsForm' ).find('textarea#contact_message').on("input", function() {
            checkContactForm($('#ContactUsForm'));
        });

        //Focus form on failed request
        if(window.location.href.indexOf("#ContactUsForm") !== -1) {
            checkContactForm($('#ContactUsForm'));
            document.getElementById("name").focus();
        }
    });

    $('.alert').on('close.bs.alert', function () {
        $(this).addClass('alert-ease-out');
    })
});
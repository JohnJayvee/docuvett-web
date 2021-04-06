<?php
/**
 * Template Name: Event Registration Thank You
 */

get_header();
?>

<section id="primary" class="content-area">
    <main id="main" class="site-main">
        <div id="wl-logo-wrapper"></div>

        <?php

        /* Start the Loop */
        while ( have_posts() ) :
            the_post(); ?>
            <h2 id="wl-title">Your Registration is confirmed</h2>
            <div id="ty-msg">
                <p style="margin: 50px 0 0">You will get an email shortly with the details of the seminar.</p>
            </div>
            <form>
                <input type="hidden" name="ref" value="<?php echo $_GET['ref']; ?>" />
                <?php if($_GET['lid']){ ?>
                    <input type="hidden" name="lead_id" value="<?php echo $_GET['lid']; ?>" />
                <?php } ?>
            </form>
        <?php

            // get_template_part( 'template-parts/content/content', 'page' );

            // If comments are open or we have at least one comment, load up the comment template.
            // if ( comments_open() || get_comments_number() ) {
            // 	comments_template();
            // }

        endwhile; // End of the loop.
        ?>

        <div id="wl-footer">
            <div class="email-wrapper"></div>
            <div class="phone-wrapper"></div>
            <div class="website-wrapper"></div>
        </div>

    </main><!-- #main -->
</section><!-- #primary -->

<style>
    #wl-logo-wrapper {
        padding: 20px;
    }
    #wl-logo-wrapper img {
        max-height: 60px;
    }
    .page-template-template-event-registration-thank-you form {
        position: relative;
        left: 50%;
        transform: translate(-50%, 0);
        display: inline-block;
        margin: 50px 0;
    }
    .page-template-template-event-registration form input[type="submit"] {
        margin: 20px auto;
        position: relative;
        display: block;
        background: rgb(0, 73, 132);
        color: white;
        border: none;
        padding: 20px 40px;
        border-radius: 5px;
    }
    .page-template-template-event-registration-thank-you h2 {
        text-align: center;
        margin: 20px 0 0;
    }
    h1:not(.site-title)::before, h2::before {
        display: none;
    }
    #wl-title {
        margin-top: 50px;
    }
    .page-template-template-event-registration-thank-you header,
    .page-template-template-event-registration-thank-you footer {
        display: none;
    }
    #wl-footer {
        color: white;
        text-align: center;
        padding: 50px 0;
        font-weight: bold;
    }
    #wl-footer > div {
        display: inline-block;
        margin: 0 50px;
    }
    #ty-msg {
        text-align: center;
        margin: 0 0 50px;
    }

    @media (max-width: 768px) {
        #registration-form {
            padding: 0 20px;
        }
        #registration-form input {
            margin: 0 0 10px 0;
            width: 100%;
        }
        #wl-footer {
            text-align: left;
        }
        #spoke-to {
            font-size: 30px;
            margin: 20px 0 20px;
        }
    }
</style>

<?php
get_footer(); ?>
<script>
    jQuery(document).ready(function( $ ) {
        var domain = 'http://localhost:8000';
        var portfolio = { agency: { logo: '', color_header: '', color_footer: '' } };
        var agency = [];
        var logo = '';
        var refInput = $('input[name=ref]').val();
        if(refInput){
            $.ajax({
                method: "GET",
                url: domain + "/api/portfolio/fetch/" + refInput,
            })
                .done(function( msg ) {
                    console.log( "Fetched agency: ", msg );
                    portfolio = msg;
                    if(portfolio.agency.logo){
                        $('#wl-logo-wrapper').append('<img src="'+ domain + portfolio.agency.logo + '" />');
                    } else {
                        portfolio.agency.logo = 'https://firstnationalassist.com.au/wp-content/uploads/2019/04/fn-assist-black_awning_logo.jpg';
                        $('#wl-logo-wrapper').append('<img src="' + portfolio.agency.logo + '" />');
                    }
                    if(portfolio.agency.email !== "null" && portfolio.agency.email !== null){
                        $('.email-wrapper').append('<span>' + portfolio.agency.email + '</span>');
                    }
                    if(portfolio.agency.phone !== "null" && portfolio.agency.phone !== null){
                        $('.phone-wrapper').append('<span>' + portfolio.agency.phone + '</span>');
                    }
                    if(portfolio.agency.website !== "null" && portfolio.agency.website !== null){
                        $('.website-wrapper').append('<span>' + portfolio.agency.website + '</span>');
                    }

                    if(portfolio.agency.color_header){
                        $("#wl-logo-wrapper").css("background-color", portfolio.agency.color_header);
                    } else {
                        portfolio.agency.color_header = '#2C2C2C';
                        $("#wl-logo-wrapper").css("background-color", portfolio.agency.color_header);
                    }

                    if(portfolio.agency.color_footer){
                        $("#wl-footer").css("background-color", portfolio.agency.color_footer);
                    } else {
                        portfolio.agency.color_footer = '#2C2C2C';
                        $("#wl-footer").css("background-color", portfolio.agency.color_footer);
                    }

                    // $("form").hide();
                    // $('#wl-title').html('Your registration is confirmed.')
                    // $('#ty-msg').html('<p style="margin: 50px 0 0">You will get an email shortly with the details of the seminar.</p>');
                })
                .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log('failed ajax');
                    fallbackToDefaultStyling();
                });
        } else {
            fallbackToDefaultStyling();
        }

        var seminars = [];
        $.ajax({
            method: "GET",
            url: domain + "/api/fetch-events"
        })
            .done(function( msg ) {
                console.log( "Fetched contacts: ", msg );
                seminars = msg;
                $.each(seminars, function( index, seminar ) {
                    $( "#seminar-radio-wrapper" ).append( "<div><input type='radio' id='seminar-"+ seminar.id +"' name='seminar' value='" + seminar.id + "'> <label for='seminar-"+ seminar.id +"'>"+ seminar.site.name +" - "+ seminar.start_date +" | "+ seminar.description +"</label></div>" );
                    // $( "#seminar-radio-wrapper" ).append( "" );
                });
            });

        function fallbackToDefaultStyling(){
            // no reference code, setting default theme values
            portfolio.agency.logo = 'https://firstnationalassist.com.au/wp-content/uploads/2019/04/fn-assist-black_awning_logo.jpg';
            $('#wl-logo-wrapper').append('<img src="'+ portfolio.agency.logo + '" />');

            portfolio.agency.color_header = '#2C2C2C';
            portfolio.agency.color_footer = '#2C2C2C';

            $("#wl-logo-wrapper").css("background-color", portfolio.agency.color_header);
            $("#wl-footer").css("background-color", portfolio.agency.color_footer);
        }


    });
</script>
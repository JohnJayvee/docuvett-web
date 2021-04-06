<?php
/**
 * Template Name: Event Registration
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
            <div id="spoke-to"></div>

            <div class="row">
                <div class="columns large-6 media-wrapper">
                    <div id="video-wrapper"></div>
                    <div id="invitation-wrapper"></div>
                </div>
                <div class="columns large-6">
                    <h2 id="wl-title">Seminar Registration</h2>
                    <div id="ty-msg"></div>
                    <form id="registration-form" action="" method="POST">

                        <?php if(!$_GET['lid']){ ?>
                            <input type="text" name="first_name" placeholder="First Name" class="if-required-input" required="true" />
                            <input type="text" name="last_name" placeholder="Last Name" class="if-required-input" required="true" />
                            <input type="email" name="email" placeholder="Email" class="if-required-input" required="true" />
                            <input type="number" name="phone" placeholder="Phone" class="if-required-input" required="true" />
                        <?php } else {} ?>
                        <input type="hidden" name="ref" value="<?php echo $_GET['ref']; ?>" />
                        <input type="hidden" name="seminar" value="" />
                        <?php if($_GET['lid']){ ?>
                            <input type="hidden" name="lead_id" value="<?php echo $_GET['lid']; ?>" />
                        <?php } ?>
                        <input type="hidden" name="reg_error" value="<?php echo $_GET['reg_error']; ?>" />

                        <label class="section-label tickets"> How many tickets do you require? </label>
                        <input id="tickets-required" type="number" name="tickets" value="1" min="1" max="10" placeholder="How many tickets do you require?" class="if-required-input" required="true" />

                        <div>
                            <ul id="extra-ticket-list"></ul>
                        </div>

                        <label class="section-label tickets"> How many tickets do you require for kids? </label>
                        <input id="tickets-kids-required" type="number" name="tickets_kids" value="0" min="0" max="10" placeholder="How many tickets do you require for kids?" class="if-required-input" required="true" />

                        <!-- js will insert event radio buttons here -->
                        <label class="section-label"> Events </label>

                        <ul id="seminar-radio-wrapper" class="row seminar-location no-bullet flex-center">

                            <!--<li class="seminar-location columns medium-4 selected">
                               <div class="bg-helper hic-image" style="background-image: url(https://firstnationalassist.com.au/wp-content/uploads/2019/04/investment.jpg)">
                                   <div class="location-title"><span>Static Event</span> 00-00-0000 <br> Selected Event Display</div>
                               </div>
                           </li>-->
                        </ul>
                        <div id="register-button" class="button btn">Register</div>
                    </form>
                </div>

            </div>


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

    .page-template-template-event-registration form,
    .page-template-template-event-registration-demo form{
        width: 750px;
    }
    #wl-logo-wrapper {
        padding: 20px;
    }
    #wl-logo-wrapper img {
        max-height: 60px;
    }
    .page-template-template-event-registration form,
    .page-template-template-event-registration-demo form{
        position: relative;
        left: 50%;
        transform: translate(-50%, 0);
        display: inline-block;
        margin: 20px 0 50px 0;
    }
    .page-template-template-event-registration form input[type="submit"],
    .page-template-template-event-registration-demo form input[type="submit"]{
        margin: 20px auto;
        position: relative;
        display: block;
        background: rgb(0, 73, 132);
        color: white;
        border: none;
        padding: 20px 40px;
        border-radius: 5px;
    }
    .page-template-template-event-registration h2,
    .page-template-template-event-registration-demo h2 {
        text-align: center;
        margin: 20px 0 0;
    }
    h1:not(.site-title)::before, h2::before {
        display: none;
    }
    .page-template-template-event-registration header,
    .page-template-template-event-registration footer,
    .page-template-template-event-registration-demo header,
    .page-template-template-event-registration-demo footer {
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
    #spoke-to {
        font-size: 50px;
        margin: 50px 0 50px;
        font-weight: bold;
        text-align: center;
    }

    #seminar-radio-wrapper li {
        cursor: pointer;
        padding: 0 8px;
        position: relative;
    }

    #seminar-radio-wrapper .hic-image {
        height: 170px;
        position: relative;
        transition: all 2.8s ease;
    }
    #seminar-radio-wrapper .hic-image:after {
        content: '';
        height: 100%;
        width: 100%;
        position: absolute;
        left: 0;
        top: 0;
        background-color: rgba(0,0,0,.35);
        transition: all .25s ease-in-out;

    }
    #registration-form .section-label {
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 16px;
    }

    #seminar-radio-wrapper li:before {
        color: #ffffff;
        font-family: 'Font Awesome 5 Free';
        font-size: 39px;
        position: absolute;
        top: -6px;
        content: '\f058';
        right: 16px;
        z-index: 2;
        opacity: 0;
        visibility: hidden;
        transform: scale(0.6);
        transition: all .25s cubic-bezier(0.45, 0.05, 0.55, 0.95);
    }
    #seminar-radio-wrapper li.selected:before {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }
    #seminar-radio-wrapper li.selected .hic-image:after,
    #seminar-radio-wrapper li:hover .hic-image:after {
        background-color: rgba(0,0,0,.65);
    }

    #seminar-radio-wrapper .location-title {
        color: #FFF;
        z-index: 1;
        text-align: center;
        padding: 0 24px;
        margin-top: auto;
        margin-bottom: auto;
        top: 0;
        bottom: 0;
        height: 55px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        left: 0;
        position: absolute;
        right: 0;
        font-weight: 600;
        font-size: 14px;
    }
    #seminar-radio-wrapper .location-title span {
        font-size: 18px;
        margin-bottom: 2px;
        font-weight: 700;
    }
    #registration-form select {
        background-color: #fff !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        border: 1px solid #eaeaea;
        padding-top: 14px;
        padding-bottom: 13px;
        height: auto;
    }
    .select-placeholder {
        color: #cacaca;
    }
    .select-placeholder option {
        color: #0a0a0a;
    }
    .media-wrapper {
        padding: 84px 0 0 0;
    }
    #video-wrapper iframe {
        width: 100%;
        min-height: 200px;
        margin: 0 0 20px;
        border: none;
    }
    #invitation-wrapper {
        max-width: 750px;
        margin: 0 auto;
    }
    #invitation-wrapper img {
        margin: 0 0 40px;
        border-radius: 6px;
    }
    #wl-title {
        text-align: center;
    }
    #registration-form .section-label.tickets {
        font-size: 18px;
    }
    #register-button.disabled {
        pointer-events: none;
    }
    #extra-ticket-list {
        list-style: none;
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
        #invitation-wrapper {
            padding: 0;
        }
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<?php
get_footer(); ?>
<script>
    jQuery(document).ready(function( $ ) {
        var domain = 'https://my.firstnationalassist.com.au'; // 'https://my.firstnationalassist.com.au';
        var portfolio = { agency: { logo: '', color_header: '', color_footer: '' } };
        var agency = [];
        var logo = '';
        var ref = $('input[name=ref]').val();
        var portfolioID = null;
        var agentID = null;
        var error = $('input[name=reg_error]').val();

        if(error == 'ar'){
            alert('You have already registered for this event. Please use a different email address or select a different event.');
        }

        if(error == 'nes'){
            alert('No event was selected, please select an event and try again.');
        }

        if(ref.indexOf('_') !== -1){
            // Agent
            var refArr = ref.split("_");
            var portfolioID = refArr[0];
            var agentID = refArr[1];
        } else {
            // Agency
            var portfolioID = ref;
        }

        // set form action, with correct environment domain
        // https://first-national.aws.hicalibertest.com.au/api/event-registration/create
        $('#registration-form').attr('action', domain + '/api/event-registration/create');
        $('#registration-form').on('click', '#register-button', function(){
            event.preventDefault();
            $('#register-button').addClass('disabled');
            //validate form
            var notValid = false;
            $('#registration-form input').each(function(index, input){
                if(input.name == 'extra_ticket' || input.name == 'reg_error'){
                    // it can stay empty
                } else if(input.name == 'phone') {
                    if(input.value.length < 8){
                        notValid = true;
                        $('#register-button').removeClass('disabled');
                        alert( "Phone field is required" );
                        return false;
                    }
                } else {
                    if(input.value.length < 1){
                        notValid = true;
                        console.log(input.name);
                        $('#register-button').removeClass('disabled');
                        alert( "You have not filled in all required fields" );
                        return false;
                    }
                }

            });

            let selectedAgent = $('#agent-select').val();
            if(selectedAgent < 1){
                $('#register-button').removeClass('disabled');
                alert( "Please select a referrer" );
                return false;
            }

            let preferred = $('input[name=seminar]').val();
            if(preferred > 0 && !notValid){
                $('#registration-form').submit();
            } else {
                $('#register-button').removeClass('disabled');
                alert( "Please select an event" );
            }
        });

        // trigger extra tickets
        var extraTicketsCreated = 0;
        $('#tickets-required').change(function(){
            calcTicketFields();
        });

        // init extra tickets
        calcTicketFields();

        function calcTicketFields(){
            var ticketCount = $('#tickets-required').val();
            var toCreate = (ticketCount - extraTicketsCreated) - 1;
            if(toCreate >= 0){
                var i;
                for (i = 0; i < toCreate; i++) {
                    console.log('create tickets');
                    $("#extra-ticket-list").append(ticketFields(extraTicketsCreated));
                    extraTicketsCreated++;
                }
            } else {
                // delete some ticket rows
                console.log('toCreate');
                console.log(toCreate);
                var i;
                for (i = 0; i > toCreate; i--) {
                    console.log('delete tickets');
                    $("#extra-ticket-list li").last().remove();
                    extraTicketsCreated--;
                }
            }
        }

        function ticketFields(id){
            var ticket = id + 1;
            return '<li id="ticket_'+ id +'"><div>Extra ticket '+ ticket +'</div><input type="text" name="extra_ticket_name_'+ id +'" placeholder="Name" /> <input type="email" name="extra_ticket_email_'+ id +'" placeholder="Email" /> <input type="text" name="extra_ticket_phone_'+ id +'" placeholder="Phone" /></li>';
        }

        if(portfolioID){
            $.ajax({
                method: "GET",
                url: domain + "/api/portfolio/fetch/" + portfolioID,
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

                    if(agentID){
                        portfolio.contacts.forEach(function(contact){
                            if(contact.id == agentID){
                                $('#spoke-to').append('<span>You were recently invited by <br>' + contact.name + '</span>');
                            }
                        });
                    } else {
                        $('#spoke-to').append('<span>You were recently invited by <br>' + portfolio.agency.display_name + '</span>');

                        $( '<select id="agent-select" class="select-placeholder" name="referred_by"><option disabled selected>Referred By</option></select>' ).insertAfter( "input[name=phone]" );
                        // $('#seminar-radio-wrapper').prepend('<div id="agent-select-wrapper"><select id="agent-select"></select></div>');
                        if (portfolio.agents) {
                            portfolio.agents.forEach(function (agent) {
                                $('#agent-select').append('<option value="' + agent.id + '">' + agent.name + '</option>');
                            });
                        }

                        $( "#registration-form" ).submit(function( event ) {
                            let preferred = $('input[name=seminar]').val();
                            if (preferred <= 0) {
                                alert("Please select an event");
                                event.preventDefault();
                            }
                        });
                    }
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
            url: domain + "/api/fetch-events?pid=" + portfolioID
        })
            .done(function( msg ) {
                console.log( "Fetched contacts: ", msg );
                seminars = msg.events;
                $.each(seminars, function( index, seminar ) {
                    date = seminar.start_date.split("-").reverse().join("-");

                    //Original Code

                    //$( "#seminar-radio-wrapper" ).append( "<div><input type='radio' id='seminar-"+ seminar.id +"' name='seminar' value='" + seminar.id + "' required='true'> <label for='seminar-"+ seminar.id +"'>"+ seminar.site.name +" - "+ date +" | "+ seminar.description +"</label></div>" );
                    // $( "#seminar-radio-wrapper" ).append( "" );

                    //Update from Dom needs Kyle dynamic and function to select and include it into hidden field name=seminar
                    if(seminar.background && seminar.background != 'null'){
                        var eventImg = domain + seminar.background;
                    } else {
                        var eventImg = "https://firstnationalassist.com.au/wp-content/uploads/2019/04/investment.jpg";
                    }
                    if(seminar.description.indexOf('Test') > -1) {
                        // console.log('triggered');
                    } else {
                        $( "#seminar-radio-wrapper" ).append( "<li class='seminar-location columns medium-6' data-seminar-id='"+seminar.id+"'><div class='bg-helper hic-image' style='background-image: url("+eventImg+")'><div class='location-title'><span>"+ seminar.site.name +" </span> "+ date +" <br> "+ seminar.description +"</div></li>" );
                    }
                });

                if(msg.invitation.value){
                    $('#invitation-wrapper').append('<img src="'+ domain + msg.invitation.value +'" />');
                } else {
                    $('#invitation-wrapper').append('<img src="'+ domain + msg.invitation +'" />');
                }

                if (msg.video) {
                    if (msg.video_type) {
                        if (msg.video_type == 'YouTube') {
                            $('#video-wrapper').append('<iframe src="https://www.youtube.com/embed/' + msg.video + '"></iframe>');

                            var width = $('#video-wrapper').width();
                            var height = width * 0.563;

                            $('#video-wrapper iframe').css('height', height);
                        }
                        if (msg.video_type == 'Vimeo') {
                            $('#video-wrapper').append('<iframe src="https://player.vimeo.com/video/' + msg.video + '"></iframe>');

                            var width = $('#video-wrapper').width();
                            var height = width * 0.575;

                            $('#video-wrapper iframe').css('height', height);
                        }
                    }
                }
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

        $('#seminar-radio-wrapper').on('click', '.seminar-location', function(){
            let seminarID = $(this).data("seminar-id");
            $('.seminar-location').removeClass('selected');
            $(this).addClass('selected');
            $('input[name=seminar]').val(seminarID);
        });

        $('#registration-form').on('change', '#agent-select', function(){
            let val = $("#agent-select :selected").val();
            if(val > 0){
                $(this).removeClass('select-placeholder');
            } else {
                $(this).addClass('select-placeholder');
            }
        });

    });
</script>
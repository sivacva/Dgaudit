<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit</title>

    <link rel="shortcut icon" href="../site/image/tn__logo.png" />
  
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/homestyle.css') }}">
   
    <!-- <link rel="stylesheet" href="{{ asset('site/js/ajax.min.js') }}"> -->
    <script src="../assets/js/jquery_3.7.1.js"></script>
    <style>
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    #pageWrapper {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }

    .content-wrapper {
        flex: 1;
        overflow: hidden;
    }

    #topbar {
        background-color: rgb(0, 63, 165, 0.8);
        font-size: 14px;
        padding: 0;
        color: rgba(255, 255, 255, 1);
        background-size: cover;
        background-position: top;
    }

    .fixed-top {
        z-index: 1030;
        background-color: #fff;
    }

    .text_header {
        color: #326a9a;
        font-size: 35px;
        text-shadow: 1px 1px #bbbbb7;
    }
   
    @media(max-width: 600px) {
        .image-header {
            margin-top: -2%;
        }

        .govt_head .g_row1 {
            padding: 1%;
        }

        .g_row1 [class*="col"]:nth-child(odd) {
            width: 28%;
        }

        .g_row1 [class*="col"]:nth-child(even) {
            width: 70%;
        }

        .g_row1 h4,
        .g_row1 h5,
        .g_row1 h6 {
            font-size: 12px;
        }

        .control-buttons {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .control-buttons button {
            margin: 0 5px;
        }

        .top-border {
            margin-top: 0%; /* Remove excessive margin for mobile */
        }

        .disclaimer-text {
        height: 100%;
        padding:8% 0%;
        margin-top:10%;
    }

    .header{
        margin-top:130%
    }

       
    }

    .fullscreen-center {
        flex: 1;
        display: flex;
        margin-top: 2%;
    }

    .disclaimer-text {
        height: 100%;
        
        
    }
    
    .header {
    margin-top: 3%;
}

/* Mobile overrides should come AFTER */
@media(max-width: 600px) {
    .header {
        margin-top: 23%;
    }
}


    footer {
        background-color: rgba(6, 39, 95, 0.8);
        color: white;
        padding-top: 20px;
        padding-bottom: 20px;
        text-align: center;
        width: 100%;
        margin-top: auto;
    }
    .additional {
    font-weight: bold; /* Make text bold */
}

a.additional {
    font-weight: bold; /* Ensure 'Home' link is bold */
}
.font-btn {
    background-color: transparent;
    color: white;
    border: 1px solid white;
    padding: 4px 10px;
    font-size: 0.9rem;
    border-radius: 3px;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

.font-btn:hover,
.font-btn:focus {
    background-color: white;
    color: black;
    outline: none;
}
</style>

</head>

<body>
<main class="fullscreen-center">
    <div id="pageWrapper">
    @include('layouts.header_elements')
       
    <section class="disclaimer-text bg-white rounded mt-5">
    <div class="text-center py-4  top-border align-middle header" style="background-color: rgba(20, 71, 160, 0.8); color: white; ">
    <h5 class='lang' key='privacycopy'>Privacy & Copyright Policy</h5>
        </div>
        <div class="mt-2 p-4 ">
        <div class="d-flex justify-content-between align-items-center pr-4">
        <h5 class="mb-1"><b key='copyright' class='lang'>Copyright Policy</b></h5>
       
    </div>
            
    <ul>
  <li>
    <p key='copyright_p1' class='lang'>
      The contents published on this portal are primarily owned by the Director General of Audit Department.
      Reproduction of material must acknowledge the source, but permission does not extend to material identified as third-party copyright.
    </p>
  </li>
</ul>

    <h5><b key='privacy_head' class='lang'>Privacy Policy</b></h5>
    <ul>
    <li>
    <p key='privacy_head_p1' class='lang'>
      Comprehensive Audit Management System (CAMS) does not automatically capture specific personal information without consent.
      Any personal information provided is protected from loss, misuse, unauthorized access, disclosure, alteration, or destruction.
      We do not sell or share personally identifiable information with any third party.
    </p>
</li>
    </ul>
  
    <h5><b key='hyperlink_head' class='lang'>Hyperlinking Policy</b></h5>
    <ul>
        <p>
          i. <b key='hyperlink_head_1' class='lang'>Links to External Websites/Portals:</b>
          <span key='hyperlink_head_p1' class='lang'>
            Links on this portal to other websites/portals are for user convenience.
            NIC or Director General of Audit Department is not responsible for the contents and reliability of linked websites.
            The presence of a link on this portal should not be assumed as an endorsement.
          </span>
        </p>
        <p>
          ii. <b key='hyperlink_head_2' class='lang'>Links to Comprehensive Audit Management System (CAMS) by Other Websites:</b>
          <span key='hyperlink_head_p2' class='lang'>
            Direct linking to information on Comprehensive Audit Management System (CAMS) is allowed without prior permission.
            Informing us of any links is appreciated to keep you informed of any changes.
            Loading our pages into frames on your site is not permitted; they must open in a new browser window.
          </span>
        </p>
      
    </ul>
  </li>
</ul>


        </div>
    </section>
 </main>
 @include('layouts.site_footer')

        <!-- Footer -->
       
    </div>
   
     <!-- include jQuery library -->
<script src="{{ asset('site/js/jquery-3.4.1.min.js') }}"></script>
<!-- include custom JavaScript -->
<script src="{{ asset('site/js/jqueryCustom.js') }}"></script>
<!-- include plugins JavaScript -->
<script src="{{ asset('site/js/plugins.js') }}"></script>
    <script>
$(document).ready(function () {
    const allowedSizes = [10, 12, 14, 16, 18,20]; // Only these sizes allowed
    const defaultSize = 16;

    function setFontSize(size) {
        $('html').css('font-size', size + 'px');
    }

    function getCurrentFontSize() {
        return parseFloat($('html').css('font-size'));
    }

    $(".resetMe").click(function () {
        setFontSize(defaultSize);
    });

    $(".increase").click(function () {
        const currentSize = getCurrentFontSize();
        const index = allowedSizes.indexOf(currentSize);
        if (index !== -1 && index < allowedSizes.length - 1) {
            setFontSize(allowedSizes[index + 1]);
        }
    });

    $(".decrease").click(function () {
        const currentSize = getCurrentFontSize();
        const index = allowedSizes.indexOf(currentSize);
        if (index > 0) {
            setFontSize(allowedSizes[index - 1]);
        }
    });

    // Optional: Set default size on page load
    setFontSize(defaultSize);
});
</script>
<script>
    

    // Function to change the background color dynamically
    

    $(window).on('load', function() {

        var jqxhr = $.getJSON("{{ asset('json/layout.json') }}", function(data) {
                // Once the JSON data is loaded, assign it to the arrLang variable
                arrLang = data;
                //console.log(arrLang); // Logging the data to ensure it's loaded correctly
            })
            .done(function() {
                // This code block will execute when the JSON data is successfully loaded
                translate(); // Call the translate function after the JSON data is loaded
                //changeBackgroundColor(storedColor);
            })
            .fail(function(jqxhr, textStatus, error) {
                // This code block will execute if there is an error in loading the JSON data
                var err = textStatus + ", " + error;
                console.error("Request failed: " + err); // Log the error for debugging
            });



    })


    // Define the translate function
    function translate() {
        // Retrieve the selected language from local storage or set it to English if not present
        var lang = window.localStorage.getItem('lang');
        if (lang == null)
            lang = 'en';

        // Set a cookie named 'language' with the selected language
        document.cookie = "language=" + lang;

        // Update the value of an element with the id 'translate' to reflect the selected language
        $('#translate').val(lang);


        // Check if an element with the id 'banner_image' exists on the page
        var bannerImageExists = document.getElementById('banner_image') !== null;

        if (bannerImageExists) { // If the element exists, do something

            // Change the source (`src`) attribute of an image with the id 'banner_image' based on the selected language
            // if (lang == "ta") {
            //     document.getElementById('banner_image').src =
            //         "https://10.163.19.176/ctax_3009/public/site/images/image_banner.png";
            // } else {
            //     document.getElementById('banner_image').src =
            //         "https://10.163.19.176/ctax_3009/public/site/images/Brown_Banner.jpg";
            // }
        } else {
            // If the element doesn't exist, do something else
            //console.log("Element with id 'banner_image' does not exist on the page.");
        }

        // Update the text content of elements with the class 'lang' based on the translations stored in the arrLang variable for the selected language
        $('.lang').each(function(index, item) {
            $(this).text(arrLang[lang][$(this).attr('key')]);
        });
    }


    // Process translation
    $(function() {
        $('#translate').change(function() {

            lang = ($(this).val());
            window.localStorage.setItem('lang', ($(this).val()));

            $('#translate').val(window.localStorage.getItem('lang'));

            window.localStorage.setItem('active_menu', '');
            translate();
        });
    });
</script>
</body>
</html>

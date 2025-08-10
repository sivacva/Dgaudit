<style>
    .govt_head .g_row1 {
        align-items: center;
      
    }


    @media(max-width:600px) {
        .govt_head .g_row1 {
            padding: 1%;
        }
        .govt_head  {
            margin-bottom:-6%;
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
    }

    .header_content {
        background-color: #fff;
        /* Background color for header */
    }
    #topbar {
        background-color:rgb(0, 63, 165, 0.8);
        font-size: 14px;
        padding: 0;
        color: rgba(255, 255, 255, 1);
        /*  background:   url(../images/bg2.png), linear-gradient(225deg, #ff852b 10%, #003fa5 40% ,#08152e 45%);*/
        /* background: url("{{ asset('site/image/bg2.png') }}"), linear-gradient(225deg, #2bd8ff 10%, #003fa5 30%, #253246 45%); */
        background-size: cover;
        background-position: top;
    }
    @media (max-width: 576px) {
  .dropdown {
    width: auto;
  }
  .dropdown button {
    font-size: 0.85rem;
    padding: 0.3rem 0.6rem;
  }
  .dropdown .fa {
    margin-right: 4px;
  }
  .dropdown-menu .dropdown-item {
    white-space: normal;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
  }
  .image{
    width:70%;
  }
}
.head select {
  min-width: 90px;
}

@media (max-width: 600px) {
  .control-buttons button {
    padding: 2px 6px;
    font-size: 12px;
  }
  .lang {
    font-size: 14px;
  }
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
    margin-right:5%;
}

.font-btn:hover,
.font-btn:focus {
    background-color: white;
    color: black;
    outline: none;
}
.additional {
    font-weight: bold; /* Make text bold */
}

a.additional {
    font-weight: bold; /* Ensure 'Home' link is bold */
}

@media (max-width: 600px) {
  .control-buttons button {
    padding: 2px 6px;
    font-size: 12px;
  }
  
}
</style>

<div class="phStickyWrap font_div">
            <div class="header_content fixed-top">
                <div id="topbar" class="hdFixerWrap d-lg-block"
                    >
                    <div class="container-fluid py-2">
                    <div class="row align-items-center">
                            <!-- Left side: Government label and Navbar toggle -->
                            <div class="col-6 col-md-6 d-flex align-items-center">
                                <a href="screenreader" class="font_div text-white"
                                    style="font-size: 16px; font-family: 'Helvetica', 'Arial', sans-serif;">
                                    <span class="lang mr-5" key="screenreaderheading">Screen Reader</span>
                                </a>
                                <div class="ml-3 d-flex ">
                                    <button class="decrease font-btn mr-2">A-</button>
                                    <button class="resetMe font-btn mr-2">A</button>
                                    <button class="increase font-btn mr-2">A+</button>
                                    <a href="/" class="lang additional" key="home" style="font-size: 16px;">Home</a>

                                </div>
                                

                                </div>
                            <!-- Right side: Font size buttons and language dropdown -->
                            <div class="col-6 col-md-6 d-flex  justify-content-end">
                                <select class="custom-select custom-select-sm" id="translate"
                                    style="width: auto; border-color: #0262af;">
                                    <option value="en">English</option>
                                    <option value="ta">தமிழ்</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
<section class="govt_head bg-white mt-0">
    <div class="container-fluid py-1">
        <div class="row g_row_main mt-1 mb-0 align-items-center">
            <!-- Left side: Logo and Titles -->
            <div class="col-12 col-md-8">
              
                    <div class="row g_row1 align-items-center image-header">
                        <div class="col-3 col-md-1 d-flex align-items-center justify-content-center image-size">
                            <img src="{{ asset('site/image/tn__logo.png') }}" class="img-fluid">
                        </div>
                        <div class="col-9 col-md-11 text-black">
                            <h5 class="nameline3 text-black lang mb-0" key="title1" style="line-height: 1; padding-bottom: 7px; color:black;">
                                Comprehensive Audit Management System
                            </h5>
                            <h5 class="nameline1 lang mb-0" key="dg_office" style="line-height: 1; color:black; padding-bottom: 7px;">
                                Director General of Audit
                            </h5>
                            <h5 class="nameline2 lang mb-0" key="title2" style="line-height: 1; color:black;">
                                Government of Tamil Nadu
                            </h5>
                        </div>
                    </div>
                
            </div>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<script src="../assets/js/jquery_3.7.1.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- Bootstrap Bundle with Popper -->
            <div class="col-12 col-md-4 d-flex justify-content-end mt-3 mt-md-0">
                <div class="dropdown border border-grey rounded p-1">
                    <button class="btn btn-link nav-link text-dark dropdown-toggle p-1"
                        type="button"
                        id="loginDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fa fa-sign-in"></i>
                    <span class="lang" key="login">Login</span>
                    </button>

                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="loginDropdown">
                    <li><a class="dropdown-item lang" key="auditorlogin" href="{{ url('/login') }}">Auditor Login</a></li>
                    <li><a class="dropdown-item lang" key="auditeelogin" href="{{ url('/auditeelogin') }}">Auditee Institution Login</a></li>
                    </ul>

                    </div>
                    </div>


                        </div>
                    </div>
                </section>
                            </div>
                        </div>
                    </div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var jqxhr = $.getJSON("{{ asset('json/layout.json') }}", function(data) {
                // Once the JSON data is loaded, assign it to the arrLang variable
                arrLang = data;
                // console.log(arrLang); // Logging the data to ensure it's loaded correctly
            })
            .done(function() {
                // This code block will execute when the JSON data is successfully loaded
                translate(); // Call the translate function after the JSON data is loaded
                //changeBackgroundColor(storedColor);
            })
            .fail(function(jqxhr, textStatus, error) {
                // This code block will execute if there is an error in loading the JSON data
                var err = textStatus + ", " + error;
                console.log("Request failed: " + err); // Log the error for debugging
            });


    });

    $(document).ready(function () {
    const allowedSizes = [12,13,14,15,16]; // Only these sizes allowed
    const defaultSize = 14;

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


    function translate() {

        // Retrieve the selected language from local storage or set it to English if not present
        var lang = window.localStorage.getItem('lang');
        if (lang == null)
            lang = 'en';

        // Set a cookie named 'language' with the selected language
        document.cookie = "language=" + lang;


        // Update the value of an element with the id 'translate' to reflect the selected language
        $('#translate').val(lang);


       
        $('.lang').each(function(index, item) {
            $(this).text(arrLang[lang][$(this).attr('key')]);
        });

    }
    $(function() {
        $('#translate').change(function() {

            lang = ($(this).val());
            window.localStorage.setItem('lang', ($(this).val()));

            $('#translate').val(window.localStorage.getItem('lang'));

            window.localStorage.setItem('active_menu', '');
            translate();
        });
    });

    function setCookie(cookieName, cookieValue, expirationDays) {
        var d = new Date();
        d.setTime(d.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
    }


    // Function to get the value of a cookie
    function getCookie(cookieName) {
        var name = cookieName + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var cookieArray = decodedCookie.split(';');
        for (var i = 0; i < cookieArray.length; i++) {
            var cookie = cookieArray[i];
            while (cookie.charAt(0) == ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) == 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }
        return "";
    }
</script>

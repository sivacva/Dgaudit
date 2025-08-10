let languageDataCache = null;  // Cache for language data
let isDataFetched = false;     // Flag to track if data has been fetched
function getLabels_jsonlayout(keys,onchangeoflanuguage) {
    //alert();
    return new Promise((resolve, reject) => {
        // Check if the data is already cached and available
        if (languageDataCache && isDataFetched) {
            var selectedLang = '';
            if(onchangeoflanuguage  == 'Y') selectedLang =getLanguage('Y');
            else  selectedLang = getLanguage('N');

            const result = keys.reduce((acc, key) => {
                acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Some error occurred contact administrator";
                return acc;
            }, {});
            return resolve(result);
        }
        if (!isDataFetched) {
            fetch('/json/layout.json')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to load JSON file: " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    languageDataCache = data;  // Cache the fetched data
                            isDataFetched = true;  // Mark that data has been fetched

                            var selectedLang = '';
                            if(onchangeoflanuguage  == 'Y') selectedLang =getLanguage('Y');
                            else  selectedLang = getLanguage('N');


                            const result = keys.reduce((acc, key) => {
                                acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Some error occurred contact administrator";
                                return acc;
                            }, {});
                            resolve(result);
                })
                .catch(error => {
                    console.error("Error loading placeholders/errors JSON:", error);
                    reject(error);
                });
        } else {
            lang = window.localStorage.getItem("lang");
            updatePlaceholders(lang);
            resolve(); // If already loaded, resolve immediately
        }
        // // If data has not been fetched or cached yet, proceed to fetch it
        // fetch('json/layout.json')
        //     .then(response => {
        //         if (!response.ok) {
        //             throw new Error(`Failed to load JSON: ${response.statusText}`);
        //         }
        //         return response.json();
        //     })
        //     .then(data => {
        //         languageDataCache = data;  // Cache the fetched data
        //         isDataFetched = true;  // Mark that data has been fetched
        //         const selectedLang = getLanguage();
        //         const result = keys.reduce((acc, key) => {
        //             acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Translation not available";
        //             return acc;
        //         }, {});
        //         resolve(result);
        //     })
        //     .catch(error => {
        //         console.error("Error loading language data:", error);
        //         const errorResult = keys.reduce((acc, key) => {
        //             acc[key.id] = "Error loading translation";
        //             return acc;
        //         }, {});
        //         resolve(errorResult); // Resolve with error message for each key
        //     });
    });
}
// Batch update labels dynamically
function updateLabels(keyIdPairs,onchangeoflanuguage) {


    getLabels_jsonlayout(keyIdPairs,onchangeoflanuguage)
        .then(labels => {
            keyIdPairs.forEach(pair => {
                $("#" + pair.id).html(labels[pair.id]);  // Dynamically update based on id
            });
        })
        .catch(error => {
            console.error("Error updating labels:", error);
        });
}



function changeButtonActionwithoutformrefresh(form_name,action_name,insertBtnid,clearBtnid,error,savebtntext,clearbtntext,action)
    {
       // alert(onchangeoflanuguage);
        if (error) $("#" + error).hide();

        updateLabels([
            { key: savebtntext, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'N');
        if(action == 'insert')
        {
            $("#" + action_name).val("insert");
            document.getElementById(insertBtnid).style.backgroundColor = "#b71362";
        }
        if(action == 'update')
        {
            $("#" + action_name).val("update");
            document.getElementById(insertBtnid).style.backgroundColor = "#0262af";
        }
        var validator = $("#"+form_name).validate(); // Get validator instance
        // Reset form fields
        // $("#"+form_name)[0].reset();
        // Reset validation messages
        validator.resetForm();
        // Remove error and valid classes
        $("#"+form_name).find("label.error").remove();
        $("#"+form_name).find(".error").removeClass("error");
        $("#"+form_name).find(".valid").removeClass("valid");
        // Reapply JSON messages to ensure they don't disappear
        const language = window.localStorage.getItem('lang') || 'en';
        validator.settings.messages = errorMessages[language];
        document.getElementById(insertBtnid).style.color = "#FFFFFF";
        window.scrollTo(0, 0);
    }


function changeButtonAction(form_name,action_name,insertBtnid,clearBtnid,error,savebtntext,clearbtntext,action)
    {
       // alert(onchangeoflanuguage);
        if (error) $("#" + error).hide();
        $("#" + form_name)[0].reset();
        updateLabels([
            { key: savebtntext, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'N');
        if(action == 'insert')
        {
            $("#" + action_name).val("insert");
            document.getElementById(insertBtnid).style.backgroundColor = "#b71362";
        }
        if(action == 'update')
        {
            $("#" + action_name).val("update");
            document.getElementById(insertBtnid).style.backgroundColor = "#0262af";
        }
        var validator = $("#"+form_name).validate(); // Get validator instance
        // Reset form fields
        $("#"+form_name)[0].reset();
        // Reset validation messages
        validator.resetForm();
        // Remove error and valid classes
        $("#"+form_name).find("label.error").remove();
        $("#"+form_name).find(".error").removeClass("error");
        $("#"+form_name).find(".valid").removeClass("valid");
        // Reapply JSON messages to ensure they don't disappear
        const language = window.localStorage.getItem('lang') || 'en';
        validator.settings.messages = errorMessages[language];
        document.getElementById(insertBtnid).style.color = "#FFFFFF";
        window.scrollTo(0, 0);
    }
    function changeButtonText(action,insertBtnid,clearBtnid,savebtntext,updatebtntext,clearbtntext)
    {
        if($('#'+action).val() == 'insert')
        {
            keyfor  =   savebtntext;
        }
        else
        {
            keyfor  =   updatebtntext;
        }
        updateLabels([
            { key: keyfor, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'Y');
    }







function handleUnauthorizedError(errorcode) {
    window.location.href = "/error-page"; // Redirect to the error page

    // Prevent back button navigation
    history.pushState(null, null, "/error-page");
    window.onpopstate = function () {
        history.pushState(null, null, "/error-page");
    };
}








$(".text_special").on("keypress", function (event) {
    var charCode = event.which || event.keyCode;
    var charStr = String.fromCharCode(charCode);

    // Allow only letters (A-Z, a-z) and special characters , . / & -
    if (/^[a-zA-Z0-9 ,_.%/&-]$/.test(charStr)) {
        return true;
    } else {
        return false;
    }
});







$(".only_numbers").on("keypress", function (event) {
    if (event.charCode >= 48 && event.charCode <= 57)
        return true; // let it happen, don't do anything
    else return false;
});

document.addEventListener("DOMContentLoaded", function () {
    // Select all the select elements
    const selects = document.querySelectorAll(".form-select");

    // Function to handle color updates for the selected option
    function updateSelectColor() {
        const selectedOption = this.options[this.selectedIndex];

        // If the selected option is empty (default), set the text color to gray
        if (selectedOption.value === "") {
            this.style.color = "gray"; // Text color of the select itself
        } else {
            this.style.color = "black"; // Text color of the select itself
        }
    }

    // Iterate over each select element
    selects.forEach((select) => {
        // Initially update the color on page load
        updateSelectColor.call(select);

        // Add event listener for focus (when clicked)
        select.addEventListener("focus", function () {
            this.style.backgroundColor = "white"; // Set background color to white when focused

            // Update color for options when select box is focused
            const options = this.querySelectorAll("option");
            options.forEach((option) => {
                if (option.value === "") {
                    option.style.color = "gray"; // Set empty option color to gray
                } else {
                    option.style.color = "black"; // Set other options to black
                }
            });
        });

        // Add event listener for blur (when focus is lost)
        select.addEventListener("blur", function () {
            this.style.backgroundColor = ""; // Remove background color when focus is lost
            // Reset the color of the selected option based on its value
            updateSelectColor.call(this);
        });

        // Add event listener for change (when user selects an option)
        select.addEventListener("change", updateSelectColor);
    });
});

function updateSelectColorByValue(selectElements) {
    selectElements.forEach((selectElement) => {
        // Update the color of the select element based on the selected value
        const selectedOption =
            selectElement.options[selectElement.selectedIndex];

        // Apply color to the select element's text
        selectElement.style.color =
            selectedOption.value === "" ? "gray" : "black";

        // Apply color to all options inside the select element
        Array.from(selectElement.options).forEach((option) => {
            option.style.color = option.value === "" ? "gray" : "black";
        });
    });
}

 // Fetch holidays from Laravel API
 let holidays = [];
 $.ajax({
     url: '/fetch-holidays', // URL of the Laravel route
     method: 'GET',
     async: false,
     success: function(data) {
         holidays = data; // Array of holiday dates in 'dd/mm/yyyy' format
     },
     error: function() {
         console.error("Failed to fetch holidays.");
     }
 });

 // Helper function to get holiday name by date
 function getHoliday(date) {
     const formattedDate = ('0' + date.getDate()).slice(-2) + '/' +
         ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
         date.getFullYear();

     const holiday = holidays.find(h => h.date === formattedDate);
     return holiday ? holiday.name : null;
 }


  function init_datepicker(inputId, startDate, endDate, setdate = null, form = null,fromvalclr=null,tovalclr=null,type='null')
  {
    
    // Destroy any existing datepicker instance before re-initializing
    $("#" + inputId).datepicker("destroy");

    //let daysOfWeekDisabled = [];

    // if (form === "entryandexitmeetform") {
    //     daysOfWeekDisabled = [0,6]; // Disable Saturday and Sunday
    // } 
    // else {
    //     daysOfWeekDisabled = [0, 6]; // Disable Saturday and Sunday
    // }
    let daysOfWeekDisabled = [0,6];

    $("#" + inputId).datepicker({
        format: "dd/mm/yyyy",
        startDate: startDate,
        endDate: endDate,
        daysOfWeekDisabled:daysOfWeekDisabled,
        autoclose: true,
        clearBtn: true,
        beforeShowDay: function (date) {
            const holidayName = getHoliday(date);
            if (holidayName) {
                return {
                    enabled: false,
                    tooltip: holidayName,
                    classes: 'holiday-red'
                };
            }
            return true;
        }
    }).on('changeDate clearDate', function (e) 
    {

        if (form === "entryandexitmeetform" && e.type === "changeDate")
            {
                const selectedDate = e.date;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                selectedDate.setHours(0, 0, 0, 0);
                if (selectedDate > today)
                {
                    var errormsg='Only today date is available for selection.';
                            passing_alert_value('Alert', errormsg, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    $('#confirmation_alert').css('z-index', 10000000);
                    $(this).datepicker('setDate', null);
                    return;
                }
            }
        if (e.type === 'clearDate' && inputId === fromvalclr && form === 'cleardateform') {
          
            $(`#${tovalclr}`).datepicker('setDate', null);


            let maxDate;
            let minDate;
        
            if (type === 'serviceperiod') {
               
                minDate = null;
                maxDate = new Date();
            } else {
                const ToDate = '<?php echo $Toquarter; ?>'; // Should be in a parseable format
                maxDate = new Date(ToDate);
                minDate = new Date();

            }
        
        
        
            init_datepicker(fromvalclr, minDate, maxDate, null, 'clear');
            init_datepicker(tovalclr, minDate, maxDate, null, 'clear');
        }
        
    });

    if(setdate)
     {
        if (form=='entryandexitmeetform') 
        {
           // alert(setdate);
            if(setdate!=='today')
            {
               $("#" + inputId).datepicker("setDate", setdate);
            }else
            {
                $("#" + inputId).datepicker("show");

            }

        }else
        {
            $("#" + inputId).datepicker("setDate", setdate);

        }

     }else 
     {
        if(form!=='clear')
        {
            $("#" + inputId).datepicker("show");

        }else
        {
            $("#" + inputId).datepicker();
        }
      }

   }






// function init_datepicker(inputId, startDate, endDate, setdate = null) {

//     alert(endDate);
//     // Initialize the datepicker with the provided options
//     $("#" + inputId).datepicker({
//         format: "dd/mm/yyyy", // Set the date format
//         startDate: startDate, // Set the start date
//         endDate: endDate, // Set the end date
//         autoclose: true, // Close the datepicker when a date is selected
//         beforeShowMonth: function(date) {
//             // Adjust the visibility of the "previous month" button
//             var prevButton = $(".ui-datepicker-prev");
//             var nextButton = $(".ui-datepicker-next");
            
//             if (date < startDate) {
//                 prevButton.hide(); // Hide previous month button if the date is before start date
//             } else {
//                 prevButton.show();
//             }

//             if (date > endDate) {
//                 nextButton.hide(); // Hide next month button if the date is after end date
//             } else {
//                 nextButton.show();
//             }
//         }
//     });

//     // If a setdate is provided, set the initial date
//     if (setdate) {
//         $("#" + inputId).datepicker("setDate", setdate); // Set the date to the provided date
//     } else {
//         $("#" + inputId).datepicker("show");
//     }
// }

// function init_datepicker(inputId, startDate, endDate, setdate = null) {
//     // Make sure startDate and endDate are valid Date objects
//     if (typeof startDate === 'string') {
//         startDate = new Date(startDate.split('/').reverse().join('-'));
//     }
//     if (typeof endDate === 'string') {
//         endDate = new Date(endDate.split('/').reverse().join('-'));
//     }

//     // Destroy any previous datepicker instance and re-initialize
//     $("#" + inputId).datepicker('destroy');
//     $("#" + inputId).datepicker({
//         format: "dd/mm/yyyy", // Set the date format
//         startDate: startDate, // Set the start date
//         endDate: endDate, // Set the end date
//         autoclose: true, // Close the datepicker when a date is selected
//         beforeShowMonth: function(date) {
//             // Adjust the visibility of the "previous month" button
//             var prevButton = $(".ui-datepicker-prev");
//             var nextButton = $(".ui-datepicker-next");
            
//             if (date < startDate) {
//                 prevButton.hide(); // Hide previous month button if the date is before start date
//             } else {
//                 prevButton.show();
//             }

//             if (date > endDate) {
//                 nextButton.hide(); // Hide next month button if the date is after end date
//             } else {
//                 nextButton.show();
//             }
//         }
//     });

//     // If setdate is provided, set the initial date
//     if (setdate) {
//         // Convert setdate to a Date object if it's a string
//         if (typeof setdate === "string") {
//             var dateArray = setdate.split('/');
//             setdate = new Date(dateArray[2], dateArray[1] - 1, dateArray[0]); // dd/mm/yyyy to Date object
//         }
        
//         $("#" + inputId).datepicker("setDate", setdate);
//     } else {
//         $("#" + inputId).datepicker("show");
//     }
// }



function fn_captilise_each_word(txtbox_name) {
    var value = $("#" + txtbox_name).val();
    text = value
        .toLowerCase()
        .split(" ")
        .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
        .join(" ");
    document.getElementById(txtbox_name).value = text;
    return true;
}

function capitalizeFirstLetter(txtbox_name) {
    const inputField = document.getElementById(txtbox_name);
    const value = inputField.value;

    // Capitalize first letter and keep the rest as it is
    inputField.value = value.charAt(0).toUpperCase() + value.slice(1);
    document.getElementById(txtbox_name).value = inputField.value;
    return true;
}

$(".name").on("keypress", function (event) {
    if (
        (event.charCode > 64 && event.charCode < 91) ||
        (event.charCode > 96 && event.charCode < 123) ||
        event.charCode == 32
    )
        return true;
    else return false;
});

// Allow Alphabets and Numbers
$(".alpha_numeric").on("keypress", function (event) {
    if (
        (event.charCode > 64 && event.charCode < 91) ||
        (event.charCode > 96 && event.charCode < 123) ||
        (event.charCode >= 48 && event.charCode <= 57) ||
        event.charCode == 32
    )
        return true; // let it happen, don't do anything
    else return false;
});

function ValidateEmail() {
    var email = document.getElementById("email").value;
    var lblError = document.getElementById("lblError");
    lblError.innerHTML = "";
    var expr =
        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!expr.test(email)) {
        lblError.innerHTML = "Invalid email address.";
    }
}

$("#email").on("keypress", function (event) {
    var regex = new RegExp("^[a-zA-Z0-9!#$%&'*+-/=?^_`{|}~@]+$");
    var key = String.fromCharCode(
        !event.charCode ? event.which : event.charCode
    );
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
});

function change_button_as_update(
    form_name,
    action_name,
    button_action,
    error,
    card_name,
    closebtn,
    key_label=''
) {
    if (error) $("#" + error).hide();

    if (card_name) $("#" + card_name).show();
    if (closebtn) $("#" + closebtn).html("Close");

    $("#" + form_name).show();
    $("#" + form_name)[0].reset();
    $("#" + action_name).val("update");
    // $("#" + button_action).html(get_jsonvalue("update"));
    $("#" + button_action).val("Update");
    $("#" + button_action).html("Update");
    $("#" + button_action).attr("key", key_label);

    document.getElementById(button_action).style.backgroundColor = "#0262af";
    document.getElementById(button_action).style.color = "#FFFFFF";
    window.scrollTo(0, 0);
}

function change_button_as_insert(
    form_name,
    action_name,
    button_action,
    error,
    closebtn
) {
    if (error) $("#" + error).hide();
    $("#" + form_name)[0].reset();
    $("#" + action_name).val("insert");
    // $("#" + button_action).html(get_jsonvalue("insert"));
    $("#" + button_action).val("Save");
    $("#" + button_action).html("Save Draft");

    document.getElementById(button_action).style.backgroundColor = "#b71362";
    document.getElementById(button_action).style.color = "#FFFFFF";
    if (closebtn) $("#" + closebtn).html("Clear");
}

// Helper function to format a date to dd/mm/yyyy
function formatDate(date) {
    var day = date.getDate().toString().padStart(2, "0"); // Ensure 2 digits for day
    var month = (date.getMonth() + 1).toString().padStart(2, "0"); // Ensure 2 digits for month
    var year = date.getFullYear(); // Get the full year
    return day + "/" + month + "/" + year; // Return formatted date
}

// Helper function to convert dd/mm/yyyy to yyyy-mm-dd format (required for <input type="date>")
function convertToInputDateFormat(date) {
    var parts = date.split("/");
    return parts[2] + "-" + parts[1] + "-" + parts[0]; // Convert to yyyy-mm-dd
}

function convertDateFormatYmd_ddmmyy(dateString) {
    // Assuming the input date format is "yyyy-mm-dd"
    const [year, month, day] = dateString.split("-");

    // Convert to dd/mm/yyyy format
    return `${day}/${month}/${year}`;
}

function passing_extra_large_alert(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type,
    alert_key_label=''
) {
    const element = document.getElementById("process_button");
    element.classList.remove("btn-danger");

    $("#ok_button").hide();
    $("#cancel_button").hide();
    $("#process_button").show();
    // $("#process_button").html("Ok");
    $("#cancel_button").show();
    element.classList.add("btn-success");

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    $(".modal-header").css({
        "background-color": selectedcolor,
    });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_header_id).attr("key", alert_key_label);

    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");


    // #593320
}

function passing_large_alert(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type,
    alert_key_label=''
) {
    const element = document.getElementById("process_button");
    element.classList.remove("btn-danger");

    $("#ok_button").hide();
    $("#cancel_button").hide();
    $("#process_button").show();
    // $("#process_button").html("Ok");
    $("#cancel_button").show();
    element.classList.add("btn-success");

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    $(".modal-header").css({
        "background-color": selectedcolor,
    });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_header_id).attr("key", alert_key_label);

    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");


    // #593320
}

function passing_alert_value(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type
) {
    if (alert_type == "confirmation_alert") {
        $("#process_button").hide();
        $("#ok_button").show();
        $("#cancel_button").hide();
        $("#button_close").hide();
    }
    if (alert_type == "delete_alert") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-success");
        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        $("#process_button").html("Delete");
        $("#cancel_button").show();
        // Add a class (quote) to the element
        element.classList.add("btn-danger");
    }
    if (alert_type == "forward_alert") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-danger");

        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        // $("#process_button").html("Ok");
        $("#cancel_button").show();
        element.classList.add("btn-success");
    }
    if (alert_type == "confirmation_alert_with_function") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-danger");
        $("#close_button").hide();
        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        // $("#process_button").html("Ok");
        $("#cancel_button").hide();
        // $('#button_close').hide();
        element.classList.add("btn-success");
    }

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    // $(".modal-header").css({ "background-color": selectedcolor });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");

    // #593320
}






 function change_dateformat(inputDate)
 {
    // Create a Date object
    let dateObj = new Date(inputDate);

    // Format the date as "dd-mm-yyyy"
    let day = String(dateObj.getDate()).padStart(2, '0'); // Ensure two digits for day
    let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Ensure two digits for month
    let year = dateObj.getFullYear(); // Get the full year

    // Get the time in 12-hour format with AM/PM
    let hours = dateObj.getHours();
    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12; // Convert to 12-hour format
    hours = hours ? hours : 12; // Handle 0 hour as 12 (midnight)

    // Combine the date and time in the desired format
    let formattedDate = day + '-' + month + '-' + year + ' ' + hours + ':' + minutes + ' ' + ampm;

   return formattedDate; // Output: 14-12-2024 10:14 PM

 }

 function makedropdownempty(id, placeholder) {
    $("#" + id).empty();
    $("#" + id).append("<option value=''>" + placeholder + "</option>");
}


var labels = {}; // To store the loaded labels
var labelsLoaded = false;
// Function to load labels.json dynamically
async function loadLabels() {
  try {
    const response = await fetch('json/layout.json'); // Path to your JSON file
    labels = await response.json();
    labelsLoaded = true;
  } catch (error) {
    console.error('Error loading labels:', error);
  }
}


// Function to get label based on the selected language
async function getLabel(key) {
    // If labels are not yet loaded, wait for them to load
    if (!labelsLoaded) {
      await loadLabels();
    }

    const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en'

    return labels[lang] && labels[lang][key] ? labels[lang][key] : key; // Fallback to the key if missing
  }

  // Initialize the labels when the script is loaded
  loadLabels();

  function getLanguage(onchange) {
    let lang;

    if (onchange === 'Y') {
        lang = $('#translate').val();

    } else {

        lang = window.localStorage.getItem('lang') || 'en';
    }

    return lang === 'ta' ? 'ta' : 'en';
}
function ChangeDateFormat(timestamp) {
    const dateParts = timestamp.split(' ');

    // If the timestamp contains a space, it includes time
    const hasTime = dateParts.length > 1;

    const date = new Date(timestamp);

    // Define options for formatting the date
    const options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };

    // Format the date
    const formattedDate = date.toLocaleString('en-GB', options).replace(',', '').replace(/:/g, ':').replace(/\//g, '-');

    // If no time, return only the date
    if (!hasTime) {
        return formattedDate.split(' ')[0];  // Date only
    }

    return formattedDate;  // Date with time
}

function downloadAndPreview(fileUrl) {
    // Trigger download
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = ''; // Hints to browser to download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
   
}

// // Example usage
// const timestamp1 = '2025-02-07 19:12:00';  // Example timestamp
// const timestamp2 = '2025-02-07';  // Example date only
// console.log(ChangeDateFormat(timestamp1));  // Output: "07-02-2025 07:12 PM"
// console.log(ChangeDateFormat(timestamp2));  // Output: "07-02-2025"

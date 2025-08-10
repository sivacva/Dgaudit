$(document).ready(function () {
    var originalSize = $(".font_div").css("font-size");
    // reset
    $(".resetMe").click(function () {
        $(".font_div").css("font-size", originalSize);
    });

    // Increase Font Size
    $(".increase").click(function () {
        var currentFontSize = $(".font_div").css("font-size");
        var currentSize = parseFloat(currentFontSize);

        // Set a maximum font size limit (adjust this value as needed)
        var maxSize = 24;

        if (currentSize < maxSize) {
            var newSize = currentSize * 1.2;
            $(".font_div").css("font-size", newSize + "px");
        }

        return false;
    });

    // Decrease Font Size
    $(".decrease").click(function () {
        var currentFontSize = $(".font_div").css("font-size");
        var currentSize = parseFloat(currentFontSize);

        // Set a minimum font size limit (adjust this value as needed)
        var minSize = 12;

        if (currentSize > minSize) {
            var newSize = currentSize * 0.8;
            $(".font_div").css("font-size", newSize + "px");
        }

        return false;
    });
});

// Function to change the background color dynamically
function changeBackgroundColor(color) {
    var elements = document.getElementsByClassName("bg_color");

    // Iterate through all elements with the class "bg_color"
    for (var i = 0; i < elements.length; i++) {
        // Set the background color for each element
        elements[i].style.backgroundColor = color;
    }

    // Store the selected color in local storage
    localStorage.setItem("selectedColor", color);
    document.cookie = "selectedColor=" + color;
}

// Example of how to retrieve the selected color from local storage
var storedColor = localStorage.getItem("selectedColor");
if (storedColor == null) {
    storedColor = "#3782ce";
    window.localStorage.setItem("selectedColor", storedColor);
    // Set a cookie named 'language' with the selected language
    document.cookie = "selectedColor=" + storedColor;
} else changeBackgroundColor(storedColor);



// let placeholders = {};
// let errorMessages = {};
// let jsonLoaded = false; // Flag to ensure JSON is loaded only once


// window.onload = function() {


// };

// // Fetch the JSON file containing placeholders and error messages
// function loadJsonData() {
//     return new Promise((resolve, reject) => {
//         if (!jsonLoaded) {
//             fetch('json/placeholdersErros.json') // Check the correct path to the JSON file
//                 .then(response => {
//                     if (!response.ok) {
//                         throw new Error("Failed to load JSON file: " + response.statusText);
//                     }
//                     return response.json();
//                 })
//                 .then(data => {
//                     console.log("Loaded JSON:", data); // Debugging log to see the full JSON data

//                     // Check if placeholders and errors exist in the loaded data
//                     if (data) {
//                         placeholders = data; // Store placeholders
//                         errorMessages = data; // Store error messages
//                         jsonLoaded = true; // Mark JSON as loaded
//                         console.log("Placeholders and error messages loaded:", placeholders, errorMessages); // Debugging log
//                         resolve(); // Resolve the promise once data is loaded
//                     } else {
//                         console.error("Invalid JSON structure: Missing 'placeholders' or 'errors' object.");
//                         reject("Invalid JSON structure");
//                     }
//                 })
//                 .catch(error => {
//                     console.error("Error loading the JSON file: ", error);
//                     reject(error); // Reject the promise on error
//                 });
//         } else {
//             resolve(); // If JSON is already loaded, just resolve the promise
//         }
//     });
// }



// document.addEventListener("DOMContentLoaded", function () {
//     var jqxhr = $.getJSON("/json/layout.json", function (data) {
//         // Once the JSON data is loaded, assign it to the arrLang variable
//         arrLang = data;

// loadJsonData()
//         // console.log(arrLang); // Logging the data to ensure it's loaded correctly
//     })
//         .done(function () {
//             // This code block will execute when the JSON data is successfully loaded
//             translate(); // Call the translate function after the JSON data is loaded
//             //changeBackgroundColor(storedColor);
//         })
//         .fail(function (jqxhr, textStatus, error) {
//             // This code block will execute if there is an error in loading the JSON data
//             var err = textStatus + ", " + error;
//             // console.log("Request failed: " + err); // Log the error for debugging
//         });
// });


var arrLang = {};
var placeholders = {};
var errorMessages = {};
var jsonLoaded = false;
let dataTables = {};
let columnLabels = {};
// document.addEventListener("DOMContentLoaded", function () {
//     // Load the first JSON file using jQuery
//     $.getJSON("/json/layout.json")
//         .done(function (data) {
//             arrLang = data; // Assign the JSON data
//             console.log("layout.json loaded:", arrLang); // Debugging log

//             // Now, load the second JSON file using fetch()
//             return loadJsonData('he;lo');
//         })
//         .then(() => {
//             console.log("Both JSON files loaded successfully");
//             translate(); // Only call translate() after both JSONs are loaded
//         })
//         .catch((error) => {
//             console.error("Error loading JSON files:", error);
//         });
// });

// // Function to load placeholders and error messages JSON
// function loadJsonData(value) {
//     alert(value);
//     return new Promise((resolve, reject) => {
//         if (!jsonLoaded) {
//             fetch('/json/placeholdersErros.json')
//                 .then(response => {
//                     if (!response.ok) {
//                         throw new Error("Failed to load JSON file: " + response.statusText);
//                     }
//                     return response.json();
//                 })
//                 .then(data => {
//                     console.log("placeholdersErrors.json loaded:", data); // Debugging log
//                     placeholders = data;
//                     errorMessages = data;
//                     jsonLoaded = true;
//                     lang = window.localStorage.getItem("lang");
//                     updatePlaceholders(lang); // Resolve once JSON is fully loaded
//                 })
//                 .catch(error => {
//                     console.error("Error loading placeholders/errors JSON:", error);
//                     reject(error); // Reject on error
//                 });
//         } else {
//             lang = window.localStorage.getItem("lang");
//             updatePlaceholders(lang); // If already loaded, resolve immediately
//         }
//     });
// }
// var arrLang = {};
// var placeholders = {};
// var errorMessages = {};
// var jsonLoaded = false;


// $(function () {
//     $("#translate").change(function () {
//         let lang = $(this).val();
//         window.localStorage.setItem("lang", lang);

//         $("#translate").val(lang);
//         updatePlaceholders(lang);
//         updatedatatable(lang);

//         window.localStorage.setItem("active_menu", "");
//         translate();
//     });
// });
// let datatableselements = new Promise((resolve, reject) => {
//     document.addEventListener("DOMContentLoaded", function () {
//         // Load the first JSON file
//         $.getJSON("/json/layout.json")
//             .done(function (data) {
//                 arrLang = data; // Assign JSON data
//                 console.log("layout.json loaded:", arrLang);

//                 // Load the second JSON file
//                 loaddatatableData().then(() => {
//                     console.log("Both JSON files loaded successfully");
//                     translate(); // Call translate() after both JSONs are loaded
//                     resolve(); // Resolve the promise after loading
//                 }).catch((error) => {
//                     console.error("Error loading JSON files:", error);
//                     reject(error);
//                 });
//             })
//             .fail((error) => {
//                 console.error("Failed to load layout.json:", error);
//                 reject(error);
//             });
//     });
// });
// let jsonLoadedPromise = new Promise((resolve, reject) => {
//     document.addEventListener("DOMContentLoaded", function () {
//         if (!jsonLoaded) {
//             $.getJSON("/json/layout.json")
//                 .done(function (data) {
//                     arrLang = data; // Assign JSON data to arrLang
//                     dataTables = arrLang; // Assign same data to dataTables for DataTable translations
//                     jsonLoaded = true; // Mark JSON as loaded

//                     console.log("layout.json loaded:", dataTables);

//                     updatedatatable(getLanguage());

//                     // Load the second JSON file (if needed)
//                     loadJsonData().then(() => {
//                         console.log("Both JSON files loaded successfully");
//                         translate(); // Call translate() after both JSONs are loaded
//                         resolve();
//                     }).catch((error) => {
//                         console.error("Error loading JSON files:", error);
//                         reject(error);
//                     });
//                 })
//                 .fail((error) => {
//                     console.error("Failed to load layout.json:", error);
//                     reject(error);
//                 });
//         } else {
//             resolve(); // Resolve immediately if already loaded
//         }
//     });
// });



// Common function to handle clicks only in mobile view


// function applyCommonDataTableStyles(table) {
//     table.on('draw.dt', function () {
//         let $pagination = $('.dataTables_paginate');
//         let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

//         function adjustView() {
//             if ($(window).width() <= 768) {
//                 $(".dataTables_filter input").css({ "width": "100px", "font-size": "12px", "padding": "4px" });
//                 $(".dt-buttons .btn").addClass("btn-sm");

//                 $pages.each(function (index) {
//                     if (index !== 0 && index !== $pages.length - 1) {
//                         $(this).hide();
//                     }
//                 });

//                 $(".dataTables_info").css({ "display": "block", "text-align": "center", "margin-bottom": "20px" });
//             } else {
//                 $(".dataTables_info").css("display", "inline-block");
//                 $(".dataTables_filter input").css({ "width": "auto", "font-size": "14px", "padding": "8px" });
//                 $(".dt-buttons .btn").removeClass("btn-sm");
//                 $pages.show();
//             }
//         }

//         adjustView();
//         $(window).off("resize").on("resize", adjustView);
//     });

//     $(window).off("resize").on("resize", function () {
//         table.buttons(0).text(
//             window.innerWidth > 768
//                 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download'
//                 : '<i class="fas fa-download"></i>'
//         );
//     });
// }
let previousWidth = window.innerWidth;

$(window).on("resize", function () {
    let currentWidth = window.innerWidth;

    if (currentWidth < previousWidth) {
        $(".dataTable").each(function () {
            let tableId = $(this).attr("id");
            let language = window.localStorage.getItem("lang") || "en";

            if (tableId) {
                console.log(`🔄 Screen shrunk! Updating DataTable: ${tableId}`);
                updatedatatable(language, tableId);
            }
        });
    }

    previousWidth = currentWidth;
});

const customExportTables = ["usertable", "userchargetable","additionalchargetable","mappallocationobj_table","auditorinstmapping","userchargetable","departmenttable","mapinst_table","auditinspectiontable"];
function updatedatatable(language, tableId) {
    getLanguage();

    if (!tableId) return;
    if (!dataTables || !dataTables[language]) return;

   let dtText = dataTables[language]["datatable"];
   if (!dtText) return;

    let tableSelector = `#${tableId}`;
    if (!$(tableSelector).length) return;

   let paginateData = dtText["paginate"];
   let buttonData = dtText["buttons"];

   let titleKey = `${tableId}_title`;
   let tableTitle = dtText[titleKey] || "Records";

   if ($.fn.DataTable.isDataTable(tableSelector)) {
       $(tableSelector).DataTable().destroy();
   }

    function isMobileView() {
        return window.innerWidth <= 768;
    }

    let table = $(tableSelector).DataTable({
        destroy: true,
        autoWidth: false,
        dom: isMobileView()
            ? '<"row g-0 align-items-center"<"col-auto p-1"B><"col-auto ms-auto p-0"f>>rtip'
            : '<"row"<"col-6"B><"col-6 text-end"f>>rtip',

        pagingType: "simple_numbers",
        responsive: true,
        lengthChange: false,
        pageLength: 10,
        columnDefs: [
            { targets: 0, type: 'num' }
        ],
        order: [[0, 'asc']],

        fnDrawCallback: function() {
            let $pagination = $('.dataTables_paginate');
            let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

            function adjustView() {
                if (isMobileView()) {
                    $(".dataTables_filter input").css({
                        "width": "100px",
                        "font-size": "12px",
                        "padding": "4px"
                    });

                    $(".dataTables_paginate").css({
                        "font-size": "12px",
                        "margin-top": "5px"
                    });

                    $(".dt-buttons .btn").addClass("btn-sm");
                    $(tableSelector).css("font-size", "12px");

                    $(tableSelector + " td, " + tableSelector + " th").css({
                        "white-space": "normal",
                        "word-break": "break-word",
                        "padding": "6px"
                    });

                    $pages.hide();
                    $(".dataTables_paginate .next, .dataTables_paginate .previous").show();
                } else {
                    $(".dataTables_filter input").css({
                        "width": "auto",
                        "font-size": "14px",
                        "padding": "6px"
                    });

                    $(".dataTables_paginate").css({
                        "font-size": "14px",
                        "margin-top": "10px"
                    });

                    $(".dt-buttons .btn").removeClass("btn-sm");
                    $(tableSelector).css("font-size", "14px");

                    $(tableSelector + " td, " + tableSelector + " th").css({
                        "white-space": "nowrap",
                        "padding": "10px"
                    });

                    $pages.show();
                }

                $(".dt-buttons .btn-success").html(
                    isMobileView()
                        ? `<i class="fas fa-download"></i>`
                        : `<i class="fas fa-download"></i> ${buttonData.download}`
                );
            }

            adjustView();
            $(window).off("resize.adjustView").on("resize.adjustView", adjustView);
        },

        buttons: [
            {
                extend: 'excelHtml5',
                text: `<i class="fas fa-download"></i> <span class="download-text">${buttonData.download}</span>`,
                className: 'btn btn-success',
                title: tableTitle,
                exportOptions: {
                    columns: function(idx, data, node) {
                        return !$(node).hasClass('noExport') && idx !== 0;
                    }
                },
                action: function(e, dt, button, config) {
                    let tableId = dt.table().node().id; // Get the actual table ID

                    if (customExportTables.includes(tableId)) {
                        exportToExcel(tableId, language); // Custom export function
                    } else {
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config); // Default Excel Export
                    }
                }
            }
        ],

        language: {
            title: tableTitle,
            search: dtText["search"],
            lengthMenu: dtText["lengthMenu"],
            info: dtText["info"],
            infoEmpty: dtText["infoEmpty"],
            infoFiltered: dtText["infoFiltered"],
            zeroRecords: dtText["zeroRecords"],
            emptyTable: dtText["emptyTable"],
            paginate: {
                previous: paginateData.previous,
                next: paginateData.next
            }
        }
    });

    if (!$.fn.DataTable.isDataTable(tableSelector)) return;

    $(".dt-buttons .btn-success .download-text").text(buttonData.download);

    table.rows().every(function() {
        let rowData = this.data();
        rowData.statusflag = rowData.statusflag === 'Y'
            ? `<span class="badge lang btn btn-primary btn-sm">${arrLang[language]["active"] || 'Active'}</span>`
            : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${arrLang[language]["inactive"] || 'Inactive'}</span>`;

        this.invalidate();
    });

    table.draw(false);
}

$(function () {
    $("#translate").change(function () {
        let language = $(this).val();
        window.localStorage.setItem("lang", language);

        $("#translate").val(window.localStorage.getItem("language"));
        updatePlaceholders(language);

        $(".dataTable").each(function () {
            let tableId = $(this).attr("id");
            if (tableId) {
              //  console.log( ${tableId}`); // Debugging log
                updatedatatable(language, tableId);
            } else {
                console.warn("⚠ Skipping DataTable with missing ID.");
            }
        });

        window.localStorage.setItem("active_menu", "");
        translate();
    });

});
function handleMobileClick(selector, callback) {
    $(document).off('click', selector).on('click', selector, function(event) {
        if ($(window).width() <= 768) {
            event.preventDefault();
            callback($(this));
        }
    });
}

function setupMobileRowToggle(mobileColumns) {
    jsonLoadedPromise.then(() => {
        let language = getLanguage();
        applyMobileStyles()
        handleMobileClick('.toggle-row', function(button) {
            let rowData = JSON.parse(button.attr('data-row'));
            let currentRow = button.closest('tr');

            if (!rowData) {
                console.warn(" No row data found.");
                return;
            }

            if (currentRow.next().hasClass('extra-row')) {
                currentRow.next('.extra-row').remove();
                button.text('▶');
            } else {
                let extraContent = `<div class="p-2 bg-light extra-content">`;

                mobileColumns.forEach((key) => {
                    if (!key) return;

                    let label = arrLang?.[language]?.[key] ||
                                columnLabels?.[key]?.[language] ||
                                columnLabels?.[key] ||
                                (typeof key === "string" ? key.replace(/_/g, ' ').toUpperCase() : "N/A");

                    let value = rowData[key] || '-';

                    if (key.toLowerCase() === "statusflag") {
                        let activeText = arrLang?.[language]?.["active"] || 'Active';
                        let inactiveText = arrLang?.[language]?.["inactive"] || 'Inactive';

                        value = (rowData[key] === 'Y')
                            ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                            : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                    }

                    if (String(key).toLowerCase() === "allocatetowhom") {
                        let membername = arrLang?.[language]?.["membername"] || 'Team Member';
                        let leadername = arrLang?.[language]?.["leadername"] || 'Team Head';

                        value = (rowData[key] === 'Y')
                            ? `<span class="badge lang btn btn-primary btn-sm">${membername}</span>`
                            : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${leadername}</span>`;
                    }

                    if (key.toLowerCase() === "if_subcategory") {
                        let yText = arrLang?.[language]?.["yes"] || 'Yes';
                        let NText = arrLang?.[language]?.["no"] || 'No';

                        value = (rowData[key] === 'Y')
                            ? `<span class="badge lang btn btn-success btn-sm">${yText}</span>`
                            : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${NText}</span>`;
                    }
                    if (key.toLowerCase() === "reservelist") {
                        let yText = arrLang?.[language]?.["yes"] || 'Yes';
                        let NText = arrLang?.[language]?.["no"] || 'No';

                        value = (rowData[key] === 'Y')
                            ? `<span class="badge lang btn btn-success btn-sm">${yText}</span>`
                            : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${NText}</span>`;
                    }

                    extraContent += `<div class="mobile-data"><strong class="lang">${label}:</strong> ${value}</div>`;
                });

                extraContent += `</div>`;

                let extraRow = `
                    <tr class="extra-row">
                        <td colspan="3" class="extra-data">
                            ${extraContent}
                        </td>
                    </tr>`;

                currentRow.after(extraRow);
                button.text('▼');

                applyMobileStyles();
            }
        });

        $(".toggle-row").each(function () {
            let key = $(this).attr("key");
            let language = getLanguage();

            if (!key) return;

            let newLabel = arrLang?.[language]?.[key] ||
                           columnLabels?.[key]?.[language] ||
                           columnLabels?.[key] ||
                           (typeof key === "string" ? key.replace(/_/g, ' ').toUpperCase() : "N/A");

            $(this).text(newLabel);
        });

        $(window).on("resize", function() {
            if ($(window).width() > 768) {
                $('.extra-row').remove();
                $('.toggle-row').text('▶');
            } else {
                applyMobileStyles();
            }
        });
    }).catch((error) => {
        console.error("Error loading column labels:", error);
    });
}

function applyMobileStyles() {
    let isMobile = $(window).width() <= 768;

    $(".dataTables_filter input").css({
        "width": isMobile ? "100px" : "auto",
        "font-size": isMobile ? "12px" : "14px",
        "padding": isMobile ? "6px" : "6px"
    });

    $(".dataTables_paginate").css({
        "font-size": isMobile ? "12px" : "14px",
        "margin-top": isMobile ? "5px" : "10px"
    });

    $(".dt-buttons .btn").toggleClass("btn-sm", isMobile);
    $(".dataTable").css("font-size", isMobile ? "12px" : "14px");

    $(".dataTable td, .dataTable th").css({
        "white-space": isMobile ? "normal" : "nowrap",
        "padding": isMobile ? "6px" : "10px"
    });

    $(".extra-content").css({
        "font-size": isMobile ? "12px" : "14px",
        "padding": isMobile ? "6px" : "10px"
    });

    $(".mobile-data").css({
        "margin-bottom": isMobile ? "5px" : "10px"
    });

    $(".toggle-row").css({
        "font-size": isMobile ? "14px" : "16px"
    });

    // $(".dt-buttons .btn-success").html(
    //     isMobile
    //         ? `<i class="fas fa-download"></i>`
    //         : `<i class="fas fa-download"></i> Download`
    // );

    let $pagination = $('.dataTables_paginate');
    let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

    if (isMobile) {
        $pages.hide();
        $(".dataTables_paginate .next, .dataTables_paginate .previous").show();
    } else {
        $pages.show();
    }
}

const style = document.createElement("style");
style.innerHTML = `
    .toggle-row {
        background: none;
        border: none;
        color: grey;
        font-size: 16px;
        cursor: pointer;
        display: inline-block;
    }
    .toggle-row:focus {
        outline: none;
    }
    .toggle-row:hover {
        color: darkgrey;
    }
    .extra-row td {
        padding: 6px !important;
        white-space: normal !important;
    }
    .extra-content {
        font-size: 12px;
        padding: 8px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    .mobile-data {
        margin-bottom: 5px;
    }
    @media (min-width: 769px) {
        .toggle-row {
            display: none !important;
        }
    }
`;
document.head.appendChild(style);



    let jsonLoadedPromise = new Promise((resolve, reject) => {
        document.addEventListener("DOMContentLoaded", function () {
            $.getJSON("/json/layout.json")
                .done(function (data) {
                    arrLang = data;
                    dataTables = data;

                    let currentLang = getLanguage();
                    if (data[currentLang]) {
                        columnLabels = data[currentLang]; // ✅ Extract column labels based on language
                        // console.log("✅ Column Labels Loaded:", columnLabels);
                    } else {
                        console.warn(`⚠ No translations found for '${currentLang}', defaulting to English.`);
                        columnLabels = data["en"] || {}; // Default to English if missing
                    }

                    if (dataTables[currentLang] && dataTables[currentLang]["datatable"]) {
                        $(".dataTable").each(function () {
                            let tableId = $(this).attr("id");
                            if (tableId) {
                                updatedatatable(currentLang, tableId);
                            }
                        });
                    } else {
                        console.warn(`⚠ 'datatable' translations missing for '${currentLang}', skipping update.`);
                    }

                    loadJsonData().then(() => {
                        translate();
                        resolve(); // ✅ Mark JSON as loaded
                    }).catch((error) => {
                        console.error("❌ Error loading placeholders/errors JSON:", error);
                        reject(error);
                    });
                })
                .fail((error) => {
                    console.error("❌ Failed to load layout.json:", error);
                    reject(error);
                });
        });
    });



// Function to load placeholders and error messages JSON
function loadJsonData() {
    return new Promise((resolve, reject) => {
        if (!jsonLoaded) {
            fetch('/json/placeholdersErrors.json')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to load JSON file: " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    //console.log("placeholdersErrors.json loaded:", data);
                    placeholders = data;
                    errorMessages = data;
                    jsonLoaded = true;
                    lang = window.localStorage.getItem("lang");
                    updatePlaceholders(lang);
                    updatedatatable(lang) ;
                    resolve(); // Resolve after JSON is loaded
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
    });
}


function translate() {
    // Retrieve the selected language from local storage or set it to English if not present
    var lang = window.localStorage.getItem("lang");
    if (lang == null) lang = "en";



    // Set a cookie named 'language' with the selected language
    document.cookie = "language=" + lang;

    // Update the value of an element with the id 'translate' to reflect the selected language
    $("#translate").val(lang);
    $('#translate_hidden').val(lang);
    change_lang_for_page(lang);


    var major_objection_exists =
        document.getElementById("majorobjectioncode") !== null;

    if (major_objection_exists) {
        // get_objectiondetail();
        // get_severity();
    }
    $(".sidebar-item .lang, .sidebar-link.lang.has-arrow").each(function () {
        // Read the appropriate menu name from the data attributes
        var menuname = $(this).data(`menuname-${lang}`); // Fetch either data-menuname-en or data-menuname-ta based on language

        // Update the text inside the <span class="hide-menu">
        $(this).find(".hide-menu").text(menuname);
    });


    $(".lang").each(function (index, item) {
        $(this).text(arrLang[lang][$(this).attr("key")]);
    });
    $("button.lang").each(function (index, item) {
        var key = $(this).attr("key"); // Get the key attribute
        if (key && arrLang[lang][key]) {
            $(this).text(arrLang[lang][key]); // Update the button text
        }
    });
}

function setCookie(cookieName, cookieValue, expirationDays) {
    var d = new Date();
    d.setTime(d.getTime() + expirationDays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie =
        cookieName + "=" + cookieValue + ";" + expires + ";path=/";
}

// Function to get the value of a cookie
function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(";");
    for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) == " ") {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) == 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return "";
}

function change_lang_for_page(lang) {
    // Get all dropdowns with the class 'lang-dropdown'
    const dropdowns = document.querySelectorAll('.lang-dropdown');

    // Loop through each dropdown
    dropdowns.forEach((dropdown) => {
        const options = dropdown.options;

        // Loop through each option in the current dropdown
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const nameEn = option.getAttribute('data-name-en'); // English name
            const nameTa = option.getAttribute('data-name-ta'); // Tamil name

            // Update the visible text based on the selected language
            if (lang === 'en' && nameEn) {
                option.textContent = nameEn;
            } else if (lang === 'ta' && nameTa) {
                option.textContent = nameTa;
            }
        }

        if ($(dropdown).hasClass("select2-hidden-accessible")) {
            $(dropdown).select2('destroy').select2({
                templateResult: (state) => state.id ? $(state.element).attr(lang === "ta" ? "data-name-ta" : "data-name-en") || state.text : state.text,
                templateSelection: (state) => state.id ? $(state.element).attr(lang === "ta" ? "data-name-ta" : "data-name-en") || state.text : state.text
            });
        }
    });
}


// function loadTranslationsAndInitDataTable(tableId, data, lang) {
//     $.get("{{ asset('json/layout.json') }}", function (translations) {
//         console.log("JSON Loaded:", translations);

//         var langData = translations[lang] || translations['en']; // Default to English

//         if ($.fn.DataTable.isDataTable(tableId)) {
//             $(tableId).DataTable().destroy();
//         }

//         $(tableId).DataTable({
//             processing: true,
//             serverSide: false,
//             lengthChange: false,
//             data: data,

//             dom: '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
//             buttons: [{
//                 extend: "excelHtml5",
//                 text: langData.download,
//                 title: 'Call For Records',
//                 exportOptions: {
//                     columns: ':not(:last-child)'
//                 },
//                 className: window.innerWidth > 768 ? 'btn btn-info' : 'btn btn-info btn-sm'
//             }],
//             pagingType: "simple_numbers",
//             responsive: true,
//             pageLength: 10,
//             lengthMenu: [[10, 50, -1], [10, 25, 50, -1]],
//             language: {
//                 search: langData.search,
//                 lengthMenu: langData.lengthMenu,
//                 paginate: langData.paginate,
//                 emptyTable: langData.emptyTable,
//                 info: langData.info,
//                 infoEmpty: langData.infoEmpty,
//                 infoFiltered: langData.infoFiltered
//             }
//         });
//     });
// }




// let placeholders = {};
// let jsonLoaded = false; // Flag to ensure JSON is loaded only once



// // Fetch the JSON file containing placeholders
// window.onload = function() {
//     if (!jsonLoaded) {
//         fetch('json/placeholdersErros.json')
//             .then(response => response.json())
//             .then(data => {
//                 placeholders = data; // Store the placeholders
//                 jsonLoaded = true; // Mark JSON as loaded
//                 // console.log("Placeholders loaded:", placeholders); // Debugging log
//                 updatePlaceholders(window.localStorage.getItem('lang') || 'en'); // Set initial placeholders
//             })
//             .catch(error => {
//                 console.error("Error loading the JSON file: ", error);
//             });
//     }
// };



// // Update all placeholders based on the selected language
// function updatePlaceholders(language) {
//     // Check if the placeholders exist for the selected language
//     if (placeholders[language]) {
//         // Get all input fields with the `data-placeholder-key` attribute
//         const inputs = document.querySelectorAll('input[data-placeholder-key]');
//         // console.log("Updating placeholders for language:", language); // Debugging log

//         // Loop through each input and set the placeholder
//         inputs.forEach(input => {
//             const key = input.getAttribute('data-placeholder-key');
//             // console.log("Updating placeholder for:", input.id, "to", placeholders[language][key]); // Debugging log
//             if (placeholders[language][key]) {
//                 input.placeholder = placeholders[language][key];
//             }
//         });
//     } else {
//         console.error("Placeholders for language", language, "not found!");
//     }
// }



//     // Fetch error messages from the JSON file
//     function loadErrorMessages() {
//         return fetch('json/placeholdersErros.json') // Path to your JSON file
//             .then(response => response.json())
//             .then(data => {
//                 errorMessages = data; // Store error messages globally
//                 return data;
//             })
//             .catch(error => console.error("Error loading JSON:", error));
//     }

// function updateValidationMessages(language,formname) {
//     if (errorMessages[language]) {
//         // Update validation rules
//         let validator = $("#"+formname).validate();
//         validator.settings.messages = errorMessages[language]; // Update validation messages

//         // Update error messages by input name attribute
//         $("input[name]").each(function () {
//             const fieldName = $(this).attr("name"); // Get the 'name' attribute of the input field
//             if (fieldName && errorMessages[language][fieldName]) {
//                 // Find the error label and update the text
//                 $(this).next("label.error").text(errorMessages[language][fieldName]);
//             }
//         });
//     }
// }



// let placeholders = {};
// let errorMessages = {};
// let jsonLoaded = false; // Flag to ensure JSON is loaded only once

// // Fetch the JSON file containing placeholders and error messages
// function loadJsonData() {
//     if (!jsonLoaded) {
//         fetch('json/placeholdersErros.json') // Check the correct path to the JSON file
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error("Failed to load JSON file: " + response.statusText);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 console.log("Loaded JSON:", data); // Debugging log to see the full JSON data

//                 // Check if placeholders and errors exist in the loaded data
//                 if (data) {
//                     placeholders = data; // Store placeholders
//                     errorMessages = data; // Store error messages
//                     jsonLoaded = true; // Mark JSON as loaded
//                     console.log("Placeholders and error messages loaded:", placeholders, errorMessages); // Debugging log
//                 } else {
//                     console.error("Invalid JSON structure: Missing 'placeholders' or 'errors' object.");
//                 }
//             })
//             .catch(error => {
//                 console.error("Error loading the JSON file: ", error);
//             });
//     }
// }

// // Update all placeholders based on the selected language
// function updatePlaceholders(language) {
//     if (placeholders[language]) {
//         const inputs = document.querySelectorAll('input[data-placeholder-key]');
//         inputs.forEach(input => {
//             const key = input.getAttribute('data-placeholder-key');
//             if (placeholders[language][key]) {
//                 input.placeholder = placeholders[language][key];
//             }
//         });
//     } else {
//         console.error("Placeholders for language", language, "not found!");
//     }
// }

// // Update validation messages dynamically based on the selected language
// function updateValidationMessages(language, formName) {
//     if (errorMessages[language]) {
//         let validator = $("#" + formName).validate();
//         validator.settings.messages = errorMessages[language];

//         // Update error messages by input name attribute
//         $("input[name]").each(function () {
//             const fieldName = $(this).attr("name");
//             if (fieldName && errorMessages[language][fieldName]) {
//                 $(this).next("label.error").text(errorMessages[language][fieldName]);
//             }
//         });
//     } else {
//         console.error("Error messages for language", language, "not found!");
//     }
// }



// loadJsonData().then(() => {
//     const language = window.localStorage.getItem('lang') || 'en';

//     // Initialize jQuery Validation
//     validator = $("#myForm").validate({
//         rules: {
//             email: {
//                 required: true,
//                 email: true
//             },
//             username: {
//                 required: true,
//                 maxlength: 10
//             }
//         },
//         messages: errorMessages[language], // Set initial messages
//         errorPlacement: function (error, element) {
//             error.insertAfter(element);
//         },
//         submitHandler: function (form) {
//             form.submit();
//         }
//     });

//     // Language dropdown change event
//     $("#translate").change(function () {
//         const selectedLang = $(this).val(); // Get selected language
//         updateValidationMessages(selectedLang,'myForm'); // Update validation messages
//     });
// });


// Update all placeholders based on the selected language
// function updatePlaceholders(language) {
//     console.log(placeholders);
//     if (placeholders[language]) {
//         const inputs = document.querySelectorAll('input[data-placeholder-key]');
//         inputs.forEach(input => {
//             const key = input.getAttribute('data-placeholder-key');
//             if (placeholders[language][key]) {
//                 input.placeholder = placeholders[language][key];
//             }
//         });
//     } else {
//         console.error("Placeholders for language", language, "not found!");
//     }
// }

function updatePlaceholders(language) {
    // console.log(placeholders);
    if (placeholders[language]) {
        // Update placeholders for input fields
        const inputs = document.querySelectorAll('input[data-placeholder-key]');
        inputs.forEach(input => {
            const key = input.getAttribute('data-placeholder-key');
            if (placeholders[language][key]) {
                input.placeholder = placeholders[language][key];
            }
        });

        // Update placeholders for select fields
        const selects = document.querySelectorAll('select[data-placeholder-key]');
        selects.forEach(select => {
            const key = select.getAttribute('data-placeholder-key');
            if (placeholders[language][key]) {
                // Update the first disabled option (placeholder option)
                const placeholderOption = select.querySelector('option[disabled]');
                if (placeholderOption) {
                    placeholderOption.textContent = placeholders[language][key];
                } else {
                    // If no placeholder option exists, add a new one
                    const newPlaceholderOption = document.createElement('option');
                    newPlaceholderOption.value = '';
                    newPlaceholderOption.disabled = true;
                    newPlaceholderOption.selected = true;
                    newPlaceholderOption.hidden = true;
                    newPlaceholderOption.textContent = placeholders[language][key];
                    select.prepend(newPlaceholderOption);
                }
            }
        });

    } else {
        console.error("Placeholders for language", language, "not found!");
    }
}


// Update validation messages dynamically based on the selected language
// function updateValidationMessages(language, formName) {
//     if (errorMessages[language]) {
//         let validator = $("#" + formName).validate();
//         validator.settings.messages = errorMessages[language];

//         // Update error messages by input name attribute
//         $("input[name]").each(function () {
//             const fieldName = $(this).attr("name");
//             if (fieldName && errorMessages[language][fieldName]) {
//                 $(this).next("label.error").text(errorMessages[language][fieldName]);
//             }
//         });
//     } else {
//         console.error("Error messages for language", language, "not found!");
//     }
// }

function updateValidationMessages(language, formName) {
    if (errorMessages[language]) {
        let validator = $("#" + formName).validate();
        validator.settings.messages = errorMessages[language];

        // Update error messages for input, select, and date picker fields
        $("input[name], select[name]").each(function () {
            const fieldName = $(this).attr("name");
            if (fieldName && errorMessages[language][fieldName]) {
                let errorLabel = $(this).siblings("label.error");

                // Handle error labels for input-group elements (date picker, etc.)
                if (errorLabel.length === 0) {
                    errorLabel = $(this).closest(".input-group").next("label.error");
                }

                // If still not found, check within the form group
                if (errorLabel.length === 0) {
                    errorLabel = $(this).closest(".col-md-4").find("label.error");
                }

                if (errorLabel.length) {
                    errorLabel.text(errorMessages[language][fieldName]);
                }
            }
        });
    } else {
        console.error("Error messages for language", language, "not found!");
    }
}


function Open_checkflow_model(slipid, mainslipnumber) 
    {
        $('#HistoryModel').modal('show');

        $('#slipnodyn').text(`#${mainslipnumber}`);
        $('#slipHistoryTable').show();

        // Clear previous table content
        $('#slipHistoryTable tbody').empty();
        // $('#no_data_details').hide();
        $('#his_no_data_details').addClass('hide_this');

        $.ajax({
            url: `/get-sliphistory-details/${slipid}`, // Replace with your actual endpoint
            method: 'GET',
            success: function(response) {
                // Populate modal content with fetched data
                if (response.status === 'success' && response.data.length > 0) {
                    // Calculate serial number dynamically in descending order
                    const totalRows = response.data.length;

                    // Populate table rows with response data
                    response.data.forEach((item, index) => {
                        const serialNumber = index + 1; // Calculate serial number in descending order
                        const row = `
                                <tr>
                                    <td>${serialNumber}</td> <!-- Serial Number -->
                                    <td>${item.forwardedby_username || 'N/A'}</td>
                                    <td>${item.forwardedto_username || 'N/A'}</td>
                                    <td>${item.processelname || 'N/A'}</td>
                                    <td>${item.forwardedon || 'N/A'}</td>
                                </tr>
                            `;
                        $('#slipHistoryTable tbody').append(row);
                    });
                } else {
                    // Show "No data" message if the response is empty
                    $('#slipHistoryTable').hide();
                    $('#his_no_data_details').removeClass('hide_this');
                }
            },
            error: function(error) {
                // Handle errors
                $('#slipDetailsContent').text('Failed to load slip details.');
            }
        });

    }


    
    function Open_viewmodel(slipid, mainslipnumber) {
        $('#ViewSlipModel').modal('show');
        var instname = $('.slipshowinstname').html();
        $('.slipnodyn').text(instname);



        $.ajax({
            url: `/get-slip-details/${slipid}`, // Replace with your actual endpoint
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(response, textStatus, jqXHR) {

                var data = response.data;
                $('#auditsliptable').empty();
                var appenddata = $('#auditsliptable');
                var liabilitycheck = '';
                data.forEach(function(item, index) {
                    let uniqueTextareaId = "viewslip_auditorremarks_" + index;
                    let sno = index + 1;

                    // Set values based on forwardedbyusertypecode
                    let severity = item.severity;
                    let slipdetails = item.slipdetails;
                    let objectionename = item.objectionename;
                    let subobjectionename = item.subobjectionename;

                     let issuedBy = '';
                    if (item.forwardedbyusertypecode === 'A') {
                        if (item.auditteamhead === 'Y') {
                            issuedBy = item.username + ' (Team Head)';
                        } else {
                            issuedBy = item.username + ' (Team Member)';
                        }
                    } else if (item.forwardedbyusertypecode === 'I') {

                        issuedBy = 'Auditee';
                    }



                    if (item.forwardedbyusertypecode === 'I' && index > 0) {
                        // Use previous index data for severity and slipdetails
                        severity = data[index - 1].severity;
                        slipdetails = data[index - 1].slipdetails;
                        objectionename = data[index - 1].objectionename;
                        subobjectionename = data[index - 1].subobjectionename
                    }
                   

                    let dataappend =
                        '<div style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">' +
                        '<h5><center><b>#History ' + sno + '</b></center></h5>' +
                        '<table class="auditor-table table_slip"><tbody>' +
                        '<tr><th>Objection Details</th><td>' +
                        '<p><b>Main Objection :</b> <span class="mainobj">' + objectionename +
                        '</span></p>' +
                        '<p><b>Sub Objection :</b> <span class="subobj">' + subobjectionename +
                        '</span></p></td></tr>' +
                        '<tr><th>Severity</th><td class="severity">' + severity + '</td></tr>' +
                        '<tr><th>Slip Details</th><td class="auditslipdetails">' + slipdetails +
                        '</td></tr>' +
                        '<tr><th>Status</th><td class="auditslipsts">' + item.processelname +
                        '</td></tr>'+

                        '</tbody></table>' +
                        '<label class="form-label lang" for="validationDefaultUsername" key="observation">Remarks</label>' +
                        '<textarea class="form-control" id="' + uniqueTextareaId +
                        '" placeholder="Enter remarks"></textarea>' +
                        '</div><br>';

                    appenddata.append(dataappend);

                    // Load CKEditor with existing remarks content if available
                    loadckeditorauditor(item.remarks ? JSON.parse(item.remarks).content : '',
                        uniqueTextareaId);

                    if (index === 0) {
                        liabilitycheck = item.liability;
                    }
                });



                $('.liabilitydetails').hide();
                if (liabilitycheck == 'Yes') {
                    $('.liabilitydetails').show();
                    var liabilitydata = response.liability;


                    let tableBody = $('#liabilitiesTable tbody');
                    tableBody.empty();

                    liabilitydata.forEach(function(item) {

                        if (item.notype == '01') {
                            liabilityLabel = 'GPF No';
                        } else if (item.notype == '02') {
                            liabilityLabel = 'CPF No';
                        } else if (item.notype == '03') {
                            liabilityLabel = 'IFHRMS No';
                        }

                        let row = `<tr>
                                            <td>${item.liabilityname}</td>
                                            <td><b>${liabilityLabel}</b><br> ${item.liabilitygpfno}</td>
                                            <td>${item.liabilitydesignation}</td>
                                            <td>${item.liabilityamount}</td>
                                        </tr>`;
                        tableBody.append(row);
                    });
                }


                /*$('.slipnodyn').text(data.instename);
                $('.slipno').text(data.mainslipnumber);
                $('.mainobj').text(data.objectionename ? data.objectionename : '-');
                $('.subobj').text(data.subobjectionename ? data.subobjectionename : '-');
                $('.amtinvolved').text(data.amtinvolved ? data.amtinvolved : '-');
                $('.severity').text(data.severity ? data.severity : '-');
                $('.auditeename').text(data.username ? data.username : '-');
                $('.liabilitysts').text(data.liability ? data.liability : '-');
                $('.auditslipdetails').text(data.slipdetails ? data.slipdetails : '-');
                $('.auditslipsts').text(data.processelname);*/

                /*if (data.memberrejoinderremarks !== null) {
                    $('.auditorreplydiv').show();
                    $('.auditoreply_remarks').text(data.memberrejoinderremarks);
                }

                if (data.finalremarks !== null) {
                    $('.teamheaddiv').show();
                    $('.teamheadname').text(response.teamheadname);
                    $('.finalremarks').text(data.finalremarks);

                }*/



                $('.auditorname').text(data.auditorname + ' - ' + data.auditordesig);
                // Check if auditorremarks and auditeeremarks are not null or undefined
                if (response.data.auditorremarks !== null && response.data.auditorremarks !== undefined &&
                    response.data.auditorremarks !== '') {
                    $('.auditorremarksdiv').show();
                    loadckeditorauditor(response.data.auditorremarks ? JSON.parse(response.data
                        .auditorremarks).content : '');
                } else {
                    //loadckeditorauditor('');
                    $('.auditorremarksdiv').hide();

                }

                if (response.data.auditeeremarks !== null && response.data.auditeeremarks !== undefined &&
                    response.data.auditeeremarks !== '') {
                    loadckeditorauditee(response.data.auditeeremarks ? JSON.parse(response.data
                        .auditeeremarks).content : '');
                    $('.auditeeremarksdiv').show();
                } else {
                    //loadckeditorauditee('');
                    $('.auditeeremarksdiv').hide();
                }

                //for attachments
                if (data.auditorfileupload) {
                    var files = getfile(data.auditorfileupload);

                    UploadedFileList(files, UploadedFileList_withaction, 'viewslip_auditorcontainer', '',
                        '')
                }
            },
            error: function(error) {
                console.error("Error fetching data:", error);
            }
        });
    }


    function loadckeditorauditor(auditorreply, textareaId) {
        let viewslip_auditorremarks;

        // Destroy the existing CKEditor instance if it exists
        if (window[textareaId] && typeof window[textareaId].destroy === 'function') {
            window[textareaId].destroy();
        }

        const editorElement = document.getElementById(textareaId);
        if (editorElement) {
            CKEDITOR.ClassicEditor.create(editorElement, {
                toolbar: {
                    items: [
                        'findAndReplace', 'selectAll', '|',
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'numberedList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        'fontSize', 'fontFamily', '|',
                        'alignment', '|',
                        'uploadImage', 'insertTable', '|',
                    ],
                    shouldNotGroupWhenFull: true
                },
                placeholder: 'Welcome to CAMS... ',
                fontFamily: {
                    options: [
                        'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: [10, 12, 14, 'default', 18, 20, 22],
                    supportAllValues: true
                },
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                removePlugins: [
                    'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                    'MultiLevelList',
                    'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                    'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                    'SlashCommand', 'Template',
                    'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                    'CaseChange'
                ]
            }).then(editor => {
                viewslip_auditorremarks = editor;
                window[textareaId] = editor; // Store the instance globally with a unique key
                editor.setData(auditorreply); // Set data (empty if auditorreply is empty)
                window[textareaId].enableReadOnlyMode('initial');
            }).catch(error => {
                console.error("CKEditor Initialization Error:", error);
            });
        } else {
            console.error("Editor element not found.");
        }
    }


    function loadckeditorauditee(auditeereply) {

        let viewslip_auditeeremarks;


        if (window.viewslip_auditeeremarks && typeof window.viewslip_auditeeremarks.destroy === 'function') {
            window.viewslip_auditeeremarks.destroy();
        }

        // Initialize the CKEditor for auditee remarks
        CKEDITOR.ClassicEditor.create(document.getElementById("viewslip_auditeeremarks"), {
            toolbar: {
                items: [
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'numberedList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    'fontSize', 'fontFamily', '|',
                    'alignment', '|',
                    'uploadImage', 'insertTable', '|',
                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'Welcome to CAMS... ',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            removePlugins: [
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData',
                'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template',
                'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                'CaseChange'
            ]
        }).then(editor => {
            viewslip_auditeeremarks = editor;
            window.viewslip_auditeeremarks = editor; // Store the instance globally
            editor.setData(auditeereply); // Set data after initialization
            viewslip_auditeeremarks.enableReadOnlyMode('initial');
        }).catch(error => console.error("CKEditor Initialization Error:", error));
    }

    function closebtn() {
        // Get all the accordion button and accordion collapse elements
        const accordionButtons = document.querySelectorAll('.accordion-button');
        const accordionCollapses = document.querySelectorAll('.accordion-collapse');

        // Loop through each accordion-collapse
        accordionCollapses.forEach((collapse, index) => {
            // Check if the collapse has the 'show' class and remove it
            if (collapse.classList.contains('show')) {
                collapse.classList.remove('show');
            }

            // Add the 'collapsed' class to the corresponding accordion button
            if (!accordionButtons[index].classList.contains('collapsed')) {
                accordionButtons[index].classList.add('collapsed');
            }
        });
    }





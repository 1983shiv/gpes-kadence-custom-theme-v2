// var data = {
//   action: 'add_to_cart_custom',
//   security: wc_add_to_cart_params.ajax_nonce, // Ensure that this matches the nonce used in the server-side code
//   // ... other data ...
// };

// jQuery(document).ready(function($) {
//   jQuery('form.cart').on('submit', function(e) {
//         e.preventDefault(); // Prevent the default form submission

//         // Extract data from the form
//         var product_id = $(this).find('input[name="add-to-cart"]').val();
//         var quantity = $(this).find('input[name="quantity"]').val();

//         // Extract data for colors and sizes
//         var custom_base_color = [];
//         var custom_size_data = {};

//         jQuery('.custom-options-basecolor input[type="checkbox"]:checked').each(function() {
//             var color = $(this).val();
//             custom_base_color.push(color);

//             jQuery('.custom-options-sizes-' + color + ' input[type="number"]').each(function() {
//                 var size_key = 'custom_size_' + $(this).attr('data-size') + '_' + color;
//                 custom_size_data[size_key] = $(this).val();
//             });
//         });

//         // Make an AJAX request to add the product to the cart
//         jQuery.ajax({
//             type: 'POST',
//             url: wc_add_to_cart_params.ajax_url,
//             data: {
//                 action: 'add_to_cart_custom',
//                 product_id: product_id,
//                 quantity: quantity,
//                 custom_base_color: custom_base_color,
//                 custom_size_data: custom_size_data,
//                 security: wc_add_to_cart_params.ajax_nonce,
//             },
//             success: function(response) {
//                 console.log(response); // Log the response to the console
//                 // You can also redirect to the cart page or perform other actions based on the response
//             },
//         });
//     });
// });


let customPricingData = {};

function slugify(text) {
  return text
    .toString() // Convert to string if not already
    .toLowerCase() // Convert to lowercase
    .trim() // Trim whitespace from both ends
    .replace(/\s+/g, "-") // Replace spaces with hyphens
    .replace(/[^\w-]+/g, "") // Remove non-word characters except hyphens
    .replace(/--+/g, "-"); // Replace multiple consecutive hyphens with a single hyphen
}

function generateRandomString(length) {
  const characters =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let randomString = "";
  for (let i = 0; i < length; i++) {
    randomString += characters.charAt(
      Math.floor(Math.random() * characters.length)
    );
  }
  return randomString;
}

function sanitizeForSelector(input) {
  return encodeURIComponent(input).replace(/[!'()*]/g, escape);
}

function fetchAllCustomPricingData() {
  return jQuery.ajax({
    url: custom_pricing_data.ajax_url,
    method: "POST",
    data: {
      action: "get_custom_pricing",
    },
    dataType: "json",
  });
}

function updatehiddenfields(inputName, sanitizedFileName) {
  // console.log("sanitizedFileName from update hidden fields", inputName, sanitizedFileName);
  jQuery('input[name="' + inputName + '_hidden"]').val(sanitizedFileName);
}

function handleFileSelect(input) {
  var fileInput = input;
  var progressBar = jQuery('<progress value="0" max="100"></progress>');
  
  // Insert the progress bar after the file input
  jQuery(input).after(progressBar);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'your-upload-endpoint.php', true);

  // Set up the progress event handler
  xhr.upload.onprogress = function (e) {
      if (e.lengthComputable) {
          var percentage = (e.loaded / e.total) * 100;
          progressBar.val(percentage);
      }
  };

  // Set up the load event handler
  xhr.onload = function () {
      if (xhr.status === 200) {
          // Handle the successful upload
          console.log('File uploaded successfully');
          jQuery(".single_add_to_cart_button").prop("disabled", true);
      } else {
          // Handle the upload error
          console.error('Error uploading file: ' + xhr.statusText);
          progressBar.remove(); // Remove the progress bar if an error occurs
      }
  };

  // Set up the error event handler
  xhr.onerror = function () {
      console.error('Network error during file upload');
      progressBar.remove(); // Remove the progress bar if an error occurs
  };

  // Set up the abort event handler (for deselecting the file)
  xhr.onabort = function () {
      console.log('File upload aborted');
      progressBar.remove(); // Remove the progress bar if the file is deselected
  };

  // Create a FormData object and append the file to it
  var formData = new FormData();
  formData.append('file', fileInput.files[0]);

  // Send the FormData to the server
  xhr.send(formData);
}


// validate upload file and rename with unique slug
function validateAndUploadFile(selectedFile, inputName) {
  
  let allowedExtensions = [".jpg", ".jpeg", ".png", ".pdf", ".eps", ".ai", ".svg", ".tiff"];
  let maxFileSize = 64 * 1024 * 1024; // 1 MB

  let fileExtension = selectedFile.name.split(".").pop().toLowerCase();
  let fileSize = selectedFile.size;

  if (allowedExtensions.indexOf("." + fileExtension) === -1) {
    alert(
      "File extension is not allowed Please upload only jpg, jpeg, or png."
    );
    return;
  }

  if (fileSize > maxFileSize) {
    alert("File size exceeds the allowed limit. Allow limit is 1MB");
    return;
  }

  // Perform upload or further processing here
  // For showing upload progress, use the HTML5 File API

  const originalFileName = selectedFile.name;
  const fileNameWithoutExtension = originalFileName
    .split(".")
    .slice(0, -1)
    .join(".");
  const uniqueFileName = `${slugify(
    fileNameWithoutExtension
  )}-${generateRandomString(5)}.${fileExtension}`;
  const sanitizedFileName = sanitizeForSelector(uniqueFileName);

  let progressBar = jQuery('<progress value="0" max="100"></progress>');
  jQuery(`#${inputName}`).after(progressBar);

  let formData = new FormData();
  formData.append("file", selectedFile, sanitizedFileName);
  formData.append("action", "fiu_upload_file");

  console.log("selectedFile", sanitizedFileName);
  //url: 'custom-upload.php', // URL to your server-side upload handler
  //console.log("fiu_upload_file", fiu_upload_file.ajax_url);
  
  jQuery.ajax({
    url: fiu_upload_file.ajax_url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    xhr: function () {
      let xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener(
        "progress",
        function (evt) {
          if (evt.lengthComputable) {
            let percentComplete = (evt.loaded / evt.total) * 100;
            console.log(`Progress : ${percentComplete}`);
            progressBar.val(percentComplete);
            // Update progress bar or display percentage
          }
        },
        false
      );
      return xhr;
    },
    success: function (response) {
      console.log("Upload successful:", response);
      let parsedResponse = JSON.parse(response);
      let sanitizedFileName = parsedResponse.sanitized_file_name;
      console.log(
        "msg from upload success from custom.js",
        parsedResponse.sanitized_file_name
      );

      // let fieldname = inputName;
      updatehiddenfields(inputName, sanitizedFileName);
      console.log("formData from upload success from custom.js", formData);
      formData.append("sanitized_file_name", sanitizedFileName);
      // Remove the progress bar after successful upload
      progressBar.remove();
    },
    error: function (xhr, status, error) {
      // Handle error
      console.error("Upload error - Error:", error);
      console.error("Upload error - Status:", status);
      console.error("Upload error - XMLHttpRequest:", xhr);

      // Remove the progress bar in case of an error
      progressBar.remove();
    },
  });
}

// When Product Page / document load
jQuery(document).ready(function ($) {

  // Function to update the hidden input with variation data
  function updateHiddenInput() {
    var variationData = {};
    jQuery('input[type="number"]').each(function() {
        var variationId = $(this).data('variation-id');
        var qty = $(this).val();
        variationData[variationId] = qty;
    });
    jQuery('#custom_hidden_field').val(JSON.stringify(variationData));
  }

  // Call the function initially
  updateHiddenInput();


  // Listen for change event on input[type="number"]
  jQuery('input[type="number"]').on('change', function() {
      updateHiddenInput();
  });
  // Check if we are on the cart page
  // jQuery(".single_add_to_cart_button").prop("disabled", true);
  // jQuery('input[name="quantity"]').val(0);
  // jQuery('input[name="quantity"]').prop("disabled", true); 
  
  if (jQuery("body").hasClass("woocommerce-cart")) {
    // Find the element containing the "Cart Summary" text and change it
    jQuery(".cart-summary h2").text("Quote Summary");
  }

  // Find the element with the text "Order number" and replace it with "Quote number"
  jQuery(
    '.woocommerce-order-overview__order.order:contains("Order number")'
  ).html(function (_, html) {
    return html.replace("Order number", "Quote number");
  });
  jQuery(".woocommerce-order-details__title").html(function (_, html) {
    return html.replace("Order details", "Quote details");
  });
  // jQuery(".input-text.qty").val(0);
  // jQuery('input[name="quantity"]').prop("disabled", true);
  // Check if it's a simple product
  if (jQuery('.product.product-type-simple').length > 0) {
    jQuery('input[name="quantity"]').prop("disabled", false);
    // Optionally, you can set a default quantity for simple products
    jQuery(".single_add_to_cart_button").prop("disabled", false);
    jQuery('input[name="quantity"]').val(1);
    // for general form      
      let fileInputs = jQuery(
          "#custom_print_area_centerfront_file, #custom_print_area_centerback_file, #custom_print_area_leftsleeve_file, #custom_print_area_rightsleeve_file, #custom_print_area_leftchest_file, #custom_print_area_rightchest_file, #custom_print_area_customposition_file, #custom_print_area_allover_file"
        );
        console.log("fileInputs", fileInputs);
        fileInputs.each(function () {
          let fileInput = $(this);
          let inputName = fileInput.attr("name");

          // if (fileInput.is(":visible")) {
            console.log("File input is visible, attaching change event", fileInput);

            fileInput.on("change", function () {
              let selectedFile = this.files[0];
              console.log('selectedfile', selectedFile);
              if (selectedFile) {
                jQuery(".single_add_to_cart_button").prop("disabled", true);
                console.log('selectedfile', selectedFile);
                response = validateAndUploadFile(selectedFile, inputName);
                console.log("response for file upload", response);
                jQuery(".single_add_to_cart_button").prop("disabled", false);
              }
            });
          // }
        });
        // for general form end here
  } else {  
    $(".single_add_to_cart_button").prop("disabled", true);
    $('input[name="quantity"]').val(0);
    $('input[name="quantity"]').prop("disabled", true);
    console.log("variable product block running..."); 
  }
  jQuery(
    '.custom-options-printarea input[type="checkbox"], .custom-options-basecolor input[type="checkbox"]'
  ).on("change", function () {
    if ($(this).is(":checked")) {
      $(this).closest("label").addClass("checked");
    } else {
      $(this).closest("label").removeClass("checked");
    }
  });

  // Show/hide file upload fields based on selected checkboxes    '.custom-options-printarea input[type="checkbox"], .custom-options-basecolor input[type="checkbox"]'
  jQuery(
    '.custom-options-printarea input[type="checkbox"], .custom-options-printarea-upload input[type="file"]'
  ).on("change", function () {
    if (jQuery('.product.product-type-simple').length > 0) {
      jQuery('input[name="quantity"]').prop("disabled", false);
      // Optionally, you can set a default quantity for simple products
      jQuery(".single_add_to_cart_button").prop("disabled", false);
      jQuery('input[name="quantity"]').val(1);
      // for general form      
        let fileInputs = jQuery(
            "#custom_print_area_centerfront_file, #custom_print_area_centerback_file, #custom_print_area_leftsleeve_file, #custom_print_area_rightsleeve_file, #custom_print_area_leftchest_file, #custom_print_area_rightchest_file, #custom_print_area_customposition_file, #custom_print_area_allover_file"
          );
          console.log("fileInputs", fileInputs);
          fileInputs.each(function () {
            let fileInput = jQuery(this);
            let inputName = fileInput.attr("name");
            
            // if (fileInput.is(":visible")) {
              console.log("File input is visible, attaching change event");
  
              fileInput.on("change", function () {
                let selectedFile = this.files[0];
                console.log('selectedfile', selectedFile);
                if (selectedFile) {
                  jQuery(".single_add_to_cart_button").prop("disabled", true);
                  console.log('selectedfile', selectedFile);
                  response = validateAndUploadFile(selectedFile, inputName);
                  console.log("response for file upload", response);
                  jQuery(".single_add_to_cart_button").prop("disabled", false);
                }
              });
            // }
          });
    } else {
      let checkboxValue;
      if ($(this).is('input[type="checkbox"]')) {
        checkboxValue = $(this).val();
      }
      let filecontainer = $(".custom-options-printarea-upload");
      // let fileUploadField = $('.custom_print_area_' + checkboxValue + '_file');
      let fileUploadField = $(".custom_print_area_" + checkboxValue);
      filecontainer.removeClass("hidden-uploads");
      fileUploadField.removeClass("hidden-uploads");
      

      let fileInputs = $(
        "#custom_print_area_centerfront_file, #custom_print_area_centerback_file, #custom_print_area_leftsleeve_file, #custom_print_area_rightsleeve_file, #custom_print_area_leftchest_file, #custom_print_area_rightchest_file, #custom_print_area_customposition_file, #custom_print_area_allover_file"
      );
      let ck = jQuery('.custom-options-printarea input[type="checkbox"]');
      let displayMisc = false;
      ck.each(function () {
        if (this.checked) {
          $(".custom-product-notice").removeClass("hidden-uploads");
          displayMisc = true;
        }
      });
      fileInputs.each(function () {
        let fileInput = $(this);
        let inputName = fileInput.attr("name");
        if (fileInput.is(":visible")) {
          console.log("File input is visible, attaching change event");

          fileInput.on("change", function () {
            let selectedFile = this.files[0];
            console.log('selectedfile', selectedFile);
            if (selectedFile) {
              
              console.log('selectedfile', selectedFile);
              response = validateAndUploadFile(selectedFile, inputName);
              console.log("response for file upload", response);
              
            }
          });
        }
      });
      if (displayMisc) {
        // If displayMisc is true, remove the hidden-upload class
        $(".custom-product-notice").removeClass("hidden-uploads");
        $(".custom-options-printarea-upload-h4").removeClass("hidden-uploads");
      } else {
        $(".custom-product-notice").addClass("hidden-uploads");
        $(".custom-options-printarea-upload-h4").addClass("hidden-uploads");
      }
      // console.log("fileUploadField", fileUploadField);
      if ($(this).prop("checked")) {
        fileUploadField.closest("label").show();
        fileUploadField.show();
      } else {
        fileUploadField.closest("label").hide();
        fileUploadField.hide();
      }
    }
  });

  jQuery('.custom-options-basecolor input[type="checkbox"]').on(
    "change",
    function () {
      let checkboxValue;
      let countt = jQuery(
        '.custom-options-basecolor input[type="checkbox"]'
      ).filter(":checked").length;
      if ($(this).is('input[type="checkbox"]')) {
        checkboxValue = $(this).val();
        let variation_id = $(this).data("variation-id");
        jQuery("input.variation_id").val(variation_id);
        if (jQuery(`#custom_base_color_${checkboxValue}`).prop("checked")) {
          jQuery("#color").val(checkboxValue).trigger("change");
          let sizep = jQuery("#talla").find("option").eq(1).val();
          jQuery("#talla").val(sizep).trigger("change");
          // $("#talla").val("M");
          // jQuery('select[name="attribute_color"]').trigger("change");
          jQuery(".single_add_to_cart_button").prop("disabled", false);
          jQuery(`.custom-options-size-${checkboxValue}`).find("input[type='number']").first().val(1);
          // if (countt == 1) {
          //   jQuery(
          //     `.custom-options-size-${checkboxValue} #custom_size_m_${checkboxValue}`
          //   ).val(1);
          // }
          jQuery(
            `.custom-options-color-container.custom-options-sizes-${checkboxValue}`
          ).css("display", "flex");
        } else {
          jQuery("#color").val(checkboxValue).trigger("change");
          // jQuery('select[name="attribute_color"]').trigger("change");
          jQuery(".single_add_to_cart_button").prop("disabled", true);
          jQuery(
            `.custom-options-color-container.custom-options-sizes-${checkboxValue}`
          ).css("display", "none");

          if (countt == 0) {
            // jQuery(
            //   `.custom-options-size-${checkboxValue} #custom_size_m_${checkboxValue}`
            // ).val(0);
            // jQuery(".input-text.qty").val(0);
          }
        }
        console.log(
          "checkbox color",
          `custom-options-sizes-${checkboxValue} and variation_id : ${variation_id} and countt : ${countt}`
        );
      }
    }
  );
 
});

let initialPriceString = jQuery(".woocommerce-Price-amount").text().trim();
let initialPrice = parseFloat(initialPriceString.replace(/[^\d.-]/g, ""));




function updateQtyBasedOnSizeQty() {
  // Listen for change event on input[type="number"]

  let sizeQtyInputs = jQuery(
    '.custom-options-color-container input[type="number"]'
  );
  let totalQty = 0;

  sizeQtyInputs.each(function () {
    let qty = parseInt(jQuery(this).val()) || 0;
    totalQty += qty;
  });
  if(totalQty > 0){
    let qtyInput = jQuery(".input-text.qty");
    qtyInput.val(totalQty);
    qtyInput.attr("readonly", true); // Set the readonly attribute
    jQuery('input[name="custom_hidden_field_for_qty"]').val(totalQty);
    // Trigger the change event to update WooCommerce's internal state
    qtyInput.trigger("change");
    jQuery(".single_add_to_cart_button").prop("disabled", false);
  } else {
    jQuery(".single_add_to_cart_button").prop("disabled", true);
    jQuery('input[name="quantity"]').val(0);
    jQuery('input[name="quantity"]').prop("disabled", true);   
  }
}

function collectVariationData() {
  var variationData = [];
  jQuery('.custom-options-color-container input[type="number"]').each(function() {
      var variationID = jQuery(this).data('variation-id');
      var value = jQuery(this).val();
      
      variationData.push({ variationID: variationID, qty: value });
  });
  console.log(variationData);
  // Set the value of the hidden input field
  jQuery('#varids').val(JSON.stringify(variationData)); // Convert array to JSON string
}


// Attach the updateQtyBasedOnSizeQty function to the change event of size quantity inputs
jQuery('.custom-options-color-container input[type="number"]').on(
  "change",
  updateQtyBasedOnSizeQty
);

jQuery('.custom-options-color-container input[type="number"]').on(
  "change",
  collectVariationData
);

// Stock functions:
function get_custom_pricing(type, color, quantity) {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      type: "POST",
      url: custom_pricing_data.ajax_url,
      data: {
        action: "get_custom_pricing",
        type: type,
        color: color,
        quantity: quantity,
      },
      success: function (response) {
        let value = parseFloat(response);
        resolve(value);
      },
      error: function (error) {
        reject(error);
      },
    });
  });
}

// Function to fetch and store custom pricing data
function fetchCustomPricingData(type, color, quantity) {
  return jQuery
    .ajax({
      url: custom_pricing_data.ajax_url,
      method: "POST",
      data: {
        action: "get_custom_pricing",
        type: type,
        color: color,
        quantity: quantity,
      },
      dataType: "text",
    })
    .then(function (data) {
      console.log("PP", parseFloat(data));
      return parseFloat(data);
    });
}



function updateInput38Value() {
  let input49Value = jQuery('input[name="input_49.1"]:checked').val();
  console.log("updateInput38Value fire after -> updateqtyPrice function fired");
  if (input49Value === "White|0") {
    let totalValue = 0;
    inputIDs.forEach(function (inputID) {
      let inputValue = parseInt(jQuery("#" + inputID).val());
      totalValue += isNaN(inputValue) ? 0 : inputValue;
    });

    if (totalValue === 0) {
      alert("Total Qty can not be 0. Please update the inputs.");
    } else {
      jQuery("#input_17_38").val(totalValue);
    }
  }
}
function updateProductPrice() {
  let checkboxes = jQuery('.gfield_checkbox input[type="checkbox"]');
  let qty = parseInt(jQuery("#input_17_38").val());
  let newPrice = initialPrice;

  checkboxes.each(function () {
    if (this.checked) {
      let checkboxValue = jQuery(this).val();

      if (checkboxValue && checkboxValue.includes("|")) {
        let checkboxParts = checkboxValue.split("|");
        let printAreaSize = checkboxParts[0].trim();
        let color = checkboxParts[1].trim();

        if (
          printAreaSize === "Centre Front" ||
          printAreaSize === "Centre Back"
        ) {
          printAreaSize = "Large";
        } else {
          printAreaSize = "Small";
        }

        let input49Value = jQuery('input[name="input_49.1"]:checked').val();
        if (input49Value === "White|0") {
          let totalValue = 0;
          inputIDs.forEach(function (inputID) {
            let inputValue = parseInt(jQuery("#" + inputID).val());
            totalValue += isNaN(inputValue) ? 0 : inputValue;
          });
          jQuery("#input_17_38").val(totalValue);
          console.log("price block fired", typeof qty, qty);
          /*
                    if((totalValue) > 5){  
                      let price = parseFloat(printAreaPrices[printAreaSize]['White'][1]);
						console.log(" if priqty > 5 -> block fired", typeof(price), price);
                      newPrice += price;
                    } else if ((totalValue) > 10) { 
                      let price = parseFloat(printAreaPrices[printAreaSize]['White'][2]);
                      newPrice += price;
                      } else {
                        let price = parseFloat(printAreaPrices[printAreaSize]['White'][3]);
                      newPrice += price;
                      }
                      */
          const step1 = 5;
          const step2 = 10;
          const step3 = 21;
          if (totalValue > step1 && totalValue < step2) {
            let price = parseFloat(printAreaPrices[printAreaSize]["White"][5]);
            newPrice += price;
          } else if (totalValue > step2) {
            let price = parseFloat(printAreaPrices[printAreaSize]["White"][10]);
            newPrice += price;
          } else {
            let price = parseFloat(printAreaPrices[printAreaSize]["White"][1]);
            newPrice += price;
          }
        }
      }
    }
  });

  let formattedPrice = "€" + newPrice.toFixed(2);
  jQuery(".woocommerce-Price-amount").html(
    '<span class="woocommerce-Price-currencySymbol">' +
      formattedPrice +
      "</span>"
  );

  jQuery("#input_17_48").removeAttr("readonly");
  jQuery("#input_17_48").val(newPrice.toFixed(2));
  jQuery("#input_17_48").attr("readonly", "readonly");
}

jQuery('.gfield_checkbox input[type="checkbox"]').on("change", updateQtyPrice);

function updateQtyPrice() {
  console.log("updateqtyPrice function fired");
  updateInput38Value();
  updateProductPrice();
}



// jQuery(document).ready(function ($) {
//   console.log("Last code is running... for disabling cart button");
//   jQuery(".single_add_to_cart_button").attr("disabled", true);
//   jQuery('input[name="quantity"]').val(0);
//   jQuery('input[name="quantity"]').prop("disabled", true); 
// });



// jQuery(document).ajaxComplete(function() {
  // jQuery(".single_add_to_cart_button").prop("disabled", true);
  // jQuery('input[name="quantity"]').val(0);
  // jQuery('input[name="quantity"]').prop("disabled", true);
// });



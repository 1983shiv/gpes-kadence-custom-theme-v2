jQuery(document).ready(function ($) {
    $('.dropdown-check-list').click(function(){
        // $(this).siblings('ul.items').toggle();
      console.log("drop clicked");
        // let checkList = $(this).siblings('ul.items');
        let checkList = $('ul.items');
  
        if (checkList.hasClass("visible")) {
          checkList.removeClass("visible");
        } else {
          checkList.addClass("visible");
        }
    });

    // $("#list-pa_marca-de-ropa").click(function () {
    //   let checkList = $("#list-pa_marca-de-ropa");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_mangas").click(function () {
    //   let checkList = $("#list-pa_mangas");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_cuello").click(function () {
    //   let checkList = $("#list-pa_cuello");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_genero").click(function () {
    //   let checkList = $("#list-pa_genero");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_edad").click(function () {
    //   let checkList = $("#list-pa_edad");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_categoria-eco").click(function () {
    //   let checkList = $("#list-pa_categoria-eco");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_eco").click(function () {
    //   let checkList = $("#list-pa_eco");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_tejido").click(function () {
    //   let checkList = $("#list-pa_tejido");
  
    //   if (checkList.hasClass("visible")) {
    //     checkList.removeClass("visible");
    //   } else {
    //     checkList.addClass("visible");
    //   }
    // });
    // $("#list-pa_color-primario").click(function () {
      // let checkList = $("#list-pa_color-primario");
  
      // if (checkList.hasClass("visible")) {
      //   checkList.removeClass("visible");
      // } else {
      //   checkList.addClass("visible");
      // }
    // });
  });
  
  let sel_filter = [];
  
  jQuery(document).ready(function ($) {
  
          let currentUrl = window.location.href;
          // Extract the query string from the URL
          let queryString = currentUrl.split('?')[1];
          console.log("queryString", queryString);
          // Initialize arrays to store attributeSlug and selectedValues
          let attributeSlug = [];
          let selectedValues = [];
  
          // Parse the query string
          if (queryString) {
              // Split the query string into key-value pairs
              let keyValuePairs = queryString.split('&');
  
              // Loop through key-value pairs
              for (let i = 0; i < keyValuePairs.length; i++) {
                  // Split each key-value pair into key and value
                  let pair = keyValuePairs[i].split('=');
  
                  // Check if both key and value exist
                  if (pair.length === 2) {
                      // Add key to attributeSlug array and value to selectedValues array
                      attributeSlug.push(pair[0]);
                      selectedValues.push(pair[1]);
                  }
              }
          }
  
          // Display the results in the console (you can modify this part based on your needs)
          console.log('attributeSlug:', attributeSlug);
          console.log('selectedValues:', selectedValues);
  
    // let storedValues = localStorage.getItem('selectedValues');
    // let selectedValues = storedValues ? JSON.parse(storedValues) : [];
  
    updateActiveFilters(selectedValues);
    let categoryID = $("#attribute-filter").data("category-id");
  
    // Function to show a spinner
    function showSpinner() {
      $(".loading").css("display", "block");
      $(".loading .spinner").css("display", "block");
    }
  
    // Function to hide the spinner
    function hideSpinner() {
      $(".loading").css("display", "none");
      $(".loading .spinner").css("display", "none");
    }
    
    function showAllProducts() {
      $.ajax({
        type: "POST",
        url: ajax_params.ajax_url,
        data: {
          action: "filter_products",
          attribute_value: "", // Empty attribute value to show all products
          category_id: categoryID,
        },
        success: function (response) {
          $(".products").html(response);
        },
      });
    }
  
    // Initialize the page with all products displayed
    // showAllProducts();
    // let attributeSlug = [];
    $('input[name="terms-ckbox"]').change(function () {
      let selectedValues = [];
      let attributeSlug = [];
      $('input[name="terms-ckbox"]:checked').each(function () {
        selectedValues.push($(this).val());
        attributeSlug.push($(this).data('attribute-slug'));
        if ($.inArray(attributeSlug, sel_filter) === -1) {
          // If attributeSlug is not already in sel_filter, push it
            sel_filter.push(attributeSlug);
        }
        console.log("ajax url " + ajax_params.ajax_url,);
      });
  
      console.log("selectedValues", selectedValues, attributeSlug);
      // let attributeValue = selectedValues.join(",");
      // let attributeSlugs = attributeSlug.join(",");
      let attributeValue = selectedValues;
      let attributeSlugs = attributeSlug;
      if (selectedValues.length === 0) {
        // If no attributes are selected, show all products
        showAllProducts();
      } else {
        // Assuming selectedValues and attributeSlug are arrays
        // let url = `https://v3.garmentprinting.es/wp-admin/admin-ajax.php?action=filter_products&post_type=product&product_cat=${categoryID}`;
        let currentUrl = window.location.href;
        let url = currentUrl.split('?')[0] + '?'; // Get the base URL
        
        // Loop through the selected values and attribute slugs
        parts = '';
        for (let i = 0; i < attributeValue.length; i++) {
          parts += '&' + attributeSlugs[i] + '=' + attributeValue[i];
        }
        // localStorage.setItem('selectedValues', JSON.stringify(selectedValues));
        // localStorage.setItem('attributeSlug', JSON.stringify(attributeSlug));
  
        window.location.href = (url + parts);
        updateActiveFilters(selectedValues);
  
        // console.log("url is : ", url);
        // $.ajax({
        //   type: "POST",
        //   url: ajax_params.ajax_url,
        //   data: {
        //     action: "filter_products",
        //     attribute_value: attributeValue,
        //     category_id: categoryID,
        //     attribute_slug: attributeSlugs
        //   },
        //   success: function (response) {
        //     $(".products").html(response);
        //   },
        // });
      }
      updateActiveFilters(selectedValues);
      // Trigger the AJAX request with activeFilters
      console.log(`check selected values ${selectedValues}`);
      filterProducts(selectedValues, attributeSlugs);
    });
  
    // Function to update the active filters container
    function updateActiveFilters(filters) {
      console.log("from updateActiveFilters", filters);
      let activeFiltersContainer = $(".active-filters-container");
      activeFiltersContainer.empty();
  
      if (filters.length > 0) {
        activeFiltersContainer.append("<span>Active Filters:</span>");
        filters.forEach(function (filter) {
          activeFiltersContainer.append(
            '<div class="active-filter">' +
              filter +
              ' <button class="remove-active-filter" data-filter="' +
              filter +
              '">x</button></div>'
          );
        });
      }
    }
  
    // Remove active filter on button click
    $(document).on("click", ".remove-active-filter", function () {
      let filterToRemove = $(this).data("filter");
      $('input[name="terms-ckbox"][value="' + filterToRemove + '"]')
        .prop("checked", false)
        .change();
      showSpinner();
      let selectedValues = [];
      let attributeSlug_dd = [];
      
      $('input[name="terms-ckbox"]:checked').each(function () {
          selectedValues.push($(this).val());
          attributeSlug_dd.push($(this).data('attribute-slug'));
      });
      localStorage.setItem('selectedValues', JSON.stringify(selectedValues));
      localStorage.setItem('attributeSlug', JSON.stringify(attributeSlug));
      
      console.log(`selectedValues: ${selectedValues} and attributeSlug_dd: ${attributeSlug_dd}`);
      // Update and load products based on the remaining active filters
      filterProducts(selectedValues, attributeSlug_dd);
      console.log("remove active filter block run....");
    });
  
    // Function to filter products using AJAX
    function filterProducts(filters, attributeSlug) {
      var categoryID = $("#attribute-filter").data("category-id");
      console.log("categoryID from filterproducts jquery", categoryID, attributeSlug);
      showSpinner();
        $.ajax({
          type: "POST",
          url: ajax_params.ajax_url,
          data: {
            action: "filter_products",
            attribute_value: filters.join(","),
            category_id: categoryID,
            attribute_slug: attributeSlug,
          },
          success: function (response) {
            $(".products").html(response);
            console.log("from filterproducts", attributeSlug);
            // console.log(response);
            // Hide spinner after products are loaded
            hideSpinner();
          },
          complete: function () {
            // Hide spinner in case of error or completion
            hideSpinner();
          },
        });
      }
    
  });
  
  // Initialize lastAttributeSlug to an empty string
  // filterrProducts.lastAttributeSlug = "";
  
  /*
  
  jQuery(document).ready(function ($) {
      // Remove active filter on button click
      $(document).on('click', '.remove-active-filter', function () {
          var filterToRemove = $(this).data('filter');
          $('input[name="terms-ckbox"][value="' + filterToRemove + '"]').prop('checked', false).change();
      });
  });
  
  
  
  jQuery(document).ready(function ($) {
      $('#list1').click(function () {
          let checkList = $('#list1');
          
          if (checkList.hasClass('visible')) {
              checkList.removeClass('visible');
          } else {
              checkList.addClass('visible');
          }
      });
         $('#list2').click(function () {
          let checkList = $('#list2');
          
          if (checkList.hasClass('visible')) {
              checkList.removeClass('visible');
          } else {
              checkList.addClass('visible');
          }
      });
  });
  
  jQuery(document).ready(function ($) {
            let categoryID = $('#attribute-filter').data('category-id');
          $('input[name="terms-ckbox"]').change(function () {
          let selectedValues = [];
          $('input[name="terms-ckbox"]:checked').each(function () {
              selectedValues.push($(this).val());
          });
  
          let attributeValue = selectedValues.join(',');
  
          $.ajax({
              type: 'POST',
              url: ajax_params.ajax_url,
              data: {
                  action: 'filter_products',
                  attribute_value: attributeValue,
                  category_id: categoryID,
              },
              success: function (response) {
                  $('.products').html(response);
              },
          });
      });
  });
  
  
  
  jQuery(document).ready(function ($) {
      $('.anchor').click(function () {
          var checkList = $('#list1');
          
          if (checkList.hasClass('visible')) {
              checkList.removeClass('visible');
          } else {
              checkList.addClass('visible');
          }
      });
  });
  
  
  jQuery(document).ready(function ($) {
      $('.anchor').click(function () {
          var checkList = $(this).siblings('.dropdown-check-list');
          
          if (checkList.hasClass('visible')) {
              checkList.removeClass('visible');
          } else {
              checkList.addClass('visible');
          }
      });
  });
  
  jQuery(document).ready(function ($) {
      let categoryID = $('#attribute-filter').data('category-id');
      
      $('input[name="terms-ckbox"]').change(function () {
          var selectedValues = [];
          $('input[name="terms-ckbox"]:checked').each(function () {
              selectedValues.push($(this).val());
          });
  
          var attributeValue = selectedValues.join(',');
  
          $.ajax({
              type: 'POST',
              url: ajax_params.ajax_url,
              data: {
                  action: 'filter_products',
                  attribute_value: attributeValue,
                  category_id: categoryID,
              },
              success: function (response) {
                  $('.products').html(response);
              },
          });
      });
  });
  */
  
  /*
  jQuery(document).ready(function ($) {
             let categoryID = $('#attribute-filter').data('category-id');
          $('input[name="terms-ckbox"]').change(function () {
          var selectedValues = [];
          $('input[name="terms-ckbox"]:checked').each(function () {
              selectedValues.push($(this).val());
          });
  
          var attributeValue = selectedValues.join(',');
  
          $.ajax({
              type: 'POST',
              url: ajax_params.ajax_url,
              data: {
                  action: 'filter_products',
                  attribute_value: attributeValue,
                  category_id: categoryID,
              },
              success: function (response) {
                  $('.products').html(response);
              },
          });
      });
  });
  */
  
  /*
  jQuery(document).ready(function ($) {
      let categoryID = $('#attribute-filter').data('category-id');
      $('#attribute-filter').on('change', function () {
            // console.log("Filter is working");
          var attributeValue = $(this).val();
          
          $.ajax({
              type: 'POST',
              url: ajax_params.ajax_url,
              data: {
                  action: 'filter_products',
                  attribute_value: attributeValue,
                  category_id: categoryID,
              },
              success: function (response) {
                  $('.products').html(response);
              },
          });
      });
  });
  */
  
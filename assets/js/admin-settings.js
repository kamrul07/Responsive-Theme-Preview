jQuery(document).ready(function ($) {
  // Breakpoints repeater functionality
  var breakpointIndex = $(".rtp-breakpoint-item").length;
  var optionName = rtpSettings.optionName || "rtp_settings";

  $("#rtp-add-breakpoint").on("click", function () {
    var newBreakpoint = $(
      '<div class="rtp-breakpoint-item" data-index="' +
        breakpointIndex +
        '">' +
        '<div class="rtp-breakpoint-fields">' +
        '<div class="rtp-field-row">' +
        "<label>Title</label>" +
        '<input type="text" name="' +
        optionName +
        "[default_breakpoints][" +
        breakpointIndex +
        '][title]" placeholder="Device" />' +
        "</div>" +
        '<div class="rtp-field-row">' +
        "<label>Width (px)</label>" +
        '<input type="number" name="' +
        optionName +
        "[default_breakpoints][" +
        breakpointIndex +
        '][width]" min="320" max="2560" />' +
        "</div>" +
        '<div class="rtp-field-row">' +
        "<label>Icon Image</label>" +
        '<div class="rtp-media-uploader">' +
        '<input type="text" class="rtp-media-input" name="' +
        optionName +
        "[default_breakpoints][" +
        breakpointIndex +
        '][icon]" placeholder="Enter image URL or click to upload" />' +
        '<button type="button" class="button rtp-media-upload-button" data-target="' +
        optionName +
        "[default_breakpoints][" +
        breakpointIndex +
        '][icon]">Upload</button>' +
        "</div>" +
        "</div>" +
        "</div>" +
        '<div class="rtp-breakpoint-actions">' +
        '<button type="button" class="button rtp-remove-breakpoint">Remove</button>' +
        "</div>" +
        "</div>"
    );

    $("#rtp-breakpoints-container").append(newBreakpoint);
    breakpointIndex++;
  });

  $(document).on("click", ".rtp-remove-breakpoint", function () {
    $(this).closest(".rtp-breakpoint-item").remove();
  });

  // Media uploader functionality
  var mediaUploader;

  $(document).on("click", ".rtp-media-upload-button", function (e) {
    e.preventDefault();

    var targetInput = $(this).data("target");
    var button = $(this);
    var inputField = $('input[name="' + targetInput + '"]');

    // Debug: Log the target to console
    console.log("Media uploader target:", targetInput);
    console.log("Found input field:", inputField.length, inputField);

    // Create a new media uploader instance for each click to avoid conflicts
    var mediaUploader = (wp.media.frames.file_frame = wp.media({
      title: "Choose Icon Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    }));

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      var url = attachment.url;

      inputField.val(url);

      // Update or create preview
      var previewContainer = inputField.closest(".rtp-media-uploader").find(".rtp-media-preview");
      if (previewContainer.length === 0) {
        previewContainer = $('<div class="rtp-media-preview"></div>');
        inputField.closest(".rtp-media-uploader").append(previewContainer);
      }
      previewContainer.html('<img src="' + url + '" alt="Icon Preview" style="max-width: 30px; max-height: 30px; vertical-align: middle;" />');
    });

    // Open the uploader dialog
    mediaUploader.open();
  });

  // AJAX Settings Save Functionality
  function saveSettings() {
    var settings = {};

    // Process all form fields properly
    $("#rtp-settings-form")
      .find("input, select, textarea")
      .each(function () {
        var $field = $(this);
        var name = $field.attr("name");

        // Only process fields that belong to our settings
        if (name && name.indexOf(optionName) !== -1) {
          // Extract the field name without the optionName prefix
          var fieldName = name.replace(optionName + "[", "").replace(/\]$/, "");

          // Handle different field types
          if ($field.attr("type") === "checkbox") {
            // Handle checkboxes - only include if checked
            if ($field.is(":checked")) {
              // Handle nested array fields
              if (fieldName.indexOf("[") !== -1) {
                setNestedValue(settings, fieldName, "1");
              } else {
                settings[fieldName] = "1";
              }
            }
          } else if ($field.attr("type") === "radio") {
            // Handle radio buttons - only include if checked
            if ($field.is(":checked")) {
              // Handle nested array fields
              if (fieldName.indexOf("[") !== -1) {
                setNestedValue(settings, fieldName, $field.val());
              } else {
                settings[fieldName] = $field.val();
              }
            }
          } else {
            // Handle other input types (text, number, select, textarea)
            var value = $field.val();
            // Handle nested array fields
            if (fieldName.indexOf("[") !== -1) {
              setNestedValue(settings, fieldName, value);
            } else {
              settings[fieldName] = value;
            }
          }
        }
      });

    // Handle unchecked checkboxes - set them to '0'
    $("#rtp-settings-form")
      .find('input[type="checkbox"][name^="' + optionName + '"]')
      .each(function () {
        var $field = $(this);
        var name = $field.attr("name");
        var fieldName = name.replace(optionName + "[", "").replace(/\]$/, "");

        if (!$field.is(":checked")) {
          // Handle nested array fields
          if (fieldName.indexOf("[") !== -1) {
            setNestedValue(settings, fieldName, "0");
          } else {
            settings[fieldName] = "0";
          }
        }
      });

    // Helper function to set nested values
    function setNestedValue(obj, path, value) {
      var parts = path.match(/([^\[\]]+)|\[(\d+)\]/g);
      var current = obj;

      for (var i = 0; i < parts.length - 1; i++) {
        var part = parts[i];
        if (part.indexOf("[") === 0) {
          // Array index
          var index = part.replace(/\[|\]/g, "");
          if (!current[index]) {
            current[index] = {};
          }
          current = current[index];
        } else {
          // Object key
          if (!current[part]) {
            current[part] = {};
          }
          current = current[part];
        }
      }

      // Set the final value
      var lastPart = parts[parts.length - 1];
      if (lastPart.indexOf("[") === 0) {
        // Array index
        var index = lastPart.replace(/\[|\]/g, "");
        current[index] = value;
      } else {
        // Object key
        current[lastPart] = value;
      }
    }

    // Show loading indicator
    showLoading();

    // Debug: Log the settings object
    console.log("Saving settings:", settings);

    // Send AJAX request
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "rtp_save_settings",
        nonce: rtpSettings.nonce || $("#rtp-settings-nonce").val(),
        settings: settings,
      },
      success: function (response) {
        hideLoading();
        console.log("AJAX response:", response);
        if (response.success) {
          showNotification(response.data.message, "success");
        } else {
          showNotification(response.data || "Error saving settings", "error");
        }
      },
      error: function (xhr, status, error) {
        hideLoading();
        console.error("AJAX Error:", xhr.responseText);
        showNotification("AJAX Error: " + error + " - " + xhr.responseText, "error");
      },
    });
  }

  // Show loading indicator
  function showLoading() {
    if (!$("#rtp-loading-indicator").length) {
      $("body").append(
        '<div id="rtp-loading-indicator" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; display: flex; align-items: center; justify-content: center;"><div style="background: #fff; padding: 20px; border-radius: 5px; text-align: center;"><div class="spinner" style="display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Saving settings...</p></div></div>'
      );
    }
    $("#rtp-loading-indicator").show();
  }

  // Hide loading indicator
  function hideLoading() {
    $("#rtp-loading-indicator").hide();
  }

  // Show notification
  function showNotification(message, type) {
    var className = type === "success" ? "notice-success" : "notice-error";
    var notification = $(
      '<div class="notice ' + className + ' is-dismissible" style="position: fixed; top: 30px; right: 20px; z-index: 999999; max-width: 400px;"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'
    );

    $("body").append(notification);

    // Auto-hide after 5 seconds
    setTimeout(function () {
      notification.fadeOut(function () {
        $(this).remove();
      });
    }, 5000);

    // Handle dismiss button
    notification.find(".notice-dismiss").on("click", function () {
      notification.fadeOut(function () {
        $(this).remove();
      });
    });
  }

  // Override form submission to use AJAX
  $("#rtp-settings-form").on("submit", function (e) {
    e.preventDefault();
    saveSettings();
  });

  // Add nonce field if not present
  if (!$("#rtp-settings-nonce").length) {
    $("#rtp-settings-form").append('<input type="hidden" id="rtp-settings-nonce" name="nonce" value="' + (rtpSettings.nonce || "") + '" />');
  }

  // Add CSS for spinner animation
  if (!$("#rtp-spinner-style").length) {
    $("head").append('<style id="rtp-spinner-style">@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>');
  }
});

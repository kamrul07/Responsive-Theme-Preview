jQuery(document).ready(function ($) {
  // Initialize color pickers
  $(".rtp-color-picker").wpColorPicker();

  // Initialize color pickers with alpha support for rgba values
  $(".rtp-color-picker").each(function () {
    var $this = $(this);
    var value = $this.val();

    // Check if this is an rgba value (for shadow color)
    if (value && value.indexOf("rgba") !== -1) {
      // For rgba values, we'll use a simpler approach
      $this.wpColorPicker({
        change: function (event, ui) {
          var color = ui.color.toString();
          $this.val(color);
        },
      });
    } else {
      // Standard hex color picker
      $this.wpColorPicker();
    }
  });

  // Tab functionality for WordPress-style tabs
  $(".rtp-tab-nav a").on("click", function (e) {
    e.preventDefault();
    var tab = $(this).data("tab");

    // Remove active class from all tabs and contents
    $(".rtp-tab-nav a").removeClass("nav-tab-active active");
    $(".rtp-tab-content").removeClass("active").hide();

    // Add active class to clicked tab and show corresponding content
    $(this).addClass("nav-tab-active active");
    $("#" + tab)
      .addClass("active")
      .show();
  });

  // Show first tab by default
  if ($(".rtp-tab-nav a").length > 0) {
    var firstTab = $(".rtp-tab-nav a:first");
    firstTab.addClass("nav-tab-active active");
    var firstTabId = firstTab.data("tab");
    $("#" + firstTabId)
      .addClass("active")
      .show();
  }

  // Handle form submission with visual feedback
  $("#rtp-submit").on("click", function () {
    $(this).prop("disabled", true).after('<span class="spinner is-active"></span>');
  });

  // Handle breakpoints management
  let breakpointIndex = 0;

  // Add new breakpoint row
  $("#rtp-add-breakpoint").on("click", function () {
    const container = $("#rtp-breakpoints-container");
    const newIndex = container.find(".rtp-breakpoint-row").length;

    const newRow = `
      <div class="rtp-breakpoint-row" data-index="${newIndex}">
        <input type="text" name="rtp_settings[default_breakpoints][${newIndex}][title]" placeholder="Title" class="regular-text" />
        <input type="number" name="rtp_settings[default_breakpoints][${newIndex}][width]" placeholder="Width (px)" class="small-text" min="320" max="2560" />
        <input type="text" name="rtp_settings[default_breakpoints][${newIndex}][icon]" placeholder="Icon URL or data URI" class="regular-text" />
        <button type="button" class="button rtp-remove-breakpoint">Remove</button>
      </div>
    `;

    container.append(newRow);
  });

  // Remove breakpoint row
  $(document).on("click", ".rtp-remove-breakpoint", function () {
    $(this).closest(".rtp-breakpoint-row").remove();
  });

  // Import settings
  $("#rtp-import-btn").on("click", function () {
    const fileInput = document.getElementById("rtp-import-file");
    if (fileInput.files.length === 0) {
      alert("Please select a file to import.");
      return;
    }

    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
      try {
        const settings = JSON.parse(e.target.result);

        // Create a form and submit it
        const form = $("<form>", {
          method: "POST",
          action: ajaxurl + "?action=rtp_import_settings",
        });

        // Add nonce
        form.append(
          $("<input>", {
            type: "hidden",
            name: "rtp_import_nonce",
            value: rtp_admin.nonce,
          })
        );

        // Add settings data
        form.append(
          $("<input>", {
            type: "hidden",
            name: "rtp_settings",
            value: JSON.stringify(settings),
          })
        );

        // Submit form
        form.appendTo("body").submit();
      } catch (error) {
        alert("Invalid JSON file. Please check the file format.");
      }
    };

    reader.readAsText(file);
  });

  // Export settings
  $("#rtp-export-btn").on("click", function () {
    window.location.href = ajaxurl + "?action=rtp_export_settings";
  });

  // Reset settings
  $("#rtp-reset-btn").on("click", function () {
    if (confirm("Are you sure you want to reset all settings to defaults? This action cannot be undone.")) {
      window.location.href = ajaxurl + "?action=rtp_reset_settings";
    }
  });
});

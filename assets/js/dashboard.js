jQuery(document).ready(function ($) {
  // Toggle import/export section
  $("#rtp-import-settings").on("click", function (e) {
    e.preventDefault();
    $("#rtp-import-export").slideToggle();
  });

  // Export settings
  $("#rtp-export-settings").on("click", function (e) {
    e.preventDefault();
    window.location.href = ajaxurl + "?action=rtp_action&rtp_action=export_settings";
  });

  // Reset settings with confirmation
  $("#rtp-reset-settings").on("click", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to reset all settings to defaults? This action cannot be undone.")) {
      window.location.href = ajaxurl + "?action=rtp_action&rtp_action=reset_settings";
    }
  });

  // Breakpoint management
  let currentBreakpointIndex = -1;
  let rtp_media_frame = null; // Initialize media frame variable

  // Show breakpoint modal for editing
  $(".rtp-edit-breakpoint").on("click", function () {
    currentBreakpointIndex = $(this).data("index");
    const $item = $(this).closest(".rtp-breakpoint-item");
    const title = $item.find("h4").text();
    const width = $item.find(".rtp-breakpoint-width").text().replace("px", "");

    // Get the icon data from the item (stored in a data attribute)
    const icon = $item.data("icon") || "";
    console.log("Editing breakpoint - Icon data:", icon);

    $("#rtp-breakpoint-title").val(title);
    $("#rtp-breakpoint-width").val(width);
    $("#rtp-breakpoint-icon").val(icon);

    // Show icon preview if icon exists
    $("#rtp-icon-preview").empty();
    if (icon) {
      if (icon.startsWith("data:image/")) {
        // Base64 encoded image
        const img = $("<img>").attr("src", icon);
        $("#rtp-icon-preview").append(img);
      } else if (icon.startsWith("http")) {
        // URL
        const img = $("<img>").attr("src", icon);
        $("#rtp-icon-preview").append(img);
      }
    }

    $("#rtp-breakpoint-modal").css("display", "flex");
  });

  // Show breakpoint modal for adding new
  $("#rtp-add-breakpoint").on("click", function () {
    currentBreakpointIndex = -1;
    $("#rtp-breakpoint-form")[0].reset();
    $("#rtp-breakpoint-modal").css("display", "flex");
    $("#rtp-icon-preview").empty();
  });

  // Hide breakpoint modal
  $("#rtp-cancel-breakpoint").on("click", function () {
    $("#rtp-breakpoint-modal").css("display", "none");
  });

  // Handle icon upload using WordPress Media Uploader
  $("#rtp-upload-icon").on("click", function (e) {
    e.preventDefault();

    // If the media frame already exists, reopen it
    if (rtp_media_frame) {
      rtp_media_frame.open();
      return;
    }

    // Create the media frame
    rtp_media_frame = wp.media({
      title: "Select or Upload Icon",
      button: {
        text: "Use this icon",
      },
      multiple: false, // Allow one file per click
    });

    // When an image is selected, run a callback
    rtp_media_frame.on("select", function () {
      // We set multiple to false so only get one image from the uploader
      const attachment = rtp_media_frame.state().get("selection").first().toJSON();

      // Get the image URL
      const imageUrl = attachment.url;

      // Set the icon input value
      $("#rtp-breakpoint-icon").val(imageUrl);

      // Show preview
      const img = $("<img>").attr("src", imageUrl);
      $("#rtp-icon-preview").empty().append(img);
    });

    // Open the modal
    rtp_media_frame.open();
  });

  // Save breakpoint
  $("#rtp-save-breakpoint").on("click", function () {
    const title = $("#rtp-breakpoint-title").val().trim();
    const width = parseInt($("#rtp-breakpoint-width").val());
    const icon = $("#rtp-breakpoint-icon").val().trim();

    if (!title || !width) {
      alert("Please enter both title and width for the breakpoint.");
      return;
    }

    if (width < 320 || width > 2560) {
      alert("Width must be between 320 and 2560 pixels.");
      return;
    }

    // Show loading state
    $(this).prop("disabled", true).html('<span class="dashicons dashicons-spinner"></span> Saving...');

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "rtp_save_breakpoint",
        nonce: rtp_dashboard.nonce,
        index: currentBreakpointIndex,
        title: title,
        width: width,
        icon: icon,
      },
      success: function (response) {
        console.log("Save response:", response);
        if (response.success) {
          // Close modal
          $("#rtp-breakpoint-modal").css("display", "none");

          // Show success message
          alert("Breakpoint saved successfully!");

          // Reload the page to refresh the breakpoints list
          location.reload();
        } else {
          console.error("Error saving breakpoint:", response);
          alert("Error saving breakpoint: " + response.data);
          // Restore button state
          $("#rtp-save-breakpoint").prop("disabled", false).html("Save");
        }
      },
      error: function () {
        alert("Error saving breakpoint. Please try again.");
        // Restore button state
        $("#rtp-save-breakpoint").prop("disabled", false).html("Save");
      },
    });
  });

  // Delete breakpoint
  $(document).on("click", ".rtp-delete-breakpoint", function (e) {
    e.preventDefault();
    e.stopPropagation(); // Prevent event bubbling

    const index = $(this).data("index");

    if (!confirm("Are you sure you want to delete this breakpoint?")) {
      return;
    }

    // Show loading state
    $(this).prop("disabled", true).html('<span class="dashicons dashicons-spinner"></span>');

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "rtp_delete_breakpoint",
        nonce: rtp_dashboard.nonce,
        index: index,
      },
      success: function (response) {
        console.log("Delete response:", response);
        if (response.success) {
          // Remove the deleted item from the DOM with animation
          $(`.rtp-delete-breakpoint[data-index="${index}"]`)
            .closest(".rtp-breakpoint-item")
            .fadeOut(300, function () {
              $(this).remove();
            });
        } else {
          console.error("Error deleting breakpoint:", response);
          alert("Error deleting breakpoint: " + response.data);
          // Restore button state
          $(`.rtp-delete-breakpoint[data-index="${index}"]`).prop("disabled", false).html("Delete");
        }
      },
      error: function () {
        console.error("Error deleting breakpoint. Please try again.");
        alert("Error deleting breakpoint. Please try again.");
        // Restore button state
        $(`.rtp-delete-breakpoint[data-index="${index}"]`).prop("disabled", false).html("Delete");
      },
    });
  });

  // Handle inline delete button
  $(document).on("click", ".rtp-delete-breakpoint-inline", function (e) {
    e.preventDefault();
    e.stopPropagation(); // Prevent event bubbling

    const index = $(this).data("index");
    console.log("Delete button clicked for index:", index);

    if (!confirm("Are you sure you want to delete this breakpoint?")) {
      return;
    }

    // Show loading state
    $(this).prop("disabled", true).html('<span class="dashicons dashicons-spinner"></span>');

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "rtp_delete_breakpoint",
        nonce: rtp_dashboard.nonce,
        index: index,
      },
      success: function (response) {
        console.log("Delete response:", response);
        if (response.success) {
          // Remove the deleted item from the DOM with animation
          $(`.rtp-delete-breakpoint-inline[data-index="${index}"]`)
            .closest(".rtp-breakpoint-item")
            .fadeOut(300, function () {
              $(this).remove();
            });
        } else {
          console.error("Error deleting breakpoint:", response);
          alert("Error deleting breakpoint: " + response.data);
          // Restore button state
          $(`.rtp-delete-breakpoint-inline[data-index="${index}"]`).prop("disabled", false).html('<span class="dashicons dashicons-trash"></span>');
        }
      },
      error: function () {
        console.error("Error deleting breakpoint. Please try again.");
        alert("Error deleting breakpoint. Please try again.");
        // Restore button state
        $(`.rtp-delete-breakpoint-inline[data-index="${index}"]`).prop("disabled", false).html('<span class="dashicons dashicons-trash"></span>');
      },
    });
  });

  // Close modal when clicking outside
  $(document).on("click", function (e) {
    if ($(e.target).is("#rtp-breakpoint-modal")) {
      $("#rtp-breakpoint-modal").css("display", "none");
    }
  });

  // Smooth scroll to sections when clicking on links
  $('a[href^="#"]').on("click", function (e) {
    e.preventDefault();
    const target = $(this.getAttribute("href"));
    if (target.length) {
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 50,
        },
        500
      );
    }
  });
});

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
});

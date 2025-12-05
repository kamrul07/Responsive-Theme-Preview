(function () {
  function $$(s, c) {
    return Array.prototype.slice.call((c || document).querySelectorAll(s));
  }
  var ov, frame, closeBtn, cta, titleEl;
  var currentDeviceIndex = 0;
  var loadingIndicator = null;

  // Get advanced settings
  function getAdvancedSettings() {
    return window.RTPAdvanced || {};
  }

  function createLoadingIndicator() {
    var settings = getAdvancedSettings();
    if (!settings.overlay || !settings.overlay.loadingIndicator) return;

    loadingIndicator = document.createElement("div");
    loadingIndicator.className = "rtp-loading";
    loadingIndicator.style.cssText = "position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);" + "width:40px;height:40px;border:4px solid " + (settings.overlay.loadingColor || "#2563eb") + ";" + "border-radius:50%;border-top-color:transparent;" + "animation:rtp-spin 1s linear infinite;";

    var style = document.createElement("style");
    style.textContent = "@keyframes rtp-spin{0%{transform:translate(-50%,-50%) rotate(0deg)}100%{transform:translate(-50%,-50%) rotate(360deg)}}";
    document.head.appendChild(style);

    return loadingIndicator;
  }

  function showLoading() {
    if (!loadingIndicator) return;
    var frameWrap = document.querySelector(".rtp-framewrap");
    if (frameWrap) {
      frameWrap.appendChild(loadingIndicator);
    }
  }

  function hideLoading() {
    if (loadingIndicator && loadingIndicator.parentNode) {
      loadingIndicator.parentNode.removeChild(loadingIndicator);
    }
  }

  function setActiveDevice(index) {
    var settings = getAdvancedSettings();
    var buttons = $$(".rtp-devices button", ov);
    if (buttons.length === 0) return;

    // Remove active class from all buttons
    buttons.forEach(function (btn) {
      btn.classList.remove("active");
    });

    // Add active class to current button
    if (buttons[index]) {
      buttons[index].classList.add("active");
      var w = parseInt(buttons[index].getAttribute("data-w") || "1280", 10);
      frame.style.width = w + "px";

      // Apply zoom level if set
      if (settings.preview && settings.preview.zoomLevel && settings.preview.zoomLevel !== 1) {
        frame.style.transform = "scale(" + settings.preview.zoomLevel + ")";
      } else {
        frame.style.transform = "";
      }
    }

    currentDeviceIndex = index;
  }

  function handleKeyboardNavigation(e) {
    var settings = getAdvancedSettings();
    if (!settings.keyboard || !settings.keyboard.enabled) return;

    if (!ov.classList.contains("show")) return;

    var buttons = $$(".rtp-devices button", ov);
    if (buttons.length === 0) return;

    switch (e.key) {
      case "ArrowLeft":
        e.preventDefault();
        currentDeviceIndex = Math.max(0, currentDeviceIndex - 1);
        setActiveDevice(currentDeviceIndex);
        break;
      case "ArrowRight":
        e.preventDefault();
        currentDeviceIndex = Math.min(buttons.length - 1, currentDeviceIndex + 1);
        setActiveDevice(currentDeviceIndex);
        break;
      case "Escape":
        e.preventDefault();
        if (settings.overlay && settings.overlay.closeOnEsc) {
          closeOverlay();
        }
        break;
    }
  }

  function closeOverlay() {
    if (!ov) return;
    ov.classList.remove("show");
    frame.src = "about:blank";
    hideLoading();
    document.body.classList.remove("rtp-popup-open");
  }

  document.addEventListener("DOMContentLoaded", function () {
    var settings = getAdvancedSettings();

    ov = document.getElementById("rtp-overlay");
    frame = document.getElementById("rtp-frame");
    if (!ov || !frame) return;

    closeBtn = ov.querySelector(".rtp-close");
    cta = ov.querySelector(".rtp-cta");
    titleEl = ov.querySelector("#rtp-topbar-title");

    // Create loading indicator
    loadingIndicator = createLoadingIndicator();

    // Set initial device based on settings
    var startDevice = settings.preview && settings.preview.startWithDevice ? settings.preview.startWithDevice : "desktop";
    var deviceButtons = $$(".rtp-devices button", ov);

    // Find index of starting device
    for (var i = 0; i < deviceButtons.length; i++) {
      var title = deviceButtons[i].getAttribute("title") || "";
      if (title.toLowerCase() === startDevice.toLowerCase()) {
        currentDeviceIndex = i;
        break;
      }
    }

    $$(".rtp-open").forEach(function (open) {
      open.addEventListener("click", function (e) {
        e.preventDefault();
        var mode = open.getAttribute("data-mode") || "popup";
        var url = open.getAttribute("data-url") || "";
        var title = open.getAttribute("data-title") || "";
        var postId = open.getAttribute("data-postid") || "";
        var permalink = open.getAttribute("data-permalink") || "";
        var topbar = open.getAttribute("data-topbar") || "#0f172a";
        var ctaText = open.getAttribute("data-cta-text") || "";
        var ctaLink = open.getAttribute("data-cta-link") || "";

        if (mode === "page" && permalink) {
          window.location.href = permalink;
          return;
        }

        openOverlay(url, postId, title);
        var tb = document.querySelector(".rtp-overlay .rtp-topbar");
        if (tb) tb.style.background = topbar || "#0f172a";
        if (cta) cta.textContent = ctaText || "Open Live";
        if (cta && (ctaLink || url)) cta.setAttribute("href", ctaLink || url);

        // Set active device after opening overlay
        setActiveDevice(currentDeviceIndex);
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener("click", closeOverlay);
    }

    // Handle overlay click outside to close
    if (settings.overlay && settings.overlay.closeOnClick) {
      ov.addEventListener("click", function (e) {
        if (e.target === ov) {
          closeOverlay();
        }
      });
    }

    // Device button clicks
    $$(".rtp-devices button", ov).forEach(function (btn, index) {
      btn.addEventListener("click", function () {
        setActiveDevice(index);
      });
    });

    // Keyboard navigation
    document.addEventListener("keydown", handleKeyboardNavigation);

    // Frame load events for loading indicator
    if (frame) {
      frame.addEventListener("loadstart", showLoading);
      frame.addEventListener("load", hideLoading);
    }
  });

  function openOverlay(url, postId, title) {
    if (!ov || !frame) return;
    frame.src = url || "";
    if (cta) {
      cta.setAttribute("href", url || "#");
    }
    if (titleEl) {
      titleEl.textContent = title || "";
    }
    ov.classList.add("show");
    // Force body class addition
    setTimeout(function () {
      document.body.classList.add("rtp-popup-open");
    }, 10);

    try {
      if (postId && window.RTPTrack && RTPTrack.ajax) {
        var fd = new FormData();
        fd.append("action", "rtp_track_view");
        fd.append("nonce", RTPTrack.nonce);
        fd.append("post_id", postId);
        fetch(RTPTrack.ajax, { method: "POST", body: fd });
      }
    } catch (e) {}
  }
})();

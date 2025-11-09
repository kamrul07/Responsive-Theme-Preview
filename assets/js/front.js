(function () {
  function $$(s, c) {
    return Array.prototype.slice.call((c || document).querySelectorAll(s));
  }
  var ov, frame, closeBtn, cta, titleEl;
  document.addEventListener("DOMContentLoaded", function () {
    ov = document.getElementById("rtp-overlay");
    frame = document.getElementById("rtp-frame");
    if (!ov || !frame) return;
    closeBtn = ov.querySelector(".rtp-close");
    cta = ov.querySelector(".rtp-cta");
    titleEl = ov.querySelector("#rtp-topbar-title");

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
      });
    });
    if (closeBtn) {
      closeBtn.addEventListener("click", function () {
        ov.classList.remove("show");
        frame.src = "about:blank";
      });
    }
    $$(".rtp-devices button", ov).forEach(function (btn) {
      btn.addEventListener("click", function () {
        var w = parseInt(btn.getAttribute("data-w") || "1280", 10);
        frame.style.width = w + "px";
      });
    });
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

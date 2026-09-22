/**
 * FAQ accordion: closes every other <details> inside the same
 * ".wp-faq-accordion" group when one of them is opened.
 */
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".wp-faq-accordion").forEach(function (accordion) {
    // Capture phase: the native "toggle" event does not bubble in every browser.
    accordion.addEventListener(
      "toggle",
      function (event) {
        var target = event.target;

        if (target.tagName !== "DETAILS" || !target.open) {
          return;
        }

        accordion.querySelectorAll("details[open]").forEach(function (details) {
          if (details !== target) {
            details.open = false;
          }
        });
      },
      true,
    );
  });
});

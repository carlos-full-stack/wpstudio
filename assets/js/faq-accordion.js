/**
 * FAQ accordion: animates the open/close height of each <details> inside a
 * ".wp-faq-accordion" group (native <details> toggles instantly otherwise)
 * and keeps only one item open at a time within the group.
 */
document.addEventListener("DOMContentLoaded", function () {
  var ANIMATION_DURATION = 300;
  var prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;

  function FaqItem(details, group) {
    this.el = details;
    this.summary = details.querySelector("summary");
    this.group = group;
    this.animation = null;
    this.isClosing = false;
    this.isExpanding = false;

    this.summary.addEventListener("click", this.onClick.bind(this));
  }

  // Collapsed height = summary + the details element's own padding (the
  // padding lives on .wp-faq-item itself, not on a separate content wrapper).
  FaqItem.prototype.getCollapsedHeight = function () {
    var styles = window.getComputedStyle(this.el);
    var paddingTop = parseFloat(styles.paddingTop) || 0;
    var paddingBottom = parseFloat(styles.paddingBottom) || 0;
    return this.summary.offsetHeight + paddingTop + paddingBottom + "px";
  };

  FaqItem.prototype.onClick = function (event) {
    event.preventDefault();

    if (prefersReducedMotion) {
      this.el.open = !this.el.open;
      if (this.el.open) {
        this.group.closeOthers(this);
      }
      return;
    }

    this.el.style.overflow = "hidden";

    if (this.isClosing || !this.el.open) {
      this.group.closeOthers(this);
      this.open();
    } else if (this.isExpanding || this.el.open) {
      this.shrink();
    }
  };

  FaqItem.prototype.shrink = function () {
    this.isClosing = true;

    var startHeight = this.el.offsetHeight + "px";
    var endHeight = this.getCollapsedHeight();

    if (this.animation) {
      this.animation.cancel();
    }

    this.animation = this.el.animate(
      { height: [startHeight, endHeight] },
      { duration: ANIMATION_DURATION, easing: "ease-out" },
    );
    this.animation.onfinish = this.onAnimationFinish.bind(this, false);
    this.animation.oncancel = function () {
      this.isClosing = false;
    }.bind(this);
  };

  FaqItem.prototype.open = function () {
    this.el.style.height = this.el.offsetHeight + "px";
    this.el.open = true;
    window.requestAnimationFrame(this.expand.bind(this));
  };

  FaqItem.prototype.expand = function () {
    this.isExpanding = true;

    var startHeight = this.el.offsetHeight + "px";
    var endHeight = this.el.scrollHeight + "px";

    if (this.animation) {
      this.animation.cancel();
    }

    this.animation = this.el.animate(
      { height: [startHeight, endHeight] },
      { duration: ANIMATION_DURATION, easing: "ease-out" },
    );
    this.animation.onfinish = this.onAnimationFinish.bind(this, true);
    this.animation.oncancel = function () {
      this.isExpanding = false;
    }.bind(this);
  };

  FaqItem.prototype.onAnimationFinish = function (open) {
    this.el.open = open;
    this.animation = null;
    this.isClosing = false;
    this.isExpanding = false;
    this.el.style.height = "";
    this.el.style.overflow = "";
  };

  // Shrinks this item if it's open (or mid-expand) when another one opens.
  FaqItem.prototype.closeIfOpen = function () {
    if (this.el.open || this.isExpanding) {
      this.shrink();
    }
  };

  function FaqGroup(accordion) {
    this.items = [];

    var self = this;
    accordion
      .querySelectorAll("details.wp-faq-item")
      .forEach(function (details) {
        self.items.push(new FaqItem(details, self));
      });
  }

  FaqGroup.prototype.closeOthers = function (current) {
    this.items.forEach(function (item) {
      if (item !== current) {
        item.closeIfOpen();
      }
    });
  };

  document.querySelectorAll(".wp-faq-accordion").forEach(function (accordion) {
    new FaqGroup(accordion);
  });
});

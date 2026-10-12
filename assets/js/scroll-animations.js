/**
 * Anima elementos con la clase "animate" al entrar en el viewport.
 * El tipo, duración, delay y repetición se configuran con clases CSS
 * (ver style.css), sin necesidad de tocar este archivo.
 */
(function () {
  if (!("IntersectionObserver" in window)) {
    return;
  }

  var elements = document.querySelectorAll(".animate");
  if (!elements.length) {
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        var el = entry.target;

        if (entry.isIntersecting) {
          el.classList.add("is-inview");

          if (!el.classList.contains("animate-repeat")) {
            observer.unobserve(el);
          }
        } else if (el.classList.contains("animate-repeat")) {
          el.classList.remove("is-inview");
        }
      });
    },
    { threshold: 0.15, rootMargin: "0px 0px -10% 0px" },
  );

  elements.forEach(function (el) {
    observer.observe(el);
  });
})();

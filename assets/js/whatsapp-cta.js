/**
 * Muestra/oculta el botón flotante de WhatsApp según lo que haya en pantalla.
 * Se oculta mientras el header (.site-header), el footer (.site-footer) o el
 * hero (.wpstudio-hero, clase opcional a añadir al bloque hero de cada página)
 * estén visibles en el viewport.
 */
(function () {
  var button = document.querySelector(".wpstudio-whatsapp-cta");
  if (!button) {
    return;
  }

  // Añade o quita selectores aquí para cambiar qué secciones ocultan el botón.
  var hideSelectors = [".site-header", ".site-footer", ".wpstudio-hero"];

  var hideTargets = [];
  hideSelectors.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      hideTargets.push(el);
    });
  });

  if (!hideTargets.length || !("IntersectionObserver" in window)) {
    button.classList.add("is-visible");
    return;
  }

  var visibleTargets = new Set();

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          visibleTargets.add(entry.target);
        } else {
          visibleTargets.delete(entry.target);
        }
      });

      button.classList.toggle("is-visible", visibleTargets.size === 0);
    },
    { threshold: 0.1 },
  );

  hideTargets.forEach(function (el) {
    observer.observe(el);
  });
})();

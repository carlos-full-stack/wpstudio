/**
 * Edit del bloque wpstudio/faq-servicio.
 * Sin build step: usa los globales `wp.*` que WordPress ya expone en el editor.
 * El render real (front y preview) lo hace siempre render.php vía ServerSideRender.
 */
(function (blocks, element, blockEditor, serverSideRender) {
  var el = element.createElement;
  var ServerSideRender = serverSideRender;

  blocks.registerBlockType("wpstudio/faq-servicio", {
    edit: function (props) {
      var blockProps = blockEditor.useBlockProps();

      return el(
        "div",
        blockProps,
        el(ServerSideRender, {
          block: "wpstudio/faq-servicio",
          attributes: props.attributes,
        }),
      );
    },
    save: function () {
      // Bloque dinámico: el contenido siempre lo pinta render.php.
      return null;
    },
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.serverSideRender,
);

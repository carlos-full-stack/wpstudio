# Reglas del proyecto — WP Studio

## Contexto

- Estamos desarrollando un child theme personalizado en WordPress 7.1 basandonos en el tema Frost de WPEngine
- Se ira pidiendo desarrollar secciones (patrones) personalizadas para el tema, basandose para ello una imagen de la seccion que adjuntare en cada caso y que nos servira como referencia (el spacing de los elementos ira siempre definido por el theme.json)
- El objetivo es, siempre que se pueda, aprovechar al maximo el codigo ya creado por wordpress o por el tema frost
- Para los estilos personalizados usaremos CSS nativo
- El design system vive en `theme.json`

## Reglas obligatorias

- Generaremos simpre codigo limpio, con los comentarios justos (solo el necesario para indicar que es cada pieza de codigo)
- Respetaremos las normas de Wordpress y la filosofia del tema frost.
- Genera los patrones en ancho completo, ya que yo me encargare despues de ponerlos en el espacio que corresponde (a veces van en una seccion de ancho completo, otras veces en una columna)

## Al generar código

- No generaremos ni textos ni imagenes.
- Si una imagen de Figma no deja claro un comportamiento (hover, versión móvil, contenido dinámico), pregunta en vez de asumir.
- No añadas dependencias, iconos, librerías o plugins nuevos sin decirlo explícitamente primero.
- No añadas código JS sin decirmelo primero.
- Las imagenes te las ire facilitando con su url, ya que las subire previamente a wordpress

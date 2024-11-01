=== Voyapp - Lugares y Cotizador de Despachos ===
Contributors: voyacl
Tags: voyapp, suubi, cotizador, envios, starken, bluexpress
Requires at least: 5.6
Requires PHP: 7.0
Tested up to: 6.6
Stable tag: 1.7.4
License: MIT License
Language: Spanish
License URI: https://opensource.org/licenses/MIT
Donate link: https://www.flow.cl/btn.php?token=innnyay

Añade los estados y ciudades de Chile y México a WooCommerce. También podrás contar con un cotizador de despachos de múltiples couriers y mucho más.

== Description ==

Añade los estados y ciudades de Chile y México a WooCommerce, además, si te registras en [Voyapp](https://app.voyapp.cl/register) podrás conseguir una API KEY para poder contar con un cotizador de despachos y seguimiento de pedidos para tu tienda.

El cotizador obtiene en tiempo real el precio del despacho (valor concreto entregado por los couriers actualmente integrados en la plataforma) en base a las dimensiones y peso del pedido, además del origen y destino de este.

Cabe destacar que a pesar de que las solicitudes de cotización son procesadas por nuestros servidores, el correcto funcionamiento depende directamente de las respuestas de los sistemas de los couriers.

Para funcionar, este plugin requiere tener instalado y activado WooCommerce en tu sitio web Wordpress.

Voyapp es totalmente compatible con el maravilloso plugin ["MkRapel Regiones y Ciudades de Chile para WC"](https://es.wordpress.org/plugins/wc-ciudades-y-regiones-de-chile/) de nuestros amigos de [Marketing Rapel](https://marketingrapel.cl/).

== Installation ==

* Ingresa al panel administrativo de Wordpress (/wp-admin), luego accede a  "Plugins" -> "Añadir nuevo". Busca «Voyapp - Lugares y Cotizador de Despachos». Presiona el botón «Instalar» y luego activa el plugin.

* Al momento de activar el plugin, contarás con todas las regiones y comunas de Chile en tu tienda WooCommerce.

* Si deseas activar el cotizador de despachos, primero debes ingresar y registrarte en [Voyapp](https://app.voyapp.cl) y luego generar una API KEY para tu dominio. En nuestro sitio web encontrarás toda la información y tutoriales necesarios para realizar este simple proceso.

* Para configurar las opciones del cotizador accede a la sección WooCommerce -> Ajustes -> Pestaña "Envío" -> Opción "Voyapp - Cotizador de Despachos".

*Opciones del Cotizador*

* Activo: Permite activar o desactivar el cotizador.
* API KEY: Aquí debes ingresar la API KEY generada para tu dominio. Para obtener la API KEY debes ingresar a [Voyapp](https://app.voyapp.cl)
* Modo de pruebas: Activa esta opción si estás recién desarrollando tu ecommerce o estás realizando las pruebas de integración. Las consultas realizadas bajo esta modalidad no serán consideradas en tu facturación mensual.
* Peso por defecto del producto: Este valor será utilizado en los productos que no tengan un peso definido en la tienda. 
* Altura por defecto del producto: Este valor será utilizado en los productos que no tengan una altura definida en la tienda.
* Ancho por defecto del producto: Este valor será utilizado en los productos que no tengan un ancho definido en la tienda.
* Largo por defecto del producto: Este valor será utilizado en los productos que no tengan un largo definido en la tienda.
* Mostrar cantidad aproximada de días que tardará el despacho: Activa esta opción si deseas mostrar la cantidad aproximada de días que demora cada courier en realizar la entrega del pedido. Este número es proporcionado directamente por la empresa de despachos. No todos los couriers entregan esta información.
* Días adicionales que tardará el despacho: Ingresa la cantidad de días que deseas sumar al valor que demora cada courier en realizar la entrega del pedido. Activa la opción anterior para poder visualizar esta información en tu tienda.
* Variación porcentual: Este campo permite aumentar/disminuir (en porcentaje) el valor final de los envíos.
* Redondear precios: Este campo permite redondear el valor del despacho a la decena superior más cercana, centena superior más cercana o millar superior más cercano.
* Ignorar comunas: Este campo permite seleccionar las comunas para las cuales Voyapp no realizará una cotización de precio de despacho.
* Habilitar Envío por pagar: Activa esta opción si deseas incluir la modalidad de despacho "Envío por pagar" en caso de que no exista una respuesta de cotización desde los servidores de los couriers.
* Envío gratis según total de compra del cliente: Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando el precio total del pedido de un comprador supera el valor especificado en este campo. Si no quieres habilitar el envío gratis mediante esta modalidad, deja este campo vacío.
* Envío gratis según destino de envío del cliente: Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando la comuna de despacho seleccionada por el comprador se encuentra seleccionada en esta lista. Si no quieres habilitar el envío gratis mediante esta modalidad, deja este campo vacío.
* Fecha de finalización de "Envío Gratis": Este campo permite establecer la fecha de finalización del "Envío Gratis" de las 2 opciones anteriores. El "Envío Gratis" se desactivará a las 23:59 de la fecha seleccionada. Si no quieres establecer una fecha límite, deja este campo vacío. Esta opción utiliza la zona horaria establecida en la sección de WordPress "Ajustes" -> "Generales" -> "Zona horaria".
* Mostrar logos de couriers: Este campo permite establecer si se desea mostrar o no los logos de los couriers en la página de checkout. Cuenta con 3 opciones: "No mostrar logos", "Mostrar logos solo a modalidades que no tienen nombre personalizado" y "Mostrar logos para todas las modalidades de despacho".

== Frequently Asked Questions ==
= No aparecen los valores de despacho =

Debes validar si:
* Ingresaste el API KEY correcto en las configuraciones del plugin
* Tu dominio está activo en el sistema [Voyapp](https://app.voyapp.cl)
* Habilitaste los couriers, modalidades y comunas de origen en el sistema [Voyapp](https://app.voyapp.cl)
También debes considerar que pueden haber ocasiones en las que los sistemas externos (Starken, Pullman Cargo, etc.) pueden presentar problemas.

= ¿Se asegura el funcionamiento del cotizador? =

El cotizador depende de varios factores, entre ellos, los sistemas externos de los couriers (Starken, Pullman GO, Blue Express, Chilexpress, Correos de Chile, Envíame, etc). Sin embargo, en caso de encontrar algún problema en nuestro servidor, software o plugin, intentaremos repararlo lo antes posible.

= ¿El cotizador es gratis? =

El cotizador no es gratis, puedes conocer nuestros atractivos precios en [Planes Voyapp](https://voyapp.cl/planes)

= ¿Qué couriers están integrados actualmente al cotizador? =

En Chile están integradas las APIs de Starken, Chilexpress, Blue Express, Correos de Chile, Pullman Cargo, Pullman GO y Envíame. En México están integradas las APIs de Envíame.

= ¿Integrarán otras APIs de couriers al cotizador? =

Sí. Queremos integrar la mayor cantidad de couriers posibles al cotizador.

= ¿Dónde puedo contactarme con ustedes? =

Si quieres realizar alguna consulta, enviar alguna sugerencia, reportar un problema, etc. No dudes en escribirnos a hola@voyapp.cl

== Changelog ==

= 1.7.4 =
* Decalaraciones de "probado hasta" de WooCommerce.
* Se añade logo a nuevo courier "Take Its".

= 1.7.3 =
* Decalaraciones de "probado hasta" de WooCommerce.
* Se cambia nombre de clase de destinos para evitar conflictos con otros plugins.

= 1.7.2 =
* Declaraciones de compatibilidad con WooCommerce Cart/Checkout Blocks
* Decalaraciones de "probado hasta" de Wordpress y WooCommerce

= 1.7.1 =
* Mejoras de compatibilidad con PHP 8+.

= 1.7.0 =
* Se mejora visualmente el formulario de ajustes del plugin.
* Se añaden opciones de configuración al formulario de seguimiento.
* Se añade lista de couriers disponibles para el seguimiento de pedidos.
* Se actualizan textos por defecto de resultados de seguimiento de pedidos.
* El formulario de seguimiento ahora muestra una lista desplegable cuando hay más de 1 courier seleccionado en las configuraciones.
* El formulario de seguimiento ahora se muestra solo si hay al menos un courier seleccionado.
* Se actualiza el endpoint de cotización a versión 2.
* Se añade fallback de courier_image en caso de que la respuesta del servidor no posea dicha propiedad.
* Se añade la posibilidad de recibir notificaciones personalizadas.
* Se agrega endpoint para obtener las configuraciones del plugin establecidas en la tienda del usuario y también la versión instalada del plugin. Esto para agilizar los procesos de soporte.
* Se añade validaciópn SSLVERIFY a requests de seguimiento.
* Se añade Timeout general a todas las requests salientes.
* Se agrega link a guía de integración de "cotizador de despacho" y "formulario de seguimiento".
* Se modifica criterio de envio gratis según valor; ahora se considera que el precio sea igual o mayor que el límite establecido en las configuraciones.
* Se añaden opciones de display de la opción "Envío Gratis": a.- Solo opción "Envío Gratis" y preseleccionada, sin mostrar otras cotizaciones Voyapp. b.- Opción "Envío Gratis" y preseleccionada, también muestra otras cotizaciones Voyapp. c.- Opción "Envío Gratis" como única opción, ninguna otra modalidad se muestra, ni siquiera de otros modulos.
* Ahora cada vez que se detecte la modalidad "Envío Gratis" de Voyapp, esta se encontrará en el primer lugar de la lista y estará seleccionada por defecto. [Sugerencia publicada](https://wordpress.org/support/topic/envio-gratis-preseleccionado-si-esta-disponible) en el foro de soporte Wordpress.
* Se elimina antigua opción "Mostrar solo courier más barato" y es remplazada por diferentes formas de display de modalidades: a.- ordenar del mas barato al mas caro. b.- ordenar del mas caro al mas barato. c.- mostrar solo mas barato. d.- mostrar solo mas caro.
* Se reordenan items del menu Voyapp en el panel de administración, ahora el orden es "Ajustes", "Listar plantillas de etiquetas" y "Crear nueva plantilla de etiqueta".
* CSS y JS del formulario de ajustes se invocan desde archivos independientes.

= 1.6.16 =
* Se modifica la manera en la que se invoca código Javascript en la página de "Finalizar Compra".

= 1.6.15 =
* La comuna Puerto Natales (CL) ahora es compatible con las direcciones de Google en el formulario de Finalizar Compra.
* Se añade logo de courier "Recíbelo".
* Se actualiza imagen de courier "por defecto".

= 1.6.14 =
* La comuna Coyhaique (CL) ahora es compatible con las direcciones de Google en el formulario de finalizar compra.

= 1.6.13 =
* Al momento de ejecutar una sentencia de tipo "require" se incluye path completo del archivo en cuestión.
* Se modifica el identificador textual de cada comuna a uno más estándar. Con esta modificación, nuestro plugin se vuelve compatible con las direcciones de Google en el formulario de finalizar compra.

= 1.6.12 =
* Se repara el display de las modalidades "Envío gratis" y "Envío por pagar" de Voyapp cuando se ha habilitado la opción de configuración "Mostrar logos de couriers".

= 1.6.11 =
* Ahora el plugin Voyapp es compatible con el sistema "High Performance Order Storage (HPOS)" de WooCommerce. Los sitios sin HPOS seguirán funcionando normalmente.

= 1.6.10 =
* Ahora la opción de configuración "Peso por defecto" acepta valores decimales.
* Se actualizan los logos de couriers a una mejor calidad y con transparencia.
* Se eliminan espacios en blanco que WooCommerce interpreta como tag "p".

= 1.6.9 =
* Se repara comportamiento de botón de apertura de ventana emergente para la función de "Cear y descargar etiquetas de despacho" en la sección de "Administración" -> "WooCommerce" -> "Pedidos" -> "Ver Pedido".

= 1.6.8 =
* Se implementa nueva opción de configuración "Mostrar logos de couriers". Este campo permite establecer si se desea mostrar o no los logos de los couriers en la página de checkout. Cuenta con 3 opciones: "No mostrar logos", "Mostrar logos solo a modalidades que no tienen nombre personalizado" y "Mostrar logos para todas las modalidades de despacho".
* Se añaden 42 logos de couriers de Chile y 1 un logo por defecto.
* Se eliminan textos que indicaban la modalidad "Beta" de los módulos "Crear y descargar etiquetas de despacho" y "Crear/Ver/Eliminar plantilla de etiquetas de despacho".

= 1.6.7 =
* Se repara [incidencia reportada](https://wordpress.org/support/topic/problema-al-editar-estado-de-ordenes) en el foro de soporte Wordpress. Ahora el pedido se puede modificar sin inconvenientes.

= 1.6.6 =
* Se añaden accesos rápidos al menú lateral del panel administrativo de Wordpress.
* Se añade la función de "Crear y descargar etiquetas de despacho" en modalidad Beta. Podrás generar archivos PDF con los datos del remitente y destinatario. Los datos del destinatario serán precargados con la información proporcionada por el cliente en el checkout y, si lo estimas conveniente, también podrás editar dichos datos. Para utilizar esta nueva función necesitas una API KEY Voyapp. Al crear una nueva etiqueta de despacho se añadirá una consulta a tu historial de consultas.
* Se añade la función de "Crear/Ver/Eliminar plantilla de etiquetas de despacho" en modalidad Beta. Podrás crear plantillas que contengan los datos del remitente y usarlos en la sección de "Crear y descargar etiquetas de despacho". Si encuentras algún problema en este nuevo módulo, no dudes en escribirnos a hola@voyapp.cl.
* Si encuentras algún problema en estos nuevos módulos, no dudes en escribirnos a hola@voyapp.cl.

= 1.6.5 =
* Algunos sitios web presentan problemas con los selectores de "Región" y "Comuna", esta actualización repara dichos selectores.
* También se implementa la mejora anterior a los sitios que utilizan el plugin "MkRapel Regiones y Ciudades de Chile para WC".
* Se añade un nuevo campo, "Barrio/Pueblo/Colonia", al formulario de checkout, disponible para los países que cuenten con ubicaciones anidadas en tres niveles, como es el caso de México. Este campo no aparecerá para los países que no lo necesitan, por ejemplo, Chile.
* Refactorización del módulo de notificaciones.

= 1.6.4 =
* En caso de que la tienda tenga seleccionado un país base que no tenga cobertura por Voyapp, de igual manera en el checkout se mostrarán las Regiones y Comunas de Chile, siempre y cuando este país se haya seleccionado como destino. Incidencia reportada por [shoppingvirtual.cl](https://www.shoppingvirtual.cl/).
* Cambios mínimos a textos.

= 1.6.3 =
* Se repara comportamiento de selector de fecha en opción 'Fecha de finalización de "Envío Gratis"'. Ahora se puede seleccionar y eliminar fecha correctamente.
* Se añaden estados y ciudades de México. 
* Ahora las configuraciones del plugin se cargan en base al país de la tienda, solo válido CL y MX. Si el país de la tienda no es válido, se cargan las configuraciones por defecto para CL.

= 1.6.2 =
* Se añade condicional is_account_page() para añadir scripts de campos anidados de Región y Comuna en las diferentes vistas de la sección "Mi Cuenta".

= 1.6.1 =
* Para realizar cotizaciones de despacho, también se utilizará el valor "código postal" (opcional), ya que algunos couriers necesitan este dato para poder cotizar correctamente. Cabe destacar que esta información no quedará guardada en nuestro sistema.

= 1.6.0 =
* Se implementa un módulo de notificaciones (no te preocupes, podrás cerrarlas). Es necesario que en tu sitio no hayas desactivado Admin Notices.
* Para realizar cotizaciones de despacho, también se utilizará el valor "total del pedido", ya que algunos couriers necesitan este dato para poder cotizar correctamente. Cabe destacar que esta información no quedará guardada en nuestro sistema.

= 1.5.1 =
* Se repara la visualización de nombres personalizados de couriers en página de "Detalle de pedido" y en correos de confirmación de pedido. Incidencia reportada por [vgamers.cl](https://vgamers.cl/).
* Agregada la opción de configuración "Combinar criterios de evaluación para Envío Gratis". Activa esta opción para combinar los criterios de evaluación de Envío Gratis. Al activar esta opción se evaluará el despacho gratis según "Total de compra" Y "Destino de envío". Por ejemplo: Despacho gratis desde los $30.000 solamente dentro de las comunas de Providencia y Macul. Mejora sugerida por [geomar.cl](https://geomar.cl/).

= 1.5.0 =
* Ahora podrás asignar nombres personalizados a cada una de las modalidades de despacho. Para personalizar un nombre de modalidad de despacho, debes ingresar a tu cuenta en la plataforma [Voyapp](https://app.voyapp.cl) y luego ir a la sección "Gestión de Couriers" del dominio en cuestión.
* Agregada la opción de configuración "Mostrar solamente el courier más barato". Este campo permite que en la página de checkout se muestre solamente el courier con la cotización de despacho de menor costo.
* Agregada la opción de configuración avanzada "Habilitar SSL Verify". Esta opción habilita el parámetro SSL Verify en las consultas. Se recomienda siempre mantenerlo activo.
* Agregada la opción de configuración "Mostrar créditos". Activa esta opción para mostrar el mensaje "Cotización de despachos por Voyapp" en la página de checkout. Este es un mensaje no invasivo y no interfiere en la compra del cliente. ¡Ayúdanos a seguir creciendo! Este campo es totalmente opcional.

= 1.4.5 =
* Se elimina declaración de función global "write_log" para evitar conflictos con otros plugins.

= 1.4.4 =
* Se repara la visualización del nombre del despacho en la página de checkout cuando este no cuenta con días de entrega. 

= 1.4.3 =
* Se repara la funcionalidad de "Mostrar cantidad aproximada de días que tardará el despacho" para que sea compatible con Envíame.io.
* Se elimina el texto de advertencia abajo del campo de configuración "¿Mostrar cantidad aproximada de días que tardará el despacho?": Si trabajas con Envíame.io, por el momento, no te recomendamos activar esta opción para así mantener el buen funcionamiento de la integración.

= 1.4.2 =
* Se agrega la palabra "hábil"/"hábiles" a la cantidad de días que demora cada courier en realizar el despacho. Cambio sugerido por el usuario [melvisnap](https://wordpress.org/support/users/melvisnap/) en su [review de nuestro plugin](https://wordpress.org/support/topic/algo-que-debia-existir-hace-anos/).
* Se agrega texto de advertencia abajo del campo de configuración "¿Mostrar cantidad aproximada de días que tardará el despacho?": Si trabajas con Envíame.io, por el momento, no te recomendamos activar esta opción para así mantener el buen funcionamiento de la integración.

= 1.4.1 =
* Se implementan las nuevas opciones que permiten mostrar la cantidad de días que tardará cada courier en realizar el despacho del paquete. Exigido por la nueva modificación a la ley de protección del consumidor en Chile. Actualmente los couriers que entregan esta información son: Starken, Blue Express y Envíame.

= 1.4.0 =
* Se implementa nueva funcionalidad de "Seguimiento de pedidos" mediante shortcode. Para más información visita la guía de [Seguimiento de pedidos](https://voyapp.cl/tutoriales/seguimiento-de-pedidos).
* Se mejora la manera en la que se calcula la opción "Envío gratis según total de compra del cliente".

= 1.3.0 =
* Fix de problemas de compatibilidad con WooCommerce 6.0+ en página de Checkout: Listas desplegables anidadas de "Región" y "Comuna" vuelven a funcionar correctamente.

= 1.2.0 =
* Agregada la opción de configuración "Redondear precios". Este campo permite redondear el valor del despacho a la decena superior más cercana, centena superior más cercana o millar superior más cercano.
* Agregada la opción de configuración "Ignorar comunas". Este campo permite seleccionar las comunas para las cuales Voyapp no realizará una cotización de precio de despacho.

= 1.1.1 =
* Fix de la opción de configuración "Fecha de finalización de 'Envío gratis'". 

= 1.1.0 =
* Agregada la opción de configuración "Envío gratis según total de compra del cliente". Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando el precio total del pedido de un comprador supera el valor especificado en este campo.
* Agregada la opción de configuración "Envío gratis según destino de envío del cliente". Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando la comuna de despacho seleccionada por el comprador se encuentra seleccionada en esta lista.
* Agregada la opción de configuración "Fecha de finalización de 'Envío gratis'". Este campo permite establecer la fecha de finalización del "Envío Gratis" de las 2 opciones anteriores. Esta opción utiliza la zona horaria establecida en la sección de WordPress "Ajustes" -> "Generales" -> "Zona horaria".
* Reimplementada la opción de configuración "Modo de pruebas".
* Aumentado el timeout de espera de respuesta de consulta.

= 1.0.1 =
* Agregada la opción de configuración "Variación porcentual". Este campo permite aumentar/disminuir (en porcentaje) el valor final de los envíos. Por defecto tiene valor 0.
* Eliminada opción de configuración "Modo de pruebas de integración".

= 1.0.0 =
* Versión inicial

== Tutoriales ==

Para saber como utilizar e integrar Voyapp, visita [nuestra página de tutoriales](https://voyapp.cl/tutoriales).

== Términos y Condiciones ==

Para más información sobre los Términos y Condiciones, accede al [siguiente enlace](https://voyapp.cl/terminos-y-condiciones).

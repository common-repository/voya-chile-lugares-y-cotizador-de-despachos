<?php
if (!class_exists('VoyaDespachosMenus')) {
  class VoyaDespachosMenus {
    
    function __construct() {
      add_action('init', [$this, 'voyacl_register_post_types']);
      add_action('admin_menu', [$this, 'mainMenu']);
      add_action( 'admin_post_'.VOYA_PLUGIN_SLUG.'-shipping-label-template-post', [$this, 'formShippingLabelProcessor'] );
    }

    function mainMenu() {
      $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNTAwLjAwMDAwMHB0IiBoZWlnaHQ9IjUwMC4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDUwMC4wMDAwMDAgNTAwLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsNTAwLjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTQ0OCA0OTM1IGMtMTkxIC00NyAtMzU0IC0xOTkgLTQxMyAtMzg2IGwtMjUgLTgwIDAgLTE5NjkgMCAtMTk2OQoyNSAtODAgYzQ2IC0xNDQgMTUyIC0yNjkgMjg3IC0zMzcgMTM3IC02OSAtMTUgLTY1IDIyMDAgLTYyIGwyMDAzIDMgNjYgMjIKYzE5MiA2NiAzNDIgMjM3IDM4NCA0MzggMjIgMTA1IDIyIDM4NjUgMCAzOTcwIC00MiAyMDEgLTE5MiAzNzIgLTM4NCA0MzgKbC02NiAyMiAtMjAxMCAyIGMtMTY4NyAxIC0yMDE5IC0xIC0yMDY3IC0xMnogbTQ1NSAtMzM4IGM3OCAtMzkgMTQyIC0xMDQgMTgwCi0xODUgMjkgLTYwIDMyIC03NyAzMiAtMTU3IDAgLTc2IC00IC05OSAtMjYgLTE0NiBsLTI3IC01NiAzMTIgLTMxMiAzMTMgLTMxMwo3OSA1NyBjMTQ1IDEwNCAzNDIgMTg2IDUyMyAyMTYgMTEyIDE5IDMxMiAxNCA0MzEgLTExIDE2NyAtMzQgMzM3IC0xMDggNDY4Ci0yMDQgMzEgLTIzIDYzIC00NiA3MSAtNTAgOSAtNiA0MSAxOSAxMDYgODQgbDkyIDkyIC0yMCA1MSBjLTE1IDM3IC0yMSA3NQotMjAgMTMyIDAgMTA0IDI3IDE3NiA5NCAyNTAgMTMyIDE0MyAzNDUgMTY3IDUwMCA1NiAyMzMgLTE2NyAyMDAgLTUyOCAtNTkKLTY0OSAtNTEgLTI0IC02OSAtMjcgLTE2MiAtMjcgLTg2IDAgLTExMyA0IC0xNTEgMjEgbC00NyAyMiAtOTEgLTkyIC05MSAtOTEKNDMgLTU4IGMxNTggLTIxMiAyNDcgLTQ3NiAyNDcgLTczNSAwIC05MiAtMjIgLTIzOSAtNTAgLTM0MiAtMjggLTEwMiAtMTE2Ci0yNzkgLTE4MyAtMzcwIGwtNTYgLTc1IDMxOCAtMzE5IDMxOCAtMzE4IDY5IDI3IGM2MCAyNCA3OCAyNyAxNTQgMjMgNzAgLTMKOTggLTEwIDE0NiAtMzMgNzUgLTM3IDE0MiAtMTA0IDE4MCAtMTgwIDI2IC01NCAyOSAtNzAgMjkgLTE2MCAwIC05MCAtMyAtMTA2Ci0yOSAtMTYwIC0zOCAtNzYgLTExMCAtMTQ5IC0xODQgLTE4MyAtNTEgLTI0IC02OSAtMjcgLTE2MiAtMjcgLTk2IDAgLTExMCAyCi0xNjMgMzAgLTc3IDM4IC0xNTggMTI2IC0xODggMjAyIC0yNyA3MiAtMzAgMTk1IC01IDI2MyBsMTcgNDQgLTMyMyAzMjMgLTMyMwozMjIgLTcxIC01MyBjLTQyNSAtMzE0IC0xMDIzIC0zMDkgLTE0NDggMTIgbC01OSA0NCAtMjA0IC0yMDQgLTIwNCAtMjA0IDI1Ci01NCBjODQgLTE4NyAwIC00MDIgLTE5MiAtNDkzIC01OSAtMjggLTc3IC0zMiAtMTUyIC0zMSAtMTU1IDEgLTI3NSA3NSAtMzQyCjIxMiAtMzAgNjEgLTMzIDc0IC0zMyAxNjIgMCA4MiA0IDEwMyAyNyAxNTIgODMgMTc3IDI3OCAyNjYgNDU4IDIwOSBsNjUgLTIxCjIwMyAyMDMgYzExMSAxMTEgMjAyIDIwNSAyMDIgMjA5IDAgNCAtMjUgNDIgLTU2IDg1IC0zMDMgNDIyIC0zMDAgMTAxMCA4CjE0MjYgbDUxIDY5IC0zMTUgMzE1IC0zMTUgMzE1IC01OCAtMTcgYy04NCAtMjUgLTIwMiAtMTcgLTI3NiAxOCAtNzAgMzQgLTE1MQoxMTggLTE4NyAxOTQgLTIzIDQ5IC0yNyA3MSAtMjcgMTQ4IDAgNzcgNCA5OSAyNyAxNDcgMTUgMzIgNDggODAgNzQgMTA4IDg4Cjk0IDE1NSAxMjEgMjkwIDExNyA3NSAtMiAxMDAgLTcgMTQ3IC0zMHoiLz4KPHBhdGggZD0iTTY2NSA0NDE1IGMtNjkgLTM3IC05OCAtODUgLTk4IC0xNjAgMCAtMTA5IDgwIC0xODIgMTg5IC0xNzMgOTQgOAoxNTggNzggMTU4IDE3MyAwIDk4IC02NSAxNjYgLTE2MiAxNzIgLTM5IDMgLTY2IC0xIC04NyAtMTJ6IG0xNDAgLTg5IGMzMiAtMzIKNDAgLTY5IDI1IC0xMDcgLTE2IC0zOSAtNDYgLTU5IC04OCAtNTkgLTkzIDAgLTEzMiAxMTUgLTU5IDE3MiAzMyAyNiA5MyAyMwoxMjIgLTZ6Ii8+CjxwYXRoIGQ9Ik0zNzE0IDM5NTIgYy01OCAtMjcgLTg3IC03MyAtOTIgLTE0MyAtOSAtMTI2IDg1IC0yMDkgMjEyIC0xODUgNjgKMTMgMTM2IDk4IDEzNiAxNzEgMCA0NiAtMzQgMTA5IC03NSAxNDAgLTQ4IDM3IC0xMjIgNDQgLTE4MSAxN3ogbTEyNyAtNzIgYzMwCi0xNyA1MiAtNjggNDUgLTEwNiAtMTggLTk0IC0xNTIgLTEwMCAtMTg3IC03IC0yOCA3NCA2OSAxNTIgMTQyIDExM3oiLz4KPHBhdGggZD0iTTE5NTIgMzE0MyBjLTQ5IC0yNCAtNzUgLTY4IC04MCAtMTMzIC00IC01NCA1IC03OCAyMjcgLTYyNSAxMzAKLTMyMCAyNDAgLTU3OSAyNTMgLTU5MiAzNCAtMzcgNzIgLTUzIDEyMyAtNTMgNTcgMCAxMDkgMjMgMTM0IDU5IDM0IDQ3IDQ3MwoxMTQzIDQ3OCAxMTkxIDQgMzQgLTEgNTYgLTE5IDg5IC0zMCA1NiAtNzUgODEgLTE0NiA4MSAtNjYgMCAtMTEzIC0xOSAtMTQwCi01NyAtMTEgLTE2IC04MiAtMjA2IC0xNTcgLTQyMyAtNzUgLTIxNyAtMTQwIC0zOTggLTE0NCAtNDAzIC00IC00IC03MCAxNzEKLTE0NSAzOTAgLTc2IDIxOSAtMTQ3IDQxMiAtMTU3IDQyOSAtMTEgMTggLTM1IDM5IC01MyA0OCAtNDMgMjAgLTEzMiAyMCAtMTc0Ci0xeiIvPgo8cGF0aCBkPSJNOTA4IDExMjAgYy00NiAtMTQgLTg1IC00OCAtMTA3IC05MyAtMjcgLTU1IC0yNyAtOTkgMCAtMTU0IDQ5IC0xMDIKMTgxIC0xMzAgMjY5IC01NyA0MyAzNyA2MCA3NSA2MCAxMzQgMCA3OSAtMzcgMTMzIC0xMTIgMTY1IC00MSAxNiAtNjggMTgKLTExMCA1eiBtMTA5IC05MiBjOSAtNyAyMiAtMjggMzAgLTQ1IDExIC0yOCAxMSAtMzggLTEgLTY3IC0yOSAtNzAgLTExMiAtODMKLTE2MSAtMjQgLTM2IDQzIC0zMyA4MiAxMCAxMjUgMzEgMzEgNDAgMzUgNzEgMzAgMjAgLTMgNDMgLTEyIDUxIC0xOXoiLz4KPHBhdGggZD0iTTQxNzAgODk4IGMtNjQgLTMzIC05MiAtODQgLTg4IC0xNjMgNCAtNzIgMzEgLTExNiA4OCAtMTQ1IDE3NCAtODkKMzM4IDEyMiAyMTIgMjcyIC01MSA2MCAtMTM1IDc1IC0yMTIgMzZ6IG0xNTEgLTg3IGMyMSAtMjIgMjkgLTM5IDI5IC02NiAwCi01MiAtNDQgLTk1IC05NyAtOTUgLTg3IDAgLTEyOCAxMDkgLTYyIDE2NCA0MyAzNiA5MSAzNSAxMzAgLTN6Ii8+CjwvZz4KPC9zdmc+Cg==';
      add_menu_page(VOYA_APP_NAME, VOYA_APP_NAME, 'manage_options', VOYA_PLUGIN_SLUG, '', $icon, 58.1);
      add_submenu_page(VOYA_PLUGIN_SLUG, 'Ajustes'                         , 'Ajustes'                    , 'manage_options', VOYA_PLUGIN_SLUG                               , [$this,'voyaSettings']);
      add_submenu_page(VOYA_PLUGIN_SLUG, 'Plantillas de etiquetas de envío', 'Plantillas de etiquetas'    , 'manage_options', VOYA_PLUGIN_SLUG.'-shipping-labels-list'       , [$this, 'showShippingLabelsPage']);
      add_submenu_page(VOYA_PLUGIN_SLUG, 'Plantillas de etiquetas de envío', 'Crear plantilla de etiqueta', 'manage_options', VOYA_PLUGIN_SLUG.'-shipping-label-template-add', [$this, 'formShippingLabelPage']);
    }

    //-- Páginas --
    //Ajustes
    function voyaSettings(){
      return wp_redirect(admin_url( 'admin.php?page=wc-settings&tab=shipping&section=voya_despachos'));
    }
    //  Plantillas de etiquetas de envío - Listar
    function showShippingLabelsPage(){
      if ( ! class_exists( 'Voya_Despachos_Shipping_Label_List_Table' ) ) {
        require_once VOYA_PLUGIN_PATH . '/includes/classes/tables/voya_despachos_shipping_label_list_table.php';
      }
      $list_table = new Voya_Despachos_Shipping_Label_List_Table();
      $list_table->display_page($_REQUEST);
    }

    //  Plantillas de etiquetas de envío - Formulario Crear/Editar/Borrar
    function formShippingLabelPage(){
      if ( ! class_exists( 'VoyaDespachosShippingLabelTemplateForm' ) ) {
        require_once VOYA_PLUGIN_PATH . '/includes/classes/forms/voya_despachos_shipping_label_template_form.php';
      }
      $formPage = new VoyaDespachosShippingLabelTemplateForm();
      $formPage->display_page($_GET);
    }

    //-- Procesamiento --
    //  Plantillas de etiquetas de envío - Crear/Editar/Borrar
    function formShippingLabelProcessor() {
      if ( ! class_exists( 'VoyaDespachosShippingLabelTemplateForm' ) ) {
        require_once VOYA_PLUGIN_PATH . '/includes/classes/forms/voya_despachos_shipping_label_template_form.php';
      }
      $formPageProcessor = new VoyaDespachosShippingLabelTemplateForm();
      $formPageProcessor->process_request($_POST);
    }

    //-- Registrar Post Types --
    function voyacl_register_post_types() {
      //Shipping Labels Templates (slt)
      register_post_type( VOYA_PLUGIN_SLUG.'-slt', array(
        'labels' => array(
          'name' => 'Plantillas de etiquetas de envío',
          'singular_name' => 'Plantilla de etiqueta de envío',
        ),
        'rewrite' => false,
        'query_var' => false,
        'public' => false,
        'capability_type' => 'page'
      ) );
    }
  }
}
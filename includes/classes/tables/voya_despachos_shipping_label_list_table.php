<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if (!class_exists('Voya_Despachos_Shipping_Label_List_Table')) {
	class Voya_Despachos_Shipping_Label_List_Table extends WP_List_Table {

		public function __construct() {
			parent::__construct( array(
				'singular' => 'post',
				'plural' => 'posts',
				'ajax' => false,
			) );
		}

		public function display_page($request){
			$this->prepare_items();
	      	?>
		        <div class="wrap" id="<?php echo VOYA_PLUGIN_SLUG; ?>-list-shipping-label-templates-table">
		          <h1 class="wp-heading-inline">
		            Plantillas de Etiquetas de Envío
		          </h1>
		          <a class="page-title-action" href="<?php menu_page_url( VOYA_PLUGIN_SLUG.'-shipping-label-template-add' ) ?>">Crear nueva</a>
		          <hr class="wp-header-end">

		          <form method="get" action="">
		            <input type="hidden" name="page" value="<?php echo esc_attr( $request['page'] ); ?>" />
		            <?php $this->display(); ?>
		          </form>
		        </div>
	      	<?php
		}

		public function prepare_items() {
			$per_page = 20; 

			$args = array(
				'posts_per_page' => $per_page,
				'orderby' => 'id',
				'order' => 'ASC',
				'offset' => ( $this->get_pagenum() - 1 ) * $per_page,
			);

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				if ( 'title' == $_REQUEST['orderby'] ) {
					$args['orderby'] = 'title';
				}
			}

			if ( ! empty( $_REQUEST['order'] ) ) {
				if ( 'asc' == strtolower( $_REQUEST['order'] ) ) {
					$args['order'] = 'ASC';
				} elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ) {
					$args['order'] = 'DESC';
				}
			}

			$defaults = array(
				'post_status' => 'any',
				'posts_per_page' => -1,
				'offset' => 0,
				'orderby' => 'ID',
				'order' => 'ASC',
			);

			$args = wp_parse_args( $args, $defaults );

			$args['post_type'] = VOYA_PLUGIN_SLUG.'-slt';

			$q = new WP_Query();
			$posts = $q->query( $args );
			$objs = array();

			foreach ( (array) $posts as $post ) {
				$sltPostData = [];
				$sltPostMeta = get_post_meta($post->ID);
				$sltPostData['remitente_nombre'] = $sltPostMeta['remitente_nombre'][0];
				$sltPostData['remitente_rut'] = $sltPostMeta['remitente_rut'][0];
				$sltPostData['remitente_telefono'] = $sltPostMeta['remitente_telefono'][0];
				$sltPostData['remitente_email'] = $sltPostMeta['remitente_email'][0];
				$sltPostData['remitente_region'] = $sltPostMeta['remitente_region'][0];
				$sltPostData['remitente_comuna'] = $sltPostMeta['remitente_comuna'][0];
				$sltPostData['remitente_direccion'] = $sltPostMeta['remitente_direccion'][0];
				$post->meta_data = $sltPostData;
				$objs[] = $post;
			}
			$columns = $this->get_columns();
	        $hidden = array();
	        $sortable = array();
	        $this->_column_headers = array($columns, $hidden, $sortable);


			$this->items = $objs;
			$total_items = wp_count_posts( VOYA_PLUGIN_SLUG.'-slt' );
			$total_items = isset($total_items->publish) ? $total_items->publish : 0;
			$total_pages = ceil( $total_items / $per_page );

			$this->set_pagination_args( array(
				'total_items' => $total_items ,
				'total_pages' => $total_pages,
				'per_page' => $per_page,
			) );
		}

		public function get_columns(){
	        $columns = array(
                'name'          => 'Nombre de la plantilla',
                'sender_name'   => 'Nombre Remitente',
                'sender_phone'   => 'Teléfono Remitente',
                'sender_email'   => 'Correo Remitente',
                'date'         => 'Fecha de creación de plantilla',
                
	        );
	        return $columns;
	    }

	    public function column_name( $item ) {
			$edit_link = admin_url('admin.php?page='.VOYA_PLUGIN_SLUG.'-shipping-label-template-add&post='.$item->ID);

			$output = sprintf(
				'<a class="row-title" href="%1$s">%2$s</a>',
				esc_url( $edit_link ),
				esc_html( $item->post_title )
			);

			$output = sprintf( '<strong>%s</strong>', $output );
			return $output;
		}

		function column_default($item, $column_name)
	    {
			switch ($column_name) {
				case 'id':
				  	return $item->ID;
				case 'name':
					return $item->post_title;
				case 'date':
					return get_date_from_gmt( $item->post_date_gmt, 'H:i d/m/Y' );
				case 'sender_name':
					return $item->meta_data['remitente_nombre'];
				case 'sender_phone':
					return $item->meta_data['remitente_telefono'];
				case 'sender_email':
					return $item->meta_data['remitente_email'];
			}
	    }
	}
}

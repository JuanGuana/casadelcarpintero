<?php
function enqueue_styles_child_theme() {

	$parent_style = 'storefront-style';
	$child_style  = 'storefront-child-style';

	wp_enqueue_style( $parent_style,
				get_template_directory_uri() . '/style.css' );

	wp_enqueue_style( $child_style,
				get_stylesheet_directory_uri() . '/style.css',
				array( $parent_style ),
				wp_get_theme()->get('Version')
				);
}
add_action( 'wp_enqueue_scripts', 'enqueue_styles_child_theme' );

function remover_carrito_compras_header(){
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_cart', 60 );
}
add_action( 'storefront_header', 'remover_carrito_compras_header', 40 );

function remover_productos_recientes_carrito_compras(){
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 20);
}
add_action( 'woocommerce_cart_collaterals', 'remover_productos_recientes_carrito_compras');

add_action( 'woocommerce_productos_recientes', 'woocommerce_cross_sell_display' );

//función para retornar el costo del envío
function etiqueta_costo_envio( $method ) {
	$label     = '';
	$has_cost  = 0 < $method->cost;
	$hide_cost = ! $has_cost && in_array( $method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true );

	if ( $has_cost && ! $hide_cost ) {
		if ( WC()->cart->display_prices_including_tax() ) {
			$label .= wc_price( $method->cost + $method->get_shipping_tax() );
			if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$label .= wc_price( $method->cost );
			if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
	}

	return apply_filters( 'woocommerce_cart_shipping_method_full_label', $label, $method );
}
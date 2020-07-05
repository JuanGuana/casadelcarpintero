<?php
function enqueue_styles_child_theme()
{

    $parent_style = 'storefront-style';
    $child_style = 'storefront-child-style';

    wp_enqueue_style(
        $parent_style,
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        $child_style,
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'enqueue_styles_child_theme');

function remover_carrito_compras_header()
{
    remove_action('storefront_header', 'storefront_product_search', 40);
    remove_action('storefront_header', 'storefront_header_cart', 60);
}
add_action('storefront_header', 'remover_carrito_compras_header', 40);

remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

add_action('woocommerce_productos_recientes', 'woocommerce_cross_sell_display');

//función para retornar el costo del envío
function etiqueta_costo_envio($method)
{
    $label = '';
    $has_cost = 0 < $method->cost;
    $hide_cost = !$has_cost && in_array($method->get_method_id(), array('free_shipping', 'local_pickup'), true);

    if ($has_cost && !$hide_cost) {
        if (WC()->cart->display_prices_including_tax()) {
            $label .= wc_price($method->cost + $method->get_shipping_tax());
            if ($method->get_shipping_tax() > 0 && !wc_prices_include_tax()) {
                $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            }
        } else {
            $label .= wc_price($method->cost);
            if ($method->get_shipping_tax() > 0 && wc_prices_include_tax()) {
                $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
        }
    }

    return apply_filters('woocommerce_cart_shipping_method_full_label', $label, $method);
}

function remover_botones_pago_formulario_carrito_compras()
{
    remove_action('woocommerce_proceed_to_checkout', array('display_paypal_button'), 20);
}
add_action('woocommerce_proceed_to_checkout', 'remover_botones_pago_formulario_carrito_compras');


/* Juank*/

// Agregamos el icono de oferta en la parte superior del item
add_action(  'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash',5);
add_filter( 'woocommerce_sale_flash', function( $texto ) {
 $imagen = '<img src="https://www.pngkey.com/png/full/931-9319435_oferta-naranja-ecolgica-.png" alt="Oferta" height="50" width="50" class="contenedor oferta">';
 return $imagen;
}, 10, 1 );


// Movemos el precio del item antes de el titulo del item
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_price',5);


// Agregamos el campo sku de cada item
add_action( 'woocommerce_before_shop_loop_item_title', 'mostrar_sku_contenido_producto', 5 );
function mostrar_sku_contenido_producto(){
   global $product;
    echo '<div class="contenedor sku">' . $product->get_sku() . '</div>';
}

// Mostrar solo el precio mas bajo de productos variables
add_filter( 'woocommerce_variable_sale_price_html', 'wc_custom_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_custom_variation_price_format', 10, 2 );

function wc_custom_variation_price_format( $price, $product ) {

  // Precio principal
  $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
  $price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

  // Precio de oferta
  $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
  sort( $prices );
  $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

  if ( $price !== $saleprice ) {
	//$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
	$price = '<ins>' . $price . $product->get_price_suffix() . '</ins>  <span class="msj oferta">ANTES </span> <del>' . $saleprice . $product->get_price_suffix() . '</del>';
  }
  return $price;
}

// Mostrar texto antes del precio
/* add_filter( 'woocommerce_get_price_html', 'custom_price_message' );
function custom_price_message( $price ) {
	global $post;
	$product_id = $post->ID;
	$textafter = ' Desde'; 
	return $price . '<span class="textafter">' . $textafter . '</span>';
} */




/* Poner texto antes y despues del precio */
/* add_filter( 'woocommerce_format_sale_price', 'filter_function_name', 10, 3 );
function filter_function_name( $price, $regular_price, $sale_price ) { 
 $regular_price = 'Antes: '. wc_price( $regular_price);
 $sale_price = 'Ahora: ' . wc_price( $sale_price);
 $price = $regular_price . ' - '. $sale_price;
 return $price; 
};
 */




/* function custom_price_message( $price ) {
global $post;
$product_id = $post->ID;
$textbefore = 'Desde'; //texto que vamos agregar
return '<span class="textbefore">' . $textbefore . '</span>' .$price; //class textbefore para el CSS
}
add_filter( 'woocommerce_get_price_html', 'custom_price_message' ); */
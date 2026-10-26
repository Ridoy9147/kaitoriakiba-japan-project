<?php
/**
 * Kaitori Akiba — SEO: Open Graph/Twitter meta + JSON-LD structured data.
 *
 * Adds LocalBusiness (sitewide), Product/Offer (single device), and FAQPage
 * (FAQ page) structured data, reusing the shared store/FAQ/pricing providers.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Open Graph + Twitter Card meta tags.
 */
function kaitoriakiba_meta_tags() {
    $title = wp_get_document_title();
    $desc  = get_bloginfo( 'description' );
    $url   = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
    $image = '';

    if ( is_singular() ) {
        $pid = get_queried_object_id();
        if ( has_excerpt( $pid ) ) {
            $desc = wp_strip_all_tags( get_the_excerpt( $pid ) );
        }
        if ( has_post_thumbnail( $pid ) ) {
            $image = get_the_post_thumbnail_url( $pid, 'large' );
        } else {
            $custom = get_post_meta( $pid, '_device_primary_image_url', true );
            if ( $custom ) { $image = $custom; }
        }
        $url = get_permalink( $pid );
    }

    echo "\n<!-- Kaitori Akiba SEO -->\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
    if ( $image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'kaitoriakiba_meta_tags', 5 );

/**
 * Emit a JSON-LD script block.
 *
 * @param array $data
 */
function kaitoriakiba_jsonld( $data ) {
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * Structured data appropriate to the current view.
 */
function kaitoriakiba_structured_data() {
    // 1. LocalBusiness — sitewide.
    $store = function_exists( 'kaitoriakiba_store_info' ) ? kaitoriakiba_store_info() : array();
    if ( ! empty( $store ) ) {
        $local = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Store',
            'name'     => get_bloginfo( 'name' ),
            'url'      => home_url( '/' ),
            'address'  => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => $store['address'],
                'addressLocality' => 'Chiyoda',
                'addressRegion'   => 'Tokyo',
                'addressCountry'  => 'JP',
            ),
            'openingHours' => 'Mo-Su 10:00-19:30',
        );
        if ( ! empty( $store['phone'] ) ) {
            $local['telephone'] = $store['phone'];
        }
        kaitoriakiba_jsonld( $local );
    }

    // 2. Product/Offer — single device.
    if ( is_singular( 'kaitori_device' ) ) {
        $pid   = get_queried_object_id();
        $price = (int) get_post_meta( $pid, '_max_buyback_price', true );
        $img   = get_post_meta( $pid, '_device_primary_image_url', true );
        if ( ! $img && has_post_thumbnail( $pid ) ) {
            $img = get_the_post_thumbnail_url( $pid, 'large' );
        }
        $brands = get_the_terms( $pid, 'brand' );
        $brand  = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : '';

        $product = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => get_the_title( $pid ),
            'url'      => get_permalink( $pid ),
        );
        if ( $img )   { $product['image'] = $img; }
        if ( $brand ) { $product['brand'] = array( '@type' => 'Brand', 'name' => $brand ); }
        $mpn = get_post_meta( $pid, '_model_number', true );
        if ( $mpn ) { $product['mpn'] = $mpn; }
        if ( $price > 0 ) {
            $product['offers'] = array(
                '@type'         => 'Offer',
                'price'         => $price,
                'priceCurrency' => 'JPY',
                'availability'  => 'https://schema.org/InStock',
                'url'           => get_permalink( $pid ),
            );
        }
        kaitoriakiba_jsonld( $product );
    }

    // 3. FAQPage — FAQ page.
    if ( is_page_template( 'page-faq.php' ) && function_exists( 'kaitoriakiba_get_faqs' ) ) {
        $faqs = kaitoriakiba_get_faqs();
        if ( $faqs ) {
            $entities = array();
            foreach ( $faqs as $faq ) {
                $entities[] = array(
                    '@type'          => 'Question',
                    'name'           => $faq['q'],
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags( $faq['a'] ),
                    ),
                );
            }
            kaitoriakiba_jsonld( array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $entities,
            ) );
        }
    }
}
add_action( 'wp_head', 'kaitoriakiba_structured_data', 20 );

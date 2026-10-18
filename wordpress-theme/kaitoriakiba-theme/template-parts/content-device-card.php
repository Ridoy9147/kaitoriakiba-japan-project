<?php
/**
 * Template part for displaying a kaitori device card
 *
 * @package KaitoriAkiba
 */

$dev_id    = get_the_ID();
$price     = get_post_meta( $dev_id, '_max_buyback_price', true ) ?: '168000';
$jan       = get_post_meta( $dev_id, '_jan_code', true ) ?: '4549995734140';
$brands    = get_the_terms( $dev_id, 'brand' );
$brand_str = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Apple';
$thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url( $dev_id, 'medium' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png';
?>

<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
    <div class="pro-border gutter-10 row pro_list" style="margin-bottom:20px; border:1px solid #e2e8f0; border-radius:12px; background:#fff; padding:15px; transition:all 0.2s;">
        <div class="col-xs-12 text-center" style="margin-bottom:12px;">
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" style="max-height:160px; max-width:100%; object-fit:contain;">
            </a>
        </div>
        <div class="col-xs-12">
            <div style="font-size:11px; font-weight:700; color:#ef4444; margin-bottom:4px;"><?php echo esc_html( $brand_str ); ?></div>
            <div style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:6px; min-height:40px;">
                <a href="<?php the_permalink(); ?>" style="color:inherit; text-decoration:none;"><?php the_title(); ?></a>
            </div>
            <div style="font-size:11px; color:#ec1600; margin-bottom:4px;">分割残債あり(△)も減額査定なし対応</div>
            <div style="font-size:12px; font-weight:700; color:#475569; margin-top:8px;">
                最高買取: <strong style="font-size:18px; color:#dc2626;">¥<?php echo number_format((int)$price); ?></strong>
            </div>
            <a href="<?php the_permalink(); ?>" class="btn btn-pro" style="display:block; text-align:center; width:100%; margin-top:12px; font-weight:700; border-radius:6px; padding:8px;">
                <?php _e( '査定・買取を申込む', 'kaitoriakiba' ); ?>
            </a>
        </div>
    </div>
</div>

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
    <div class="pro-border gutter-10 row pro_list" style="padding: 14px 12px 8px 12px;">
        <div data-isok="false" data-value="<?php echo esc_attr( $dev_id ); ?>" class="collect" title="<?php echo esc_attr( kaitori_t( 'Add to favorites', 'お気に入りに追加' ) ); ?>"></div>
        <div class="col-xs-12 text-center pro-img-box" style="padding: 6px 0; margin-bottom: 8px;">
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>">
            </a>
        </div>
        <div class="col-xs-12 pro-info-box" style="margin-bottom: 0; padding-bottom: 0;">
            <ul style="list-style:none; padding:0; margin:0;">
                <div class="sub-pro" style="margin-bottom: 4px;">
                    <li class="sub-pro-career">
                        <span style="font-size:11px; font-weight:700; color:#ef4444;"><?php echo esc_html( $brand_str ); ?></span>
                    </li>
                    <li class="sub-pro-name sub-pro-device-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                    <li class="sub-pro-notice-item" style="margin: 2px 0 1px 0; line-height: 1.2;">
                        <a style="color:#ec1600; font-size: 11px; font-weight: 600; display: block; height: auto; max-height: 18px; line-height: 1.2; text-decoration: none;" title="分割残債あり(△)も減額なし対応" href="<?php the_permalink(); ?>">分割残債あり(△)も減額なし対応</a>
                    </li>
                    <li class="sub-pro-jan-item" style="font-size:11px; color:#64748b; margin: 2px 0 0 0; line-height: 1.2; padding: 0;">JAN：<?php echo esc_html( $jan ); ?></li>
                </div>
                <li class="sub-pro-jia" style="margin: auto 0 6px 0; padding-top: 6px;">
                    <?php kaitori_te( 'Max buyback:', '最高買取:' ); ?> <span>¥<?php echo number_format((int)$price); ?></span>
                </li>
                <li style="margin: 0; padding: 0;">
                    <a href="<?php the_permalink(); ?>" class="btn btn-pro" style="margin: 0; padding: 8px 12px;">
                        <?php kaitori_te( 'Get a quote / Apply', '査定・買取を申込む' ); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

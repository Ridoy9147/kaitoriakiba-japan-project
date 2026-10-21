<?php
/**
 * Kaitori Akiba Front Page Template
 * Supports Elementor editing seamlessly while providing full native dynamic fallback.
 * 
 * @package KaitoriAkiba
 */

get_header();

// Check if page is built with Elementor
$is_elementor = kaitoriakiba_is_elementor_page();

if ( $is_elementor ) :
    ?>
    <main id="primary" class="site-main elementor-content-wrap">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </main>
    <?php
else :
    // Native Dynamic Homepage Layout
    ?>
    <div class="container main-content-container" style="margin-top: 24px;">

        <!-- 1. Search Box (Dual Search) -->
        <div class="search" style="margin-bottom: 24px;">
            <div class="row">
                <!-- Col 1: Keyword Search -->
                <div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
                    <div class="search_title">
                        <div class="search_title_h2"><?php _e( 'Search by device model name or model number!', 'kaitoriakiba' ); ?></div>
                        <div class="search_title_h3"><?php _e( 'Please enter device name, model number, JAN code, etc.', 'kaitoriakiba' ); ?></div>					
                    </div>
                    <div class="search_box">
                        <form method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                            <div class="form-group mbtm0 clearfix">
                                <div class="keyword_search">
                                    <select class="form-control fw_cat" name="cat">
                                        <option value=""><?php _e( 'All Categories', 'kaitoriakiba' ); ?></option>
                                        <?php
                                        $cats = get_terms( array( 'taxonomy' => 'device_category', 'hide_empty' => false ) );
                                        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                            foreach ( $cats as $c ) {
                                                echo '<option value="' . esc_attr( $c->slug ) . '">' . esc_html( $c->name ) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="text" class="form-control keyword_search_text q" name="q" placeholder="<?php esc_attr_e( 'Keyword search (2+ characters)', 'kaitoriakiba' ); ?>" required="required" autocomplete="off">
                                    <button class="btn search_word" type="submit"><?php _e( 'Search', 'kaitoriakiba' ); ?> <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                        <div class="title" style="margin-top: 14px; font-size: 13px; font-weight: bold; color: #475569;">
                            <?php _e( 'Popular Search Keywords!', 'kaitoriakiba' ); ?>
                            <ul class="hotword" style="margin-top: 6px;">
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+16' ) ); ?>">iPhone 16</a></li>
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+16+Pro' ) ); ?>">iPhone 16 Pro</a></li>
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+15' ) ); ?>">iPhone 15</a></li>
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=Pixel' ) ); ?>">Pixel</a></li>
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPad' ) ); ?>">iPad</a></li>
                                <li><a href="<?php echo esc_url( home_url( '/devices/?q=Switch' ) ); ?>">Nintendo switch</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Step-by-Step Search -->
                <div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
                    <div class="search_title">
                        <div class="search_title_h2"><?php _e( 'Step-by-Step Category Search', 'kaitoriakiba' ); ?></div>
                        <div class="search_title_h3"><?php _e( 'Select one or more categories (Multiple selection supported)', 'kaitoriakiba' ); ?></div>					
                    </div>
                    <div class="search_box">
                        <form method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                            <div class="form-group">
                                <div class="step_search">
                                    <div class="step_search_filters">
                                        <select class="form-control s_cat" name="cat">
                                            <option value=""><?php _e( 'Select Category', 'kaitoriakiba' ); ?></option>
                                            <?php
                                            if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                                foreach ( $cats as $c ) {
                                                    echo '<option value="' . esc_attr( $c->slug ) . '">' . esc_html( $c->name ) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <select class="form-control s_career" name="s_career">
                                            <option value=""><?php _e( 'By Carrier', 'kaitoriakiba' ); ?></option>
                                            <?php
                                            $carriers = get_terms( array( 'taxonomy' => 'carrier', 'hide_empty' => false ) );
                                            if ( ! empty( $carriers ) && ! is_wp_error( $carriers ) ) {
                                                foreach ( $carriers as $car ) {
                                                    echo '<option value="' . esc_attr( $car->slug ) . '">' . esc_html( $car->name ) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <select class="form-control s_brand" name="brand">
                                            <option value=""><?php _e( 'By Brand', 'kaitoriakiba' ); ?></option>
                                            <?php
                                            $brands = get_terms( array( 'taxonomy' => 'brand', 'hide_empty' => false ) );
                                            if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
                                                foreach ( $brands as $b ) {
                                                    echo '<option value="' . esc_attr( $b->slug ) . '">' . esc_html( $b->name ) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <select class="form-control s_series" name="series">
                                            <option value=""><?php _e( 'By Series', 'kaitoriakiba' ); ?></option>
                                            <?php
                                            $series = get_terms( array( 'taxonomy' => 'series', 'hide_empty' => false ) );
                                            if ( ! empty( $series ) && ! is_wp_error( $series ) ) {
                                                foreach ( $series as $s ) {
                                                    echo '<option value="' . esc_attr( $s->slug ) . '">' . esc_html( $s->name ) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button class="btn step_search_btn" type="submit"><?php _e( 'Search', 'kaitoriakiba' ); ?> <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Announcement Alert Banner -->
        <div style="background:#fef2f2; border:1.5px solid #fca5a5; border-radius:12px; padding:18px 24px; margin-bottom:28px; display:flex; align-items:flex-start; gap:16px;">
            <div style="font-size:24px; color:#ef4444; line-height:1;"><i class="fa fa-exclamation-triangle"></i></div>
            <div>
                <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin:0 0 6px 0;"><?php _e( '【重要なお知らせ】iPhone 16 / 15 シリーズ買取強化キャンペーン実施中！', 'kaitoriakiba' ); ?></h4>
                <p style="font-size:13px; color:#475569; margin:0; line-height:1.5;"><?php _e( '秋葉原店持込または送料無料の全国郵送買取にてiPhone 16 Pro / 15 Proの買取価格を強化中。店頭なら最短5分で即現金お渡し可能です。', 'kaitoriakiba' ); ?></p>
            </div>
        </div>

        <!-- 3. Process Flow 3-Cards -->
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 15px;">
                <div style="background:#ffffff; border:1px solid #fed7aa; border-radius:14px; padding:22px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-building"></i></div>
                        <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;">店頭買取 (In-Store)</h3>
                    </div>
                    <p style="font-size:13px; color:#475569; margin:0; line-height:1.5;">秋葉原店へ直接お持込。最短5分で即時現金手渡し！事前予約不要。</p>
                    <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>" class="btn btn-default" style="margin-top:14px; width:100%; font-weight:700;">店頭買取を申込む &rarr;</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 15px;">
                <div style="background:#ffffff; border:1px solid #bbf7d0; border-radius:14px; padding:22px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-truck"></i></div>
                        <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;">郵送買取 (Mail-In)</h3>
                    </div>
                    <p style="font-size:13px; color:#475569; margin:0; line-height:1.5;">送料無料の宅配キット完備。到着当日に即日査定＆指定口座へ即日振込！</p>
                    <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>" class="btn btn-success" style="margin-top:14px; width:100%; font-weight:700; background:#16a34a; border-color:#16a34a;">郵送買取を申込む &rarr;</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 15px;">
                <div style="background:#ffffff; border:1px solid #bfdbfe; border-radius:14px; padding:22px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-briefcase"></i></div>
                        <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;">法人買取 (Corporate)</h3>
                    </div>
                    <p style="font-size:13px; color:#475569; margin:0; line-height:1.5;">社用端末の大量買取・データ消去証明書の発行や請求書払い対応。</p>
                    <a href="<?php echo esc_url( home_url( '/sell-apply/?type=corporate' ) ); ?>" class="btn btn-primary" style="margin-top:14px; width:100%; font-weight:700; background:#2563eb; border-color:#2563eb;">法人見積・相談 &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 4. Main Two-Column Layout (Sidebar + Products) -->
        <div class="row">
            <!-- Left Sidebar (Browse by Carrier, Brand, Series) -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                <!-- Carrier Box -->
                <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
                    <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
                        <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i>
                        <span>Browse by Carrier</span>
                    </div>
                    <ul style="list-style:none; padding:8px; margin:0;">
                        <?php
                        $sample_careers = array(
                            'docomo' => 'upload/career/docomo.jpg',
                            'au' => 'upload/career/au.jpg',
                            'softbank' => 'upload/career/softbank.jpg',
                            'simfree' => 'upload/career/simfree.jpg',
                            'Y!mobile' => 'upload/career/Y!mobile.jpg',
                            'uqmobile' => 'upload/career/uqmobile.jpg',
                        );
                        foreach ( $sample_careers as $slug => $path ) :
                        ?>
                            <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                                <a href="<?php echo esc_url( home_url( '/devices/?s_career=' . $slug ) ); ?>" style="display:block;">
                                    <img src="<?php echo get_template_directory_uri() . '/' . $path; ?>" alt="<?php echo esc_attr( $slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Brand Box -->
                <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
                    <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
                        <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i>
                        <span>Browse by Brand</span>
                    </div>
                    <ul style="list-style:none; padding:8px; margin:0;">
                        <?php
                        $sample_brands = array(
                            'apple' => 'upload/brand/apple.jpg',
                            'google' => 'upload/brand/google.jpg',
                            'sony' => 'upload/brand/sony.jpg',
                            'samsung' => 'upload/brand/samsung.jpg',
                            'oppo' => 'upload/brand/OPPO.jpg',
                        );
                        foreach ( $sample_brands as $slug => $path ) :
                        ?>
                            <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                                <a href="<?php echo esc_url( home_url( '/devices/?brand=' . $slug ) ); ?>" style="display:block;">
                                    <img src="<?php echo get_template_directory_uri() . '/' . $path; ?>" alt="<?php echo esc_attr( $slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Series Box -->
                <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
                    <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
                        <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i>
                        <span>Browse by Series</span>
                    </div>
                    <ul style="list-style:none; padding:8px; margin:0;">
                        <?php
                        $sample_series = array(
                            'iphone' => 'upload/series/iphone.jpg',
                            'xperia' => 'upload/series/xperia.jpg',
                            'galaxy' => 'upload/series/galaxy.jpg',
                            'aquos'  => 'upload/series/aquos.jpg',
                        );
                        foreach ( $sample_series as $slug => $path ) :
                        ?>
                            <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                                <a href="<?php echo esc_url( home_url( '/devices/?series=' . $slug ) ); ?>" style="display:block;">
                                    <img src="<?php echo get_template_directory_uri() . '/' . $path; ?>" alt="<?php echo esc_attr( $slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Right Main Area: Products Grid -->
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <div class="section-title-wrap" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #e2e8f0; padding-bottom:12px;">
                    <h2 style="font-size:20px; font-weight:900; color:#0f172a; margin:0;">
                        <i class="fa fa-mobile" style="color:#ef4444;"></i> <?php _e( 'iPhone・スマホ高価買取対象機種', 'kaitoriakiba' ); ?>
                    </h2>
                    <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>" style="font-size:13px; font-weight:700; color:#ef4444; text-decoration:none;">
                        <?php _e( 'すべての買取機種を見る', 'kaitoriakiba' ); ?> &rarr;
                    </a>
                </div>

                <div class="row">
                    <?php
                    $devices_query = new WP_Query( array(
                        'post_type'      => 'kaitori_device',
                        'posts_per_page' => 12,
                        'post_status'    => 'publish',
                    ) );

                    if ( $devices_query->have_posts() ) :
                        while ( $devices_query->have_posts() ) : $devices_query->the_post();
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
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<div class="col-xs-12"><div class="alert alert-info">管理画面の「買取機種」より機種を登録してください。</div></div>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
endif;

get_footer();

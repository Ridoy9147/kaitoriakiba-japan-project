<?php
/**
 * Kaitori Akiba Front Page Template
 * 
 * 100% Authentic Original Kaitori Akiba Design from index.html.
 * Supports Elementor Page Builder seamlessly when customized,
 * and provides full native dynamic fallback with all original sections:
 * - Search & Category Icons
 * - 3 Process Flow Big Buttons (guid)
 * - Quick Guides Grid (home_liuxiang)
 * - Expandable Urgent Announcements (qingbao)
 * - Hero Nav Banners & Swiper Slider (mainbanner)
 * - Left Sidebar (Carrier, Brand, Series with 100% full-width images)
 * - What's News tabbed section with trumpet icon
 * - Sub-box Buyback Product Showcase Grid
 * - Key Selling Points & Checkpoints
 * - Step-by-step Buyback Process Flow
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

get_header();

// Check if page should render Elementor content or fallback
global $post;
$render_elementor = false;

// 1. If currently inside Elementor Editor or Preview, ALWAYS call the_content() so Elementor loads smoothly
if ( function_exists( 'kaitoriakiba_is_elementor_active' ) && kaitoriakiba_is_elementor_active() ) {
    $render_elementor = true;
} elseif ( function_exists( 'kaitoriakiba_is_elementor_page' ) && kaitoriakiba_is_elementor_page() ) {
    // 2. If page was edited and saved with Elementor builder, render the customized Elementor design
    $render_elementor = true;
}

// 3. If page template is explicitly set to Elementor Full Width or Canvas
$page_template = get_page_template_slug();
if ( in_array( $page_template, array( 'template-elementor-fullwidth.php', 'template-elementor-canvas.php' ), true ) ) {
    $render_elementor = true;
}

if ( $render_elementor ) :
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
    // =========================================================================
    // 100% COMPLETE ORIGINAL KAITORI AKIBA HOMEPAGE DESIGN
    // =========================================================================
    $theme_uri = get_template_directory_uri();
    ?>

    <!-- 1. Search Box (Dual Search) & Quick Category Bar -->
    <div class="container search mtop10" id="top-search-area">
        <div class="row search-box-row">
            <!-- Col 1: Keyword Search -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 search-col-keyword">
                <div class="search_title">
                    <div class="search_title_h2"><?php kaitori_te( 'Search by device model name or model number!', '機種名・型番で検索！' ); ?></div>
                    <div class="search_title_h3"><?php kaitori_te( 'Please enter device name, model number, JAN code, etc.', '機種名・型番・JANコード等をご入力ください。' ); ?></div>				
                </div>
                <div class="search_box">
                    <form class="m_fullsearch" method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                        <div class="form-group mbtm0 clearfix">
                            <div class="keyword_search">
                                <select class="form-control fw_cat" name="cat">
                                    <option value=""><?php kaitori_te( 'All Categories', 'すべてのカテゴリ' ); ?></option>
                                    <?php
                                    $cats = get_terms( array( 'taxonomy' => 'device_category', 'hide_empty' => false ) );
                                    if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                        foreach ( $cats as $c ) {
                                            echo '<option value="' . esc_attr( $c->slug ) . '">' . esc_html( $c->name ) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="iphone">iPhone</option>';
                                        echo '<option value="smartphones">Smartphones</option>';
                                        echo '<option value="tablets">Tablets</option>';
                                    }
                                    ?>
                                </select>
                                <input type="text" class="form-control keyword_search_text q" name="q" placeholder="<?php echo esc_attr( kaitori_t( 'Keyword search (2+ characters)', 'キーワード検索（2文字以上）' ) ); ?>" required="required" autocomplete="off">
                                <button class="btn search_word" type="submit"><?php kaitori_te( 'Search', '検索' ); ?> <i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <div class="title"><?php kaitori_te( 'Popular Search Keywords!', '人気の検索キーワード！' ); ?>
                        <ul class="hotword">
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=iphone' ) ); ?>">iphone</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=Nintendo+switch' ) ); ?>">Nintendo switch</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=Pixel' ) ); ?>">Pixel</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+16' ) ); ?>">iPhone 16</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+16+Pro' ) ); ?>">iPhone 16 Pro</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=OPPO' ) ); ?>">OPPO</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=airpods' ) ); ?>">airpods</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?q=iPhone+15' ) ); ?>">iPhone 15</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Col 2: Step-by-Step Search -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 search-col-step">
                <div class="search_title">
                    <div class="search_title_h2"><?php kaitori_te( 'Step-by-Step Category Search', 'カテゴリから絞り込み検索' ); ?></div>
                    <div class="search_title_h3"><?php kaitori_te( 'Select one or more categories (Multiple selection supported)', '条件を選択してください（複数選択可）' ); ?></div>				
                </div>
                <div class="search_box">
                    <form class="snssearch" method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                        <div class="form-group">
                            <div class="step_search">
                                <div class="step_search_filters">
                                    <select class="form-control s_cat" name="cat">
                                        <option value=""><?php kaitori_te( 'Select Category', 'カテゴリを選択' ); ?></option>
                                        <?php
                                        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                            foreach ( $cats as $c ) {
                                                echo '<option value="' . esc_attr( $c->slug ) . '">' . esc_html( $c->name ) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="iphone">iPhone</option>';
                                            echo '<option value="smartphones">Smartphones</option>';
                                        }
                                        ?>
                                    </select>
                                    <select class="form-control s_career" name="s_career">
                                        <option value=""><?php kaitori_te( 'By Carrier', 'キャリアで選択' ); ?></option>
                                        <?php
                                        $carriers = get_terms( array( 'taxonomy' => 'carrier', 'hide_empty' => false ) );
                                        if ( ! empty( $carriers ) && ! is_wp_error( $carriers ) ) {
                                            foreach ( $carriers as $car ) {
                                                echo '<option value="' . esc_attr( $car->slug ) . '">' . esc_html( $car->name ) . '</option>';
                                            }
                                        } else {
                                            $sample_c = array( 'docomo', 'au', 'SoftBank', 'Y!mobile', 'UQ mobile', 'SIM FREE', 'RakutenMobile' );
                                            foreach ( $sample_c as $sc ) echo '<option value="' . esc_attr($sc) . '">' . esc_html($sc) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <select class="form-control s_brand" name="brand">
                                        <option value=""><?php kaitori_te( 'By Brand', 'ブランドで選択' ); ?></option>
                                        <?php
                                        $brands = get_terms( array( 'taxonomy' => 'brand', 'hide_empty' => false ) );
                                        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
                                            foreach ( $brands as $b ) {
                                                echo '<option value="' . esc_attr( $b->slug ) . '">' . esc_html( $b->name ) . '</option>';
                                            }
                                        } else {
                                            $sample_b = array( 'Apple', 'Xiaomi', 'OPPO', 'ASUS', 'FUJITSU', 'SAMSUNG', 'Google', 'SONY' );
                                            foreach ( $sample_b as $sb ) echo '<option value="' . esc_attr($sb) . '">' . esc_html($sb) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <select class="form-control s_series" name="series">
                                        <option value=""><?php kaitori_te( 'By Series', 'シリーズで選択' ); ?></option>
                                        <?php
                                        $series = get_terms( array( 'taxonomy' => 'series', 'hide_empty' => false ) );
                                        if ( ! empty( $series ) && ! is_wp_error( $series ) ) {
                                            foreach ( $series as $s ) {
                                                echo '<option value="' . esc_attr( $s->slug ) . '">' . esc_html( $s->name ) . '</option>';
                                            }
                                        } else {
                                            $sample_s = array( 'iPhone', 'Xperia', 'Galaxy', 'arrows', 'AQUOS', 'Pixel' );
                                            foreach ( $sample_s as $ss ) echo '<option value="' . esc_attr($ss) . '">' . esc_html($ss) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button class="btn step_search_btn" type="submit"><?php kaitori_te( 'Search', '検索' ); ?> <i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Icons Row (href_list) -->
    <div class="bg-white">
        <div class="href_list">
            <div class="container">
                <div class="gutter-10 clearflex">
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>" title="iPhone 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_iphone.svg" alt="iPhone" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=tablets' ) ); ?>" title="iPad / タブレット 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_ipad.svg" alt="iPad / Tablets" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?q=Pixel' ) ); ?>" title="Google Pixel 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_pixel.svg" alt="Google Pixel" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?q=Galaxy' ) ); ?>" title="Galaxy 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_galaxy.svg" alt="Galaxy" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?q=Xperia' ) ); ?>" title="Xperia 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_xperia.svg" alt="Xperia" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?q=AQUOS' ) ); ?>" title="AQUOS / arrows 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_aquos.svg" alt="AQUOS / arrows" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?q=Xiaomi' ) ); ?>" title="Xiaomi / OPPO 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_xiaomi.svg" alt="Xiaomi / OPPO" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?s_career=simfree' ) ); ?>" title="SIMフリー スマホ 買取">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/phones/banner_simfree.svg" alt="SIM Free Smartphones" class="img-responsive" />
                        </a>
                    </div>
                </div>
            </div>
        </div>				
    </div>

    <!-- 2. Three Main Process Flow Buttons (guid) -->
    <div class="guid">
        <div class="container">
            <div class="row home_index">
                <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0 col-preg"></div>
                <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 center col-preg-main">
                    <div class="col-lg-1 col-xs-0"></div>
                    <div class="col-lg-10 col-xs-12 col-preg-main">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 type_1">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>" title="In-Store Buyback Flow">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s1.png" class="img-responsive hidden-xs" alt="In-Store Buyback Flow" />
                                <span class="btn btn-orange"><?php kaitori_te( 'In-Store Buyback', '店頭買取' ); ?></span>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 type_2">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>" title="Mail-in Buyback Flow">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s2.png" class="img-responsive hidden-xs" alt="Mail-in Buyback Flow" />
                                <span class="btn btn-green"><?php kaitori_te( 'Mail-in Buyback', '郵送買取' ); ?></span>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 type_3">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?type=corporate' ) ); ?>" title="Corporate Buyback Flow">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s3.png" class="img-responsive hidden-xs" alt="Corporate Buyback Flow" />
                                <span class="btn btn-blue"><?php kaitori_te( 'Corporate Buyback', '法人買取' ); ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-1 col-xs-0"></div>
                </div>
                <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0 col-preg"></div>
            </div>
        </div>
    </div>

    <!-- 3. Maintop Section (Quick Links, Urgent Announcements, Banner Slider) -->
    <div class="maintop">
        <!-- Quick Help Bar (home_liuxiang) -->
        <div class="home_liuxiang">
            <div class="container">
                <div class="row home_lx_box">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php kaitori_te( 'How to Register', '会員登録の方法' ); ?></a></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><?php kaitori_te( 'How to Apply for Buyback', '買取申込の方法' ); ?></a></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><?php kaitori_te( 'Identity Verification (eKYC)', '本人確認 (eKYC)' ); ?></a></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php kaitori_te( 'Price Guarantee Policy', '価格保証について' ); ?></a></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/mypage/' ) ); ?>"><?php kaitori_te( 'Member Dashboard', 'マイページ' ); ?></a></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                        <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php kaitori_te( 'Frequently Asked Questions (FAQ)', 'よくある質問 (FAQ)' ); ?></a></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expandable Urgent Announcements (qingbao) -->
        <div class="container pding0">
            <div style="background:#feecec; padding:20px; margin-bottom:10px; border-radius:8px;">
                <div style="border-bottom:1px solid #fca5a5; padding-bottom:10px; font-weight:800; color:#b91c1c; font-size:15px;">
                    <i class="fa fa-bullhorn" style="margin-right:6px;"></i> <?php kaitori_te( '[Important] Announcements from Kaitori Akiba', '【重要】買取アキバからのお知らせ (Urgent Announcements)' ); ?>
                </div>
                <div style="padding-top:15px;" class="qingbao" id="qb">
                    <p><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【重要】古物営業法に基づく本人確認書類（eKYC）および購入証明書の提出について</u></strong></span></a></p>
                    <p><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【郵送買取】送料無料キャンペーン実施中！到着当日にスピード本査定＆最短即日入金</u></strong></span></a></p>
                    <p><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【店舗情報】秋葉原店は年中無休で営業中 (営業時間: 10:00 〜 19:30)</u></strong></span></a></p>
                    <p><span style="background-color:#FFE500; color:#E53333; font-size:14px; padding:3px 6px;"><strong>★ 重要: ご来店・ご郵送の前にWEBページより事前査定申込をお願いいたします (事前予約制) ★</strong></span></p>
                    <p style="margin-top:10px; font-size:13px; color:#334155;">■ 分割残債あり（利用制限△）の端末も減額査定なしでお買取り可能です。</p>
                    <p style="font-size:13px; color:#334155;">■ 法人様の大量端末お買取り・データ消去証明書の発行にも対応しております。</p>
                </div>
                <div class="dropdownbutton" style="text-align:center; margin-top:10px;">
                    <button id="moreless" class="dropdown" onclick="qbopen();" style="cursor:pointer;"><?php kaitori_te( 'Show More', '詳細を見る' ); ?></button>
                </div>
            </div>
        </div>

        <script>
            function qbopen() {
                var qb = document.getElementById('qb');
                var btn = document.getElementById('moreless');
                if (qb.classList.contains('qingbao')) {
                    qb.classList.remove('qingbao');
                    btn.innerText = 'Show Less / 閉じる';
                } else {
                    qb.classList.add('qingbao');
                    btn.innerText = 'Show More / 詳細を見る';
                }
            }
        </script>

        <!-- Hero Nav Banners -->
        <div class="container">
            <div class="row">
                <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_img_1.jpg" class="img-responsive mbtm15" alt="iPhone Buyback Price List"></a>
                    <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_btn_1.png" class="img-responsive" alt="iPhone Buyback Chart"></a>
                </div>
                <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_img_2.jpg" class="img-responsive mbtm15" alt="Smartphone Buyback List by Brand"></a>
                    <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_btn_2.png" class="img-responsive" alt="Smartphone Buyback Rates"></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Promo Banner Posters -->
        <div class="container pding0">
            <ul style="list-style:none; padding:0; margin:0 0 10px 0;">
                <li style="margin-bottom:8px;">
                    <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20251229/63a58246c446bf1710aa78f25c4b2619.png" alt="Free Shipping Mail-in Buyback Terms" class="img-responsive col-center poster" style="width:100%; max-width:100%; border-radius:6px;"/>
                    </a>
                </li>
                <li style="margin-bottom:8px;">
                    <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20210621/member_rank.png" alt="Kaitori Akiba - Member Benefits & Rewards" class="img-responsive col-center poster" style="width:100%; max-width:100%; border-radius:6px;"/>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Banner Swiper Slider -->
        <div class="container pding0">
            <div class="mainbanner visible-lg visible-md" style="margin-bottom:20px;">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( home_url( '/devices/?cat=tablets' ) ); ?>">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180508/mainbanner-1.jpg" alt="iPad High Buyback Rate Chart" class="img-responsive" style="width:100%; border-radius:8px;">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( home_url( '/devices/?cat=gaming' ) ); ?>">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180508/mainbanner-3.jpg" alt="Gaming Consoles Enhanced Buyback Rates" class="img-responsive" style="width:100%; border-radius:8px;">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="https://lin.ee/5wvtHzl" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20210621/member_rank02.png" alt="LINE Official Support" class="img-responsive" style="width:100%; border-radius:8px;">
                            </a>
                        </div>
                    </div>
                    <div class="swiper-pagination-md"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Homepage Main Two-Column Container (home-contenter) -->
    <div class="home-contenter">
        <div class="container">
            <div class="row">
                <!-- ================= LEFT SIDEBAR ================= -->
                <div id="js_agn_lft" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <?php get_sidebar(); ?>
                </div>

                <!-- ================= RIGHT CONTENT AREA ================= -->
                <div id="js_agn_rgt" class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    
                    <!-- 4.1 What's News Tab Box -->
                    <div class="topicInner home">
                        <div class="topicHeader">
                            <p class="topicTtl">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/subpage/trumpet_icon.png" width="32px" style="margin-right: 16px" alt="News Icon" />
                                <?php _e( "What's News (新着情報・お知らせ)", 'kaitoriakiba' ); ?>
                            </p>
                            <ul class="topicCategory">
                                <li class="tc01 cur"><span class="all" data-id="all"><?php kaitori_te( 'All', 'すべて' ); ?></span></li>
                                <li class="tc02" data-id="1"><span class="blogTopic"><?php kaitori_te( 'Topics', 'トピックス' ); ?></span></li>
                                <li class="tc02" data-id="2"><span class="blogTopic"><?php kaitori_te( 'Information', 'お知らせ' ); ?></span></li>
                            </ul>
                        </div>
                        <div class="topicList">
                            <div class="topicWrap">
                                <ul class="clearflex show">
                                    <li class="col-xs-12 col-md-6">
                                        <div class="mThumbnail">
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>">
                                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260916/message_yoroshiku_casual.png" class="img-responsive" alt="News" />
                                            </a>
                                        </div>
                                        <div class="layout_cell">
                                            <p class="date">2026.09.16</p>
                                            <p class="title"><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>">【秋葉原店】通常営業時間のご案内 (10:00〜19:30)</a></p>
                                            <p class="msg"><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>">いつも買取アキバをご利用いただきありがとうございます。秋葉原店は年中無休で店頭買取を実施しております。</a></p>
                                        </div>
                                    </li>
                                    <li class="col-xs-12 col-md-6">
                                        <div class="mThumbnail">
                                            <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">
                                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/goods/nophoto.png" class="img-responsive" alt="eKYC" />
                                            </a>
                                        </div>
                                        <div class="layout_cell">
                                            <p class="date">2026.07.07</p>
                                            <p class="title"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">【本人確認】オンラインeKYCスピード認証システム導入</a></p>
                                            <p class="msg"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">古物営業法に基づく本人確認手続きがスマホひとつで完了。運転免許証・マイナンバーカードのアップロードに対応。</a></p>
                                        </div>
                                    </li>
                                    <li class="col-xs-12 col-md-6">
                                        <div class="mThumbnail">
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">
                                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260609/5f270b7f-2361-4448-9981-f16c42ea9875.png" class="img-responsive" alt="Mail In" />
                                            </a>
                                        </div>
                                        <div class="layout_cell">
                                            <p class="date">2026.06.09</p>
                                            <p class="title"><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">【郵送買取】安全な梱包と無料発送キットのご案内</a></p>
                                            <p class="msg"><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">輸送中の箱潰れを防ぐため、端末を気泡緩衝材（プチプチ）で丁寧に保護して発送してください。</a></p>
                                        </div>
                                    </li>
                                    <li class="col-xs-12 col-md-6">
                                        <div class="mThumbnail">
                                            <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">
                                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260317/123456.png" class="img-responsive" alt="Price Guarantee" />
                                            </a>
                                        </div>
                                        <div class="layout_cell">
                                            <p class="date">2026.03.17</p>
                                            <p class="title"><a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">【相場更新】iPhone 16 / 15 シリーズ高価買取価格改定のお知らせ</a></p>
                                            <p class="msg"><a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">最新相場に基づきiPhone各シリーズの最高買取上限価格を更新いたしました。分割中(△)も同額買取対応！</a></p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="topicFooter">
                            <a class="btn-ys more" href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php kaitori_te( 'View All', '一覧を見る' ); ?></a>
                        </div>
                    </div>

                    <!-- 4.2 Member Favorites Notice Banner -->
                    <div class="mainpage-box xs-pad0">
                        <div class="sub-box" id="layout_x">
                            <div class="text-center head_img">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/sub_box_header05.png" alt="Favorites Header" class="img-responsive" style="margin:0 auto;" />
                            </div>
                            <p style="text-indent: 10%; margin-top:12px;"><?php kaitori_te( 'Click the "★" button on the product list to add it to your favorites.', '商品一覧の「★」ボタンをクリックすると、お気に入りリストへ登録できます。' ); ?></p>
                            <p style="text-indent: 10%;"><?php kaitori_te( 'Smoothly check the price changes of devices you care about anytime!', '気になる機種の買取価格の変動をいつでもスムーズにチェック可能！' ); ?></p>
                            <p style="text-align: center; color:red; font-weight:bold;"><?php kaitori_te( '* Members-only feature. Available after registering / logging in.', '※ 会員限定機能です。会員登録・ログイン後にご利用いただけます。' ); ?></p>
                        </div>
                    </div>

                    <!-- 4.3 Featured Buyback Devices Showcase Grid -->
                    <div class="mainpage-box xs-pad0">
                        <div class="sub-box" id="layout_y">
                            <div class="kaitori-section-header" style="text-align: center; margin-bottom: 25px; padding: 15px 0;">
                                <div class="section-title-wrap" style="display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <div class="section-badge" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; font-size: 12px; font-weight: 800; padding: 4px 14px; border-radius: 20px; letter-spacing: 0.5px; margin: 0; line-height: 1.4; vertical-align: middle; box-sizing: border-box;">
                                        <span class="badge-dot" style="width: 7px; height: 7px; background-color: #ef4444; border-radius: 50%; display: inline-block;"></span>
                                        <span class="badge-text" style="display: inline-block; line-height: 1;"><?php kaitori_te( 'HIGH PRICE GUARANTEED', '高価買取強化中' ); ?></span>
                                    </div>
                                    <h2 class="section-title" style="display: inline-flex; align-items: center; font-size: 24px; font-weight: 900; color: #0f172a; margin: 0; padding: 0; line-height: 1.2; letter-spacing: -0.5px; vertical-align: middle;">
                                        <span class="title-highlight" style="background: linear-gradient(135deg, #0f172a 0%, #dc2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; line-height: 1.2;"><?php kaitori_te( 'Featured Buyback Models', '買取強化商品一覧' ); ?></span>
                                    </h2>
                                </div>
                                <p class="section-subtitle" style="font-size: 13px; color: #64748b; margin: 0 auto; font-weight: 500; max-width: 600px; line-height: 1.5;"><?php kaitori_te( 'iPhone, Android Smartphones & Tablets - Top Market Rates Updated Daily', 'iPhone・最新スマホ・タブレットなど高額買取対象モデル' ); ?></p>
                                <div class="section-title-line" style="width: 48px; height: 3px; background: linear-gradient(90deg, #ef4444, #f59e0b); border-radius: 2px; margin: 12px auto 0 auto;"></div>
                            </div>
                            <div class="sub-pro-box">
                                <?php
                                $args = array(
                                    'post_type'      => 'kaitori_device',
                                    'posts_per_page' => 12,
                                    'post_status'    => 'publish',
                                    'orderby'        => 'date',
                                    'order'          => 'DESC',
                                    );
                                $device_query = new WP_Query( $args );

                                if ( $device_query->have_posts() ) :
                                    while ( $device_query->have_posts() ) : $device_query->the_post();
                                        $p_id      = get_the_ID();
                                        $price     = get_post_meta( $p_id, '_max_buyback_price', true ) ?: '195000';
                                        $jan       = get_post_meta( $p_id, '_jan_code', true ) ?: '4549995734140';
                                        $brands    = get_the_terms( $p_id, 'brand' );
                                        $brand_str = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Apple';
                                        $custom_img= get_post_meta( $p_id, '_device_primary_image_url', true );
                                        $img_url   = ! empty( $custom_img ) ? $custom_img : ( has_post_thumbnail( $p_id ) ? get_the_post_thumbnail_url( $p_id, 'medium' ) : $theme_uri . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png' );
                                        ?>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                            <div class="pro-border gutter-10 row pro_list">
                                                <div data-isok="false" data-value="<?php echo esc_attr( $p_id ); ?>" class="collect"></div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-5 text-center pro-img-box" style="padding:10px 0;">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php the_title_attribute(); ?>" class="img-responsive" style="max-height:160px; margin:0 auto; object-fit:contain;">
                                                    </a>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-7 pro-info-box">
                                                    <ul style="list-style:none; padding:0; margin:0;">
                                                        <div class="sub-pro">
                                                            <li class="sub-pro-career">
                                                                <a href="<?php echo esc_url( home_url( '/devices/?brand=' . urlencode( $brand_str ) ) ); ?>"><?php echo esc_html( $brand_str ); ?></a>
                                                            </li>
                                                            <li class="sub-pro-name">
                                                                <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                            </li>
                                                            <li class="sub-pro-name">
                                                                <a style="color:#ec1600;" title="分割残債あり(△)も減額なし対応" href="<?php the_permalink(); ?>">分割残債あり(△)も減額なし対応</a>
                                                            </li>
                                                            <li class="sub-pro-name" style="font-size:11px; color:#64748b;">JAN：<?php echo esc_html( $jan ); ?></li>
                                                        </div>
                                                        <li class="sub-pro-jia"><?php kaitori_te( 'Buyback Price:', '買取価格:' ); ?> <span>¥<?php echo number_format( (int)$price ); ?></span></li>
                                                        <li>
                                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( get_the_title() ) ) ); ?>">
                                                                <button type="button" class="btn btn-pro"><?php kaitori_te( 'Apply for Buyback', '買取を申し込む' ); ?></button>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    // 12 Fallback Cards matching index.html perfectly
                                    $sample_devices = array(
                                        array( 'title' => 'iPhone 16 Pro 256GB Black', 'price' => 195000, 'jan' => '4549995734140', 'img' => 'iPhone 18 Pro 256GB Black.png' ),
                                        array( 'title' => 'iPhone 16 Pro 256GB Silver', 'price' => 195000, 'jan' => '4549995734157', 'img' => 'iPhone 18 Pro 256GB Silver.png' ),
                                        array( 'title' => 'iPhone 16 Pro 256GB Natural', 'price' => 195000, 'jan' => '4549995734171', 'img' => 'iPhone 18 Pro 256GB Glacier.png' ),
                                        array( 'title' => 'iPhone 16 Pro 256GB Desert', 'price' => 208000, 'jan' => '4549995734164', 'img' => 'iPhone 18 Pro 256GB Burgundy.png' ),
                                        array( 'title' => 'iPhone 16 Pro 512GB Black', 'price' => 228000, 'jan' => '4549995734188', 'img' => 'iPhone 18 Pro 256GB Black.png' ),
                                        array( 'title' => 'iPhone 16 Pro 512GB Silver', 'price' => 223000, 'jan' => '4549995734195', 'img' => 'iPhone 18 Pro 256GB Silver.png' ),
                                        array( 'title' => 'iPhone 16 Pro 512GB Natural', 'price' => 228000, 'jan' => '4549995734218', 'img' => 'iPhone 18 Pro 256GB Glacier.png' ),
                                        array( 'title' => 'iPhone 16 Pro 512GB Desert', 'price' => 238000, 'jan' => '4549995734201', 'img' => 'iPhone 18 Pro 256GB Burgundy.png' ),
                                        array( 'title' => 'iPhone 16 Pro 1TB Black', 'price' => 305000, 'jan' => '4549995734225', 'img' => 'iPhone 18 Pro 256GB Black.png' ),
                                        array( 'title' => 'iPhone 16 Pro 1TB Silver', 'price' => 305000, 'jan' => '4549995734232', 'img' => 'iPhone 18 Pro 256GB Silver.png' ),
                                        array( 'title' => 'iPhone 16 Pro 1TB Natural', 'price' => 305000, 'jan' => '4549995734256', 'img' => 'iPhone 18 Pro 256GB Glacier.png' ),
                                        array( 'title' => 'iPhone 16 Pro 1TB Desert', 'price' => 308000, 'jan' => '4549995734249', 'img' => 'iPhone 18 Pro 256GB Burgundy.png' ),
                                    );
                                    foreach ( $sample_devices as $dev ) :
                                        ?>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                            <div class="pro-border gutter-10 row pro_list">
                                                <div data-isok="false" class="collect"></div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-5 text-center pro-img-box" style="padding:10px 0;">
                                                    <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>">
                                                        <img src="<?php echo esc_url( $theme_uri . '/upload/goods/20260911/' . $dev['img'] ); ?>" alt="<?php echo esc_attr( $dev['title'] ); ?>" class="img-responsive" style="max-height:160px; margin:0 auto; object-fit:contain;">
                                                    </a>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-7 pro-info-box">
                                                    <ul style="list-style:none; padding:0; margin:0;">
                                                        <div class="sub-pro">
                                                            <li class="sub-pro-career">
                                                                <a href="<?php echo esc_url( home_url( '/devices/?brand=apple' ) ); ?>">Apple</a>
                                                            </li>
                                                            <li class="sub-pro-name">
                                                                <a title="<?php echo esc_attr( $dev['title'] ); ?>" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>"><?php echo esc_html( $dev['title'] ); ?></a>
                                                            </li>
                                                            <li class="sub-pro-name">
                                                                <a style="color:#ec1600;" title="分割残債あり(△)も減額なし対応" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>">分割残債あり(△)も減額なし対応</a>
                                                            </li>
                                                            <li class="sub-pro-name" style="font-size:11px; color:#64748b;">JAN：<?php echo esc_html( $dev['jan'] ); ?></li>
                                                        </div>
                                                        <li class="sub-pro-jia"><?php kaitori_te( 'Buyback Price:', '買取価格:' ); ?> <span>¥<?php echo number_format( $dev['price'] ); ?></span></li>
                                                        <li>
                                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>">
                                                                <button type="button" class="btn btn-pro"><?php kaitori_te( 'Apply for Buyback', '買取を申し込む' ); ?></button>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="sub-pro-footer">
                                <a class="btn-ys more" href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php kaitori_te( 'View All Devices', '全機種一覧' ); ?></a>
                            </div>
                        </div>
                    </div>

                    <!-- 4.4 Key Selling Points -->
                    <div class="mainpage-box">
                        <div class="mainpage-title col-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_1.jpg" alt="Key Checkpoints to Sell Your Phone at the Highest Price" class="img-responsive" /> 
                            <h2><?php kaitori_te( 'Key Selling Points', '高く売るためのポイント' ); ?></h2>
                        </div>
                        <ul class="gutter-0" style="list-style:none; padding:0; margin:0;">
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_1.jpg" alt="1. Erase all personal data & restore factory settings" class="img-responsive" /></a> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_2.jpg" alt="2. Include original box, cables, and accessories" class="img-responsive" /></a> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_3.jpg" alt="3. Clean the device exterior thoroughly" class="img-responsive" /></a> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_4.jpg" alt="4. Unlock SIM carrier restrictions (SIM-Free)" class="img-responsive" /></a> 
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <!-- 4.5 Checkpoints Before Applying -->
                    <div class="mainpage-box">
                        <div class="mainpage-title col-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_2.jpg" alt="Please check before applying for buyback!" class="img-responsive" /> 
                        </div>
                        <ul class="gutter-10" style="list-style:none; padding:0; margin:0;">
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_1.jpg" alt="1. Check list of non-purchasable items" class="img-responsive" /></a> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_2.jpg" alt="2. Check network usage restriction status" class="img-responsive" /></a> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_3.jpg" alt="3. Please ensure SIM and memory cards are removed" class="img-responsive" /> 
                            </li>
                            <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_4.jpg" alt="4. Charge battery to at least 50% before sending" class="img-responsive" /> 
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <!-- 4.6 Identity Verification Secondhand Dealer Act Banner -->
                    <div class="mainpage-box">
                        <div class="mainpage-title col-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_3.jpg" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" /> 
                        </div>
                        <div class="mainpage-content col-center text-center">
                            <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_3.jpg" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" style="margin:0 auto; border-radius:8px;" />
                            </a>
                        </div>
                    </div>

                    <!-- 4.7 Complete Buyback Flow Infographic -->
                    <div class="mainpage-box" style="margin:0; padding-bottom:20px;">
                        <div class="mainpage-title col-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_4.jpg" alt="Buyback Process" class="img-responsive" /> 
                        </div>
                        <div class="liucheng-box gutter-25 img-margin">
                            <!-- In-Store Flow -->
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_1.jpg" alt="In-Store Buyback" class="img-responsive" /></a> 
                                <div class="liucheng-dian-box">
                                    <ul class="col-center" style="list-style:none; padding:0;">
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_1.jpg" alt="1. Check buyback price on our website" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_2.jpg" alt="2. Book your visit online (Walk-ins welcome)" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_3.jpg" alt="3. Visit our store in Akihabara, Tokyo" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_4.jpg" alt="4. Appraisal completed & instant cash payout" class="img-responsive" /></a> 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Mail-in Flow -->
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_2.jpg" alt="Mail-in Buyback" class="img-responsive" /></a> 
                                <div class="liucheng-youji-box">
                                    <ul class="col-center" style="list-style:none; padding:0;">
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_1.jpg" alt="1. Check current rates on website" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_2.jpg" alt="2. Ship device with required documents" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_3.jpg" alt="3. Receive assessment report by email" class="img-responsive" /></a> 
                                        </li>
                                        <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                        <li>
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_4.jpg" alt="4. Same-day bank transfer payout" class="img-responsive" /></a> 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div style="text-align:center; margin-top:20px;">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?type=corporate' ) ); ?>">
                                <button type="button" class="btn btn-faren col-center"><?php kaitori_te( 'Corporate Clients Click Here', '法人様買取相談はこちら' ); ?></button>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- 5. Bottom Category Links Bar (href_list) -->
    <div class="bg-white">
        <div class="href_list">
            <div class="container">
                <div class="gutter-10 clearflex">
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/phone.jpg" alt="Smartphones" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=tablets' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/ipad.jpg" alt="Tablets" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=gaming' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/game.jpg" alt="Gaming Consoles" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=cosmetics' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/meizhuang.jpg" alt="Cosmetics" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=appliances' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628171113.jpg" alt="Home Appliances" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=cameras' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628171104.jpg" alt="Cameras" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/?cat=pc' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628170919.jpg" alt="PC & Laptops" class="img-responsive" />
                        </a>
                    </div>
                    <div class="href_item mtop10 col-xs-6 col-sm-3">
                        <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180712/kaitoriwiki_link.jpg" alt="Kaitori Akiba" class="img-responsive" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
endif;

get_footer();

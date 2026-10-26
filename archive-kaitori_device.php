<?php
/**
 * Archive Template for Kaitori Devices
 *
 * @package KaitoriAkiba
 */

get_header();
?>

<div class="container main-content-container" style="margin-top: 24px; margin-bottom: 60px;">
    
    <!-- Breadcrumb -->
    <div style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#64748b; text-decoration:none;">ホーム</a> &gt; 
        <span style="color:#0f172a; font-weight:700;">買取対象機種一覧</span>
    </div>

    <!-- Search Box (Dual Search) -->
    <div class="search" style="margin-bottom: 24px;">
        <div class="row">
            <!-- Col 1: Keyword Search -->
            <div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
                <div class="search_title">
                    <div class="search_title_h2"><?php _e( 'Search by device model name or model number!', 'kaitoriakiba' ); ?></div>
                    <div class="search_title_h3"><?php _e( 'Please enter device name, model number, JAN code, etc.', 'kaitoriakiba' ); ?></div>					
                </div>
                <div class="search_box">
                    <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'kaitori_device' ) ); ?>">
                        <div class="form-group mbtm0 clearfix">
                            <div class="keyword_search">
                                <select class="form-control fw_cat" name="cat">
                                    <option value=""><?php _e( 'All Categories', 'kaitoriakiba' ); ?></option>
                                    <?php
                                    $cats = get_terms( array( 'taxonomy' => 'device_category', 'hide_empty' => false ) );
                                    if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                        foreach ( $cats as $c ) {
                                            $sel = ( isset( $_GET['cat'] ) && $_GET['cat'] === $c->slug ) ? 'selected' : '';
                                            echo '<option value="' . esc_attr( $c->slug ) . '" ' . $sel . '>' . esc_html( $c->name ) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <input type="text" class="form-control keyword_search_text q" name="q" value="<?php echo esc_attr( get_query_var( 's' ) ?: ( isset( $_GET['q'] ) ? $_GET['q'] : '' ) ); ?>" placeholder="<?php esc_attr_e( 'Keyword search', 'kaitoriakiba' ); ?>" autocomplete="off">
                                <button class="btn search_word" type="submit"><?php _e( 'Search', 'kaitoriakiba' ); ?> <i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Col 2: Step-by-Step Search -->
            <div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
                <div class="search_title">
                    <div class="search_title_h2"><?php _e( 'Step-by-Step Category Search', 'kaitoriakiba' ); ?></div>
                    <div class="search_title_h3"><?php _e( 'Select one or more filters', 'kaitoriakiba' ); ?></div>					
                </div>
                <div class="search_box">
                    <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'kaitori_device' ) ); ?>">
                        <div class="form-group">
                            <div class="step_search">
                                <div class="step_search_filters">
                                    <select class="form-control s_cat" name="cat">
                                        <option value=""><?php _e( 'Select Category', 'kaitoriakiba' ); ?></option>
                                        <?php
                                        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                            foreach ( $cats as $c ) {
                                                $sel = ( isset( $_GET['cat'] ) && $_GET['cat'] === $c->slug ) ? 'selected' : '';
                                                echo '<option value="' . esc_attr( $c->slug ) . '" ' . $sel . '>' . esc_html( $c->name ) . '</option>';
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
                                                $sel = ( isset( $_GET['s_career'] ) && $_GET['s_career'] === $car->slug ) ? 'selected' : '';
                                                echo '<option value="' . esc_attr( $car->slug ) . '" ' . $sel . '>' . esc_html( $car->name ) . '</option>';
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
                                                $sel = ( isset( $_GET['brand'] ) && $_GET['brand'] === $b->slug ) ? 'selected' : '';
                                                echo '<option value="' . esc_attr( $b->slug ) . '" ' . $sel . '>' . esc_html( $b->name ) . '</option>';
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
                                                $sel = ( isset( $_GET['series'] ) && $_GET['series'] === $s->slug ) ? 'selected' : '';
                                                echo '<option value="' . esc_attr( $s->slug ) . '" ' . $sel . '>' . esc_html( $s->name ) . '</option>';
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

    <!-- Main Layout -->
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
            <?php get_sidebar(); ?>
        </div>

        <!-- Products List -->
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
            <div class="section-title-wrap" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #e2e8f0; padding-bottom:12px;">
                <h1 style="font-size:22px; font-weight:900; color:#0f172a; margin:0;">
                    <i class="fa fa-mobile" style="color:#ef4444;"></i> 
                    <?php
                    if ( is_tax() ) {
                        single_term_title();
                    } else {
                        _e( '買取機種・価格一覧 (Device Price List)', 'kaitoriakiba' );
                    }
                    ?>
                </h1>
                <span style="font-size:13px; color:#64748b;">
                    全 <?php echo esc_html( $wp_query->found_posts ); ?> 件
                </span>
            </div>

            <div id="live-device-results">
                <?php if ( have_posts() ) : ?>
                    <div class="row pro_list_row">
                        <?php
                        while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content', 'device-card' );
                        endwhile;
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="text-center" style="margin-top: 30px;">
                        <?php
                        the_posts_pagination( array(
                            'prev_text' => '&laquo; 前へ',
                            'next_text' => '次へ &raquo;',
                            'type'      => 'list',
                        ) );
                        ?>
                    </div>

                <?php else : ?>
                    <div class="alert alert-warning text-center" style="padding: 40px 20px; border-radius: 12px; margin-top: 20px;">
                        <i class="fa fa-search" style="font-size: 32px; color: #f59e0b; margin-bottom: 12px; display: block;"></i>
                        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">該当する買取機種が見つかりませんでした</h3>
                        <p style="color: #64748b; font-size: 13.5px; margin-bottom: 16px;">条件を変えて検索するか、お電話・LINEにて直接お問い合わせください。</p>
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'kaitori_device' ) ); ?>" class="btn btn-default" style="font-weight: 700;">全機種一覧を表示</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();

<?php
/**
 * Sidebar Template for Kaitori Akiba
 *
 * @package KaitoriAkiba
 */
?>
<!--sidebar-s-->
<div class="collapse in" id="main-menu">
    <div class="navigation-ad">
        <ul class="gutter-0 img-margin" style="list-style:none; padding:0; margin:0;">
            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 img-left" style="margin-bottom:10px;">
                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_top_1.jpg" alt="First-Time Guide" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                </a>
            </li>
            <div class="clear"></div>
            <li class="zxlx_nav_box" style="margin:0 0 10px 0; width:100%;">
                <div><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/zxlx_top.jpg" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" /></div>
                <div><a href="https://twitter.com/kaitoriakiba" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/zxlx_twitter.jpg" alt="Twitter" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
                <div><a href="https://lin.ee/5wvtHzl" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/zxlx_line.jpg" alt="Add on LINE" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
            </li>
            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 img-left" style="margin-bottom:10px;">
                <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20210621/member_rank02.png" alt="Member Benefits" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>

    <!-- Browse by Carrier (100% Full Width, No Margin Auto) -->
    <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>" style="color:#ffffff; text-decoration:none;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span>Browse by Carrier</span></a>
        </div>
        <ul style="list-style:none; padding:8px; margin:0;">
            <?php
            $careers = array(
                'docomo'        => 'upload/career/docomo.jpg',
                'au'            => 'upload/career/au.jpg',
                'softbank'      => 'upload/career/softbank.jpg',
                'Y!mobile'      => 'upload/career/Y!mobile.jpg',
                'uqmobile'      => 'upload/career/uqmobile.jpg',
                'simfree'       => 'upload/career/simfree.jpg',
                'RakutenMobile' => 'upload/career/simfree.jpg',
            );
            foreach ( $careers as $c_slug => $c_path ) :
                $c_img = file_exists( get_template_directory() . '/' . $c_path ) ? get_template_directory_uri() . '/' . $c_path : get_template_directory_uri() . '/upload/career/simfree.jpg';
            ?>
                <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                    <a href="<?php echo esc_url( home_url( '/devices/?s_career=' . urlencode( $c_slug ) ) ); ?>" style="display:block;">
                        <img src="<?php echo esc_url( $c_img ); ?>" alt="<?php echo esc_attr( $c_slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>

    <!-- Browse by Brand (100% Full Width, No Margin Auto) -->
    <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>" style="color:#ffffff; text-decoration:none;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span>Browse by Brand</span></a>
        </div>
        <ul style="list-style:none; padding:8px; margin:0;">
            <?php
            $brands = array(
                'apple'    => 'upload/brand/apple.jpg',
                'Xiaomi'   => 'upload/brand/20200603/3.png',
                'OPPO'     => 'upload/brand/OPPO.jpg',
                'asus'     => 'upload/brand/asus.jpg',
                'fujitsu'  => 'upload/brand/fujitsu.jpg',
                'samsung'  => 'upload/brand/samsung.jpg',
                'google'   => 'upload/brand/google.jpg',
                'huawei'   => 'upload/brand/huawei.jpg',
                'kyocera'  => 'upload/brand/kyocera.jpg',
                'motorola' => 'upload/brand/moto.jpg',
                'sharp'    => 'upload/brand/sharp.jpg',
                'sony'     => 'upload/brand/sony.jpg',
            );
            foreach ( $brands as $b_slug => $b_path ) :
                $b_img = file_exists( get_template_directory() . '/' . $b_path ) ? get_template_directory_uri() . '/' . $b_path : get_template_directory_uri() . '/upload/brand/apple.jpg';
            ?>
                <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                    <a href="<?php echo esc_url( home_url( '/devices/?brand=' . urlencode( $b_slug ) ) ); ?>" style="display:block;">
                        <img src="<?php echo esc_url( $b_img ); ?>" alt="<?php echo esc_attr( $b_slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>

    <!-- Browse by Series (100% Full Width, No Margin Auto) -->
    <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>" style="color:#ffffff; text-decoration:none;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span>Browse by Series</span></a>
        </div>
        <ul style="list-style:none; padding:8px; margin:0;">
            <?php
            $series = array(
                'iphone'            => 'upload/series/iphone.jpg',
                'xperia'            => 'upload/series/xperia.jpg',
                'galaxy'            => 'upload/series/galaxy.jpg',
                'aquos'             => 'upload/series/aquos.jpg',
                'arrows'            => 'upload/series/arrows.jpg',
                'zenfone'           => 'upload/series/zenfone.jpg',
                'simple-smartphone' => 'upload/series/simple-smartphone.jpg',
                'raijin'            => 'upload/series/raijin.jpg',
                'nexus'             => 'upload/series/nexus.jpg',
                'android-one'       => 'upload/series/android-one.jpg',
            );
            foreach ( $series as $s_slug => $s_path ) :
                $s_img = file_exists( get_template_directory() . '/' . $s_path ) ? get_template_directory_uri() . '/' . $s_path : get_template_directory_uri() . '/upload/series/iphone.jpg';
            ?>
                <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                    <a href="<?php echo esc_url( home_url( '/devices/?series=' . urlencode( $s_slug ) ) ); ?>" style="display:block;">
                        <img src="<?php echo esc_url( $s_img ); ?>" alt="<?php echo esc_attr( $s_slug ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>

    <!-- Quick Links Menu -->
    <div class="navigation-box2" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:#0f172a; padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span>Information &amp; Guides</span></div>
        <ul class="navigation-h3" style="list-style:none; padding:10px 14px; margin:0;">
            <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span>買取申込 (Online Application)</span></a></li>
            <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span>本人確認 (eKYC Verification)</span></a></li>
            <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span>新規会員登録 (Register)</span></a></li>
            <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span>買取価格一覧 (Price List)</span></a></li>
        </ul>
    </div>
</div>
<!--sidebar-e-->
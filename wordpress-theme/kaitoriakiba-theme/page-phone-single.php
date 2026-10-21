<?php
/**
 * Template Name: Phone Single Showcase (端末詳細・買取価格)
 * 
 * Showcase single phone template with live interactive pricing calculator,
 * Rank S-D table, and direct application linkage. Supports Elementor editing.
 *
 * @package KaitoriAkiba
 */

get_header();

// Check if page built with Elementor
$is_elementor = false;
if ( did_action( 'elementor/loaded' ) ) {
    $document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
    if ( $document && $document->is_built_with_elementor() ) {
        $is_elementor = true;
    }
}

if ( $is_elementor ) :
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
else :
    // Load device data or default to flagship iPhone 16 Pro
    $sample = get_posts( array( 'post_type' => 'kaitori_device', 'posts_per_page' => 1 ) );
    $dev_id = ! empty( $sample ) ? $sample[0]->ID : 0;

    $title       = $dev_id ? get_the_title( $dev_id ) : 'iPhone 16 Pro 256GB (SIMフリー)';
    $base_price  = $dev_id ? ( get_post_meta( $dev_id, '_max_buyback_price', true ) ?: 168000 ) : 168000;
    $price_s     = $dev_id ? ( get_post_meta( $dev_id, '_price_rank_s', true ) ?: $base_price ) : 168000;
    $price_a     = $dev_id ? ( get_post_meta( $dev_id, '_price_rank_a', true ) ?: round( $base_price * 0.92 ) ) : 155000;
    $price_b     = $dev_id ? ( get_post_meta( $dev_id, '_price_rank_b', true ) ?: round( $base_price * 0.84 ) ) : 142000;
    $price_c     = $dev_id ? ( get_post_meta( $dev_id, '_price_rank_c', true ) ?: round( $base_price * 0.76 ) ) : 128000;
    $price_d     = $dev_id ? ( get_post_meta( $dev_id, '_price_rank_d', true ) ?: round( $base_price * 0.48 ) ) : 82000;
    $installment = $dev_id ? ( get_post_meta( $dev_id, '_installment_deduction', true ) ?: 5000 ) : 5000;
    $model_num   = $dev_id ? ( get_post_meta( $dev_id, '_model_number', true ) ?: 'A3293' ) : 'A3293';
    $jan_code    = $dev_id ? ( get_post_meta( $dev_id, '_jan_code', true ) ?: '4549995734140' ) : '4549995734140';
    $custom_img  = $dev_id ? get_post_meta( $dev_id, '_device_primary_image_url', true ) : '';
    $thumb_url   = ! empty( $custom_img ) ? $custom_img : ( ( $dev_id && has_post_thumbnail( $dev_id ) ) ? get_the_post_thumbnail_url( $dev_id, 'large' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png' );

    // Extract gallery images or fallback
    $gallery_meta = $dev_id ? get_post_meta( $dev_id, '_device_gallery_images', true ) : null;
    $gallery_list = array();
    if ( ! empty( $gallery_meta ) && is_array( $gallery_meta ) ) {
        $gallery_list = $gallery_meta;
    } else {
        $gallery_list = array(
            array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png', 'label' => 'Black Titanium' ),
            array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Silver.png', 'label' => 'White Titanium' ),
            array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Glacier.png', 'label' => 'Natural Titanium' ),
            array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Burgundy.png', 'label' => 'Desert Titanium' ),
        );
    }
    ?>

    <div class="single-product-wrapper container" style="margin-top: 24px; margin-bottom: 60px;">

        <!-- Breadcrumb Navigation -->
        <div class="product-breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#64748b; margin-bottom:24px;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa fa-home"></i> <?php _e( 'トップ (Home)', 'kaitoriakiba' ); ?></a>
            <span class="sep"><i class="fa fa-chevron-right" style="font-size:11px;"></i></span>
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( '買取機種一覧', 'kaitoriakiba' ); ?></a>
            <span class="sep"><i class="fa fa-chevron-right" style="font-size:11px;"></i></span>
            <span class="current" style="color:#0f172a; font-weight:700;"><?php echo esc_html( $title ); ?></span>
        </div>

        <!-- Product Hero Card -->
        <div class="product-hero-card" style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 4px 20px rgba(0,0,0,0.04); padding:32px; margin-bottom:32px;">
            <div class="row">
                <!-- Left: Media Gallery -->
                <div class="col-lg-5 col-md-5 col-sm-12 text-center">
                    <div style="background:#ffffff; border:1px solid #f1f5f9; border-radius:12px; padding:24px; min-height:360px; display:flex; align-items:center; justify-content:center; position:relative;">
                        <span style="position:absolute; top:16px; left:16px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:#fff; font-size:12px; font-weight:800; padding:4px 12px; border-radius:20px;">高価買取強化中</span>
                        <img id="mainProductImg" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="max-height:320px; max-width:100%; object-fit:contain;">
                    </div>
                    <div style="display:flex; justify-content:center; gap:10px; margin-top:16px; flex-wrap:wrap;">
                        <?php foreach ( $gallery_list as $g_idx => $g_item ) : 
                            $g_url = is_array( $g_item ) ? $g_item['url'] : $g_item;
                            $g_label = is_array( $g_item ) && ! empty( $g_item['label'] ) ? $g_item['label'] : '';
                            if ( empty( $g_url ) ) continue;
                        ?>
                            <div class="gallery-thumb <?php echo $g_idx === 0 ? 'active' : ''; ?>" title="<?php echo esc_attr( $g_label ); ?>" style="width:56px; height:56px; border:<?php echo $g_idx === 0 ? '2px solid #ef4444' : '1px solid #e2e8f0'; ?>; border-radius:8px; padding:3px; cursor:pointer;" onclick="switchDeviceImg('<?php echo esc_url( $g_url ); ?>', this)">
                                <img src="<?php echo esc_url( $g_url ); ?>" alt="<?php echo esc_attr( $g_label ); ?>" style="width:100%; height:100%; object-fit:contain;">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Trust Checklist -->
                    <div style="background:#f8fafc; border-radius:10px; padding:14px; margin-top:20px; text-align:left; font-size:12.5px; color:#334155; line-height:1.7; border:1px dashed #cbd5e1;">
                        <div><i class="fa fa-check-circle" style="color:#10b981; margin-right:6px;"></i> 当日価格保証：Web事前査定額を完全キープ</div>
                        <div><i class="fa fa-check-circle" style="color:#10b981; margin-right:6px;"></i> 分割中 (利用制限△) でも減額最小限・即日現金化</div>
                        <div><i class="fa fa-check-circle" style="color:#10b981; margin-right:6px;"></i> 秋葉原店頭なら最短5分で即手渡し / 郵送は即日振込</div>
                    </div>
                </div>

                <!-- Right: Product Specifications & Live Dynamic Price -->
                <div class="col-lg-7 col-md-7 col-sm-12">
                    <div style="border-bottom:1.5px solid #f1f5f9; padding-bottom:14px; margin-bottom:18px;">
                        <span style="display:inline-block; font-size:12px; font-weight:700; color:#ef4444; background:#fef2f2; padding:3px 10px; border-radius:6px; margin-bottom:8px;">
                            Apple / iPhone
                        </span>
                        <h1 id="deviceTitleHeading" style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 6px 0;"><?php echo esc_html( $title ); ?></h1>
                        <p style="font-size:13px; color:#64748b; margin:0;">型番: <?php echo esc_html( $model_num ); ?> | JAN: <?php echo esc_html( $jan_code ); ?> | 国内正規SIMフリー版 &amp; 各社キャリア版対応</p>
                    </div>

                    <!-- Live Price Box -->
                    <div style="background:linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border:2px solid #fed7aa; border-radius:14px; padding:20px 24px; margin-bottom:24px;">
                        <div style="font-size:13px; font-weight:700; color:#c2410c; margin-bottom:6px;">
                            <i class="fa fa-bolt"></i> 本日の最高買取上限価格 (新品未開封・SIMフリー)
                        </div>
                        <div style="display:flex; align-items:baseline; gap:8px;">
                            <span style="font-size:24px; font-weight:800; color:#dc2626;">¥</span>
                            <span id="livePriceNum" style="font-size:38px; font-weight:900; color:#dc2626; line-height:1;"><?php echo number_format((int)$base_price); ?></span>
                            <span style="font-size:12px; font-weight:700; color:#78350f; background:#fed7aa; padding:3px 10px; border-radius:20px; margin-left:8px;">税込・手数料0円</span>
                        </div>
                        <div style="font-size:12px; color:#9a3412; margin-top:8px;">
                            <i class="fa fa-shield"></i> 本日中のWeb査定申し込みで上記価格を完全保証いたします
                        </div>
                    </div>

                    <!-- 1. Storage Capacity Pills -->
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13.5px; font-weight:800; color:#1e293b; display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span>1. ストレージ容量を選択</span>
                            <span id="activeStorageText" style="color:#ef4444;">256GB</span>
                        </label>
                        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
                            <div class="storage-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeStorage('128GB', <?php echo intval($base_price * 0.9); ?>, this)">128GB</div>
                            <div class="storage-btn active" style="border:1.5px solid #ef4444; background:#fef2f2; color:#dc2626; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeStorage('256GB', <?php echo intval($base_price); ?>, this)">256GB</div>
                            <div class="storage-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeStorage('512GB', <?php echo intval($base_price * 1.1); ?>, this)">512GB</div>
                            <div class="storage-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeStorage('1TB', <?php echo intval($base_price * 1.2); ?>, this)">1TB</div>
                        </div>
                    </div>

                    <!-- 2. Carrier Selector -->
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13.5px; font-weight:800; color:#1e293b; display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span>2. キャリア / SIMロック状態</span>
                            <span id="activeCarrierText" style="color:#ef4444;">SIMフリー</span>
                        </label>
                        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
                            <div class="carrier-btn active" style="border:1.5px solid #ef4444; background:#fef2f2; color:#dc2626; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeCarrier('SIMフリー', 0, this)">SIMフリー</div>
                            <div class="carrier-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeCarrier('docomo', -2000, this)">docomo</div>
                            <div class="carrier-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeCarrier('au', -2000, this)">au</div>
                            <div class="carrier-btn" style="border:1.5px solid #cbd5e1; border-radius:8px; padding:10px; text-align:center; font-weight:700; cursor:pointer;" onclick="changeCarrier('SoftBank', -2000, this)">SoftBank</div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div style="margin-top:28px;">
                        <a id="applyCtaLink" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $title ) . '&storage=256GB' ) ); ?>" class="btn-apply-main" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:#fff; font-size:16px; font-weight:800; padding:16px; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:10px; text-decoration:none; box-shadow:0 6px 20px rgba(239,68,68,0.35);">
                            <i class="fa fa-paper-plane"></i> この機種の買取を申し込む (Apply for Buyback)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Condition / Rank S-D Price Table -->
        <div style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.04); margin-bottom:32px;">
            <h2 style="font-size:19px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:18px; border-bottom:2px solid #f1f5f9; padding-bottom:12px;">
                <i class="fa fa-sliders" style="color:#ef4444;"></i> コンディション別買取価格一覧表 (Rank Pricing)
            </h2>
            <table class="rank-price-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                        <th style="padding:12px 14px; width:15%;">ランク</th>
                        <th style="padding:12px 14px; width:25%;">状態の目安</th>
                        <th style="padding:12px 14px; width:35%;">査定詳細・付属品条件</th>
                        <th style="padding:12px 14px; width:25%;">買取目安価格</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 14px;"><span class="badge-rank badge-rank-s" style="background:#fef3c7; color:#b45309; padding:4px 8px; border-radius:6px; font-weight:800; font-size:12px;">新品 (S)</span></td>
                        <td style="padding:12px 14px;"><strong>未開封・未使用新品</strong></td>
                        <td style="padding:12px 14px; font-size:13px; color:#64748b;">元箱の封印シールが剥がされていない未開封品。アクティベーション前。</td>
                        <td style="padding:12px 14px;"><span class="table-price-val" id="rankPriceS" style="font-weight:900; color:#dc2626; font-size:16px;">¥<?php echo number_format((int)$price_s); ?></span></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 14px;"><span class="badge-rank badge-rank-a" style="background:#dbeafe; color:#1d4ed8; padding:4px 8px; border-radius:6px; font-weight:800; font-size:12px;">未使用 (A)</span></td>
                        <td style="padding:12px 14px;"><strong>開封済み・未使用品</strong></td>
                        <td style="padding:12px 14px; font-size:13px; color:#64748b;">通電確認のみ実施。本体・液晶に傷や汚れなし。付属品欠品なし。</td>
                        <td style="padding:12px 14px;"><span class="table-price-val" id="rankPriceA" style="font-weight:900; color:#dc2626; font-size:16px;">¥<?php echo number_format((int)$price_a); ?></span></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 14px;"><span class="badge-rank badge-rank-b" style="background:#dcfce7; color:#15803d; padding:4px 8px; border-radius:6px; font-weight:800; font-size:12px;">美品 (B)</span></td>
                        <td style="padding:12px 14px;"><strong>中古美品 (目立つ傷なし)</strong></td>
                        <td style="padding:12px 14px; font-size:13px; color:#64748b;">使用感が少なく液晶・外装ともに綺麗な状態。バッテリー最大容量85%以上。</td>
                        <td style="padding:12px 14px;"><span class="table-price-val" id="rankPriceB" style="font-weight:900; color:#dc2626; font-size:16px;">¥<?php echo number_format((int)$price_b); ?></span></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 14px;"><span class="badge-rank badge-rank-c" style="background:#f1f5f9; color:#475569; padding:4px 8px; border-radius:6px; font-weight:800; font-size:12px;">良品 (C)</span></td>
                        <td style="padding:12px 14px;"><strong>中古良品 (小傷・スレあり)</strong></td>
                        <td style="padding:12px 14px; font-size:13px; color:#64748b;">日常使用に伴う軽微な擦り傷・小打痕あり。動作全機能正常。</td>
                        <td style="padding:12px 14px;"><span class="table-price-val" id="rankPriceC" style="font-weight:900; color:#dc2626; font-size:16px;">¥<?php echo number_format((int)$price_c); ?></span></td>
                    </tr>
                    <tr>
                        <td style="padding:12px 14px;"><span class="badge-rank badge-rank-d" style="background:#fee2e2; color:#b91c1c; padding:4px 8px; border-radius:6px; font-weight:800; font-size:12px;">難あり (D)</span></td>
                        <td style="padding:12px 14px;"><strong>画面割れ・故障</strong></td>
                        <td style="padding:12px 14px; font-size:13px; color:#64748b;">ガラス割れ、カメラ不具合、バッテリー著しい劣化等のジャンク品。</td>
                        <td style="padding:12px 14px;"><span class="table-price-val" id="rankPriceD" style="font-weight:900; color:#dc2626; font-size:16px;">¥<?php echo number_format((int)$price_d); ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 3: Installment Notice -->
        <div style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.04); margin-bottom:32px;">
            <h2 style="font-size:18px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:14px;">
                <i class="fa fa-info-circle" style="color:#ef4444;"></i> 分割支払い中（ネットワーク利用制限△）でも高価買取対応！
            </h2>
            <p style="font-size:13.5px; color:#475569; line-height:1.6;">
                当店では、<strong>端末代金の分割支払い中（残債あり）のiPhone・スマホでも積極的に高価買取</strong>を行っております。「他店で断られた」「減額幅が大きすぎた」という場合でも、安心してお申し込みください。
            </p>
            <div class="restriction-grid" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-top:16px;">
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; padding:16px;">
                    <div style="font-weight:800; color:#16a34a; margin-bottom:6px;"><i class="fa fa-circle"></i> 利用制限 ○ (一括完済)</div>
                    <p style="font-size:12.5px; color:#64748b; margin:0;">端末代金全額完済済み。上限価格100%で買取いたします。</p>
                </div>
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; padding:16px;">
                    <div style="font-weight:800; color:#ea580c; margin-bottom:6px;"><i class="fa fa-adjust"></i> 利用制限 △ (分割支払い中)</div>
                    <p style="font-size:12.5px; color:#64748b; margin:0;">キャリアへ分割代金お支払い中。僅かな減額（-¥<?php echo number_format((int)$installment); ?>程度）で即日現金化可能！</p>
                </div>
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; padding:16px;">
                    <div style="font-weight:800; color:#dc2626; margin-bottom:6px;"><i class="fa fa-times-circle"></i> 利用制限 × (利用制限中)</div>
                    <p style="font-size:12.5px; color:#64748b; margin:0;">ネットワークロック状態。部品取り用として個別査定いたします。</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        var currentBasePrice = <?php echo (int)$base_price; ?>;
        var carrierOffset = 0;

        function switchDeviceImg(src, el) {
            document.getElementById('mainProductImg').src = src;
            document.querySelectorAll('.gallery-thumb').forEach(function(t) {
                t.style.borderColor = '#e2e8f0';
            });
            el.style.borderColor = '#ef4444';
        }

        function changeStorage(size, price, el) {
            document.querySelectorAll('.storage-btn').forEach(function(b) {
                b.style.borderColor = '#cbd5e1';
                b.style.background = '#ffffff';
                b.style.color = '#1e293b';
            });
            el.style.borderColor = '#ef4444';
            el.style.background = '#fef2f2';
            el.style.color = '#dc2626';

            document.getElementById('activeStorageText').innerText = size;
            currentBasePrice = price;
            updateDynamicPrices();
        }

        function changeCarrier(carrier, offset, el) {
            document.querySelectorAll('.carrier-btn').forEach(function(b) {
                b.style.borderColor = '#cbd5e1';
                b.style.background = '#ffffff';
                b.style.color = '#1e293b';
            });
            el.style.borderColor = '#ef4444';
            el.style.background = '#fef2f2';
            el.style.color = '#dc2626';

            document.getElementById('activeCarrierText').innerText = carrier;
            carrierOffset = offset;
            updateDynamicPrices();
        }

        function updateDynamicPrices() {
            var finalMax = Math.max(10000, currentBasePrice + carrierOffset);
            document.getElementById('livePriceNum').innerText = finalMax.toLocaleString();
            document.getElementById('rankPriceS').innerText = '¥' + finalMax.toLocaleString();
            document.getElementById('rankPriceA').innerText = '¥' + Math.round(finalMax * 0.92).toLocaleString();
            document.getElementById('rankPriceB').innerText = '¥' + Math.round(finalMax * 0.84).toLocaleString();
            document.getElementById('rankPriceC').innerText = '¥' + Math.round(finalMax * 0.76).toLocaleString();
            document.getElementById('rankPriceD').innerText = '¥' + Math.round(finalMax * 0.48).toLocaleString();

            var storage = document.getElementById('activeStorageText').innerText;
            var title = document.getElementById('deviceTitleHeading').innerText;
            var link = '<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>?model=' + encodeURIComponent(title) + '&storage=' + encodeURIComponent(storage);
            document.getElementById('applyCtaLink').href = link;
        }
    </script>
    <?php
endif;

get_footer();

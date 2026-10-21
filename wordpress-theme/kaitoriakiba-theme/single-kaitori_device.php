<?php
/**
 * Single Device Buyback Template
 * 
 * Dynamic buyback details, tiered condition prices (Rank S-D), and interactive calculation.
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
    while ( have_posts() ) : the_post();
        $post_id     = get_the_ID();
        $base_price  = get_post_meta( $post_id, '_max_buyback_price', true ) ?: '168000';
        $price_s     = get_post_meta( $post_id, '_price_rank_s', true ) ?: $base_price;
        $price_a     = get_post_meta( $post_id, '_price_rank_a', true ) ?: round( $base_price * 0.92 );
        $price_b     = get_post_meta( $post_id, '_price_rank_b', true ) ?: round( $base_price * 0.84 );
        $price_c     = get_post_meta( $post_id, '_price_rank_c', true ) ?: round( $base_price * 0.76 );
        $price_d     = get_post_meta( $post_id, '_price_rank_d', true ) ?: round( $base_price * 0.48 );
        $installment = get_post_meta( $post_id, '_installment_deduction', true ) ?: '5000';
        $model_num   = get_post_meta( $post_id, '_model_number', true ) ?: 'A3293';
        $jan_code    = get_post_meta( $post_id, '_jan_code', true ) ?: '4549995734140';
        $custom_img  = get_post_meta( $post_id, '_device_primary_image_url', true );
        $thumb_url   = ! empty( $custom_img ) ? $custom_img : ( has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'large' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png' );

        // Extract gallery images or fallback
        $gallery_meta = get_post_meta( $post_id, '_device_gallery_images', true );
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

        // Extract series & brand
        $brands  = get_the_terms( $post_id, 'brand' );
        $brand_str = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Apple';
        $series  = get_the_terms( $post_id, 'series' );
        $series_str = ( ! empty( $series ) && ! is_wp_error( $series ) ) ? $series[0]->name : 'iPhone';
    ?>

    <div class="single-product-wrapper container" style="margin-top: 24px; margin-bottom: 60px;">

        <!-- Breadcrumb Navigation -->
        <div class="product-breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#64748b; margin-bottom:24px;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa fa-home"></i> <?php _e( 'トップ (Home)', 'kaitoriakiba' ); ?></a>
            <span class="sep"><i class="fa fa-chevron-right" style="font-size:11px;"></i></span>
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( '買取機種一覧', 'kaitoriakiba' ); ?></a>
            <span class="sep"><i class="fa fa-chevron-right" style="font-size:11px;"></i></span>
            <span class="current" style="color:#0f172a; font-weight:700;"><?php the_title(); ?></span>
        </div>

        <!-- Product Hero Card -->
        <div class="product-hero-card" style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 4px 20px rgba(0,0,0,0.04); padding:32px; margin-bottom:32px;">
            <div class="row">
                <!-- Left: Media Gallery -->
                <div class="col-lg-5 col-md-5 col-sm-12 text-center">
                    <div style="background:#ffffff; border:1px solid #f1f5f9; border-radius:12px; padding:24px; min-height:360px; display:flex; align-items:center; justify-content:center; position:relative;">
                        <span style="position:absolute; top:16px; left:16px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:#fff; font-size:12px; font-weight:800; padding:4px 12px; border-radius:20px;">高価買取強化中</span>
                        <img id="mainProductImg" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" style="max-height:320px; max-width:100%; object-fit:contain;">
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
                            <?php echo esc_html( $brand_str ); ?> / <?php echo esc_html( $series_str ); ?>
                        </span>
                        <h1 id="deviceTitleHeading" style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 6px 0;"><?php the_title(); ?></h1>
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
                        <a id="applyCtaLink" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( get_the_title() ) . '&storage=256GB' ) ); ?>" class="btn-apply-main" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:#fff; font-size:16px; font-weight:800; padding:16px; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:10px; text-decoration:none; box-shadow:0 6px 20px rgba(239,68,68,0.35);">
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
            <table class="rank-price-table">
                <thead>
                    <tr>
                        <th style="width:15%;">ランク</th>
                        <th style="width:25%;">状態の目安</th>
                        <th style="width:35%;">査定詳細・付属品条件</th>
                        <th style="width:25%;">買取目安価格</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge-rank badge-rank-s">新品 (S)</span></td>
                        <td><strong>未開封・未使用新品</strong></td>
                        <td>元箱の封印シールが剥がされていない未開封品。アクティベーション前。</td>
                        <td><span class="table-price-val" id="rankPriceS">¥<?php echo number_format((int)$price_s); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="badge-rank badge-rank-a">未使用 (A)</span></td>
                        <td><strong>開封済み・未使用品</strong></td>
                        <td>通電確認のみ実施。本体・液晶に傷や汚れなし。付属品欠品なし。</td>
                        <td><span class="table-price-val" id="rankPriceA">¥<?php echo number_format((int)$price_a); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="badge-rank badge-rank-b">美品 (B)</span></td>
                        <td><strong>中古美品 (目立つ傷なし)</strong></td>
                        <td>使用感が少なく液晶・外装ともに綺麗な状態。バッテリー最大容量85%以上。</td>
                        <td><span class="table-price-val" id="rankPriceB">¥<?php echo number_format((int)$price_b); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="badge-rank badge-rank-c">良品 (C)</span></td>
                        <td><strong>中古良品 (小傷・スレあり)</strong></td>
                        <td>日常使用に伴う軽微な擦り傷・小打痕あり。動作全機能正常。</td>
                        <td><span class="table-price-val" id="rankPriceC">¥<?php echo number_format((int)$price_c); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="badge-rank badge-rank-d">難あり (D)</span></td>
                        <td><strong>画面割れ・背面割れ・故障</strong></td>
                        <td>ガラス割れ、カメラ不具合、バッテリー著しい劣化等のジャンク品。</td>
                        <td><span class="table-price-val" id="rankPriceD">¥<?php echo number_format((int)$price_d); ?></span></td>
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

        <!-- Section 4: Pre-shipment Checklist -->
        <div style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.04); margin-bottom:32px;">
            <h2 style="font-size:18px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:16px;">
                <i class="fa fa-check-square-o" style="color:#ef4444;"></i> 発送前・持込前のかんたん初期化チェック
            </h2>
            <div class="row">
                <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; height:100%;">
                        <div style="font-weight:800; font-size:13.5px; color:#0f172a; margin-bottom:4px;">1. 「iPhoneを探す」オフ</div>
                        <p style="font-size:12px; color:#64748b; margin:0;">設定 &gt; Apple ID &gt; 探す &gt; 「iPhoneを探す」をオフにしてください。</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; height:100%;">
                        <div style="font-weight:800; font-size:13.5px; color:#0f172a; margin-bottom:4px;">2. Apple ID サインアウト</div>
                        <p style="font-size:12px; color:#64748b; margin:0;">設定最上部のユーザー名 &gt; 最下部「サインアウト」を実行。</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; height:100%;">
                        <div style="font-weight:800; font-size:13.5px; color:#0f172a; margin-bottom:4px;">3. すべてのコンテンツ消去</div>
                        <p style="font-size:12px; color:#64748b; margin:0;">一般 &gt; 転送またはiPhoneをリセット &gt; 「すべてのコンテンツと設定を消去」。</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; height:100%;">
                        <div style="font-weight:800; font-size:13.5px; color:#0f172a; margin-bottom:4px;">4. SIMカード抜き忘れ確認</div>
                        <p style="font-size:12px; color:#64748b; margin:0;">本体側面トレイの物理SIMカードを取り出してください。</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        var currentBasePrice = <?php echo intval($base_price); ?>;
        var currentCarrierDiff = 0;
        var currentStorage = "256GB";
        var currentCarrier = "SIMフリー";
        var rawTitle = "<?php echo esc_js( get_the_title() ); ?>";

        function updateSinglePrices() {
            var finalPrice = Math.max(0, currentBasePrice + currentCarrierDiff);
            document.getElementById('livePriceNum').innerText = finalPrice.toLocaleString();
            if(document.getElementById('rankPriceS')) document.getElementById('rankPriceS').innerText = '¥' + finalPrice.toLocaleString();
            if(document.getElementById('rankPriceA')) document.getElementById('rankPriceA').innerText = '¥' + Math.round(finalPrice * 0.92).toLocaleString();
            if(document.getElementById('rankPriceB')) document.getElementById('rankPriceB').innerText = '¥' + Math.round(finalPrice * 0.84).toLocaleString();
            if(document.getElementById('rankPriceC')) document.getElementById('rankPriceC').innerText = '¥' + Math.round(finalPrice * 0.76).toLocaleString();
            if(document.getElementById('rankPriceD')) document.getElementById('rankPriceD').innerText = '¥' + Math.round(finalPrice * 0.48).toLocaleString();

            var applyBtn = document.getElementById('applyCtaLink');
            applyBtn.href = "<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>?model=" + encodeURIComponent(rawTitle) + "&storage=" + encodeURIComponent(currentStorage) + "&carrier=" + encodeURIComponent(currentCarrier);
        }

        function changeStorage(storage, price, elem) {
            currentStorage = storage;
            currentBasePrice = price;
            document.getElementById('activeStorageText').innerText = storage;
            var buttons = elem.parentElement.querySelectorAll('.storage-btn');
            buttons.forEach(function(b) {
                b.style.border = '1.5px solid #cbd5e1';
                b.style.background = '#fff';
                b.style.color = '#333';
            });
            elem.style.border = '1.5px solid #ef4444';
            elem.style.background = '#fef2f2';
            elem.style.color = '#dc2626';
            updateSinglePrices();
        }

        function changeCarrier(carrier, diff, elem) {
            currentCarrier = carrier;
            currentCarrierDiff = diff;
            document.getElementById('activeCarrierText').innerText = carrier;
            var buttons = elem.parentElement.querySelectorAll('.carrier-btn');
            buttons.forEach(function(b) {
                b.style.border = '1.5px solid #cbd5e1';
                b.style.background = '#fff';
                b.style.color = '#333';
            });
            elem.style.border = '1.5px solid #ef4444';
            elem.style.background = '#fef2f2';
            elem.style.color = '#dc2626';
            updateSinglePrices();
        }

        function switchDeviceImg(src, elem) {
            document.getElementById('mainProductImg').src = src;
            var thumbs = elem.parentElement.querySelectorAll('.gallery-thumb');
            thumbs.forEach(function(t) { t.style.borderColor = '#e2e8f0'; });
            elem.style.borderColor = '#ef4444';
        }
    </script>

    <?php
    endwhile;
endif;

get_footer();

<?php
/**
 * Template Name: Kaitori Member Registration
 * 
 * Customer Account Registration Page.
 * Supports Elementor page builder.
 *
 * @package KaitoriAkiba
 */

get_header();

// Check if page is built with Elementor
$is_elementor = false;
if ( did_action( 'elementor/loaded' ) ) {
    $document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
    if ( $document && $document->is_built_with_elementor() ) {
        $is_elementor = true;
    }
}

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
    ?>
    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
        <div class="auth-container" style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <div class="auth-header" style="text-align: center; margin-bottom: 28px;">
                <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                    <i class="fa fa-user-plus" style="color:#ef4444;"></i> <?php _e( '新規会員登録 (Member Registration)', 'kaitoriakiba' ); ?>
                </h1>
                <p style="color: #64748b; font-size: 14px; margin:0;">
                    <?php _e( '会員登録を行うと、買取申込み状況のリアルタイム確認や2回目以降の入力省略、会員ランク限定特典がご利用いただけます。', 'kaitoriakiba' ); ?>
                </p>
            </div>

            <?php if ( is_user_logged_in() ) : 
                $current_user = wp_get_current_user();
            ?>
                <div class="alert alert-info text-center" style="border-radius:10px; padding:20px;">
                    <p style="margin:0 0 12px 0;">現在 <strong><?php echo esc_html( $current_user->display_name ); ?></strong> としてログインしています。</p>
                    <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700;">買取申込へ進む &rarr;</a>
                </div>
            <?php else : ?>

            <form id="registerForm" method="post">
                <div class="row">
                    <div class="col-md-6 form-group" style="margin-bottom:18px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>姓 (Last Name)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <input type="text" name="last_name" class="form-control-custom" placeholder="山田" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>
                    <div class="col-md-6 form-group" style="margin-bottom:18px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>名 (First Name)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <input type="text" name="first_name" class="form-control-custom" placeholder="太郎" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>メールアドレス (Email)</span>
                        <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <input type="email" name="email" class="form-control-custom" placeholder="taro@example.com" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>電話番号 (Phone)</span>
                        <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <input type="tel" name="phone" class="form-control-custom" placeholder="09012345678" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>パスワード (Password)</span>
                        <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <input type="password" name="password" id="reg_pwd" class="form-control-custom" placeholder="半角英数8文字以上" required minlength="6" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label style="display:flex; gap:10px; cursor:pointer;">
                        <input type="checkbox" id="reg_terms" required style="margin-top:4px;">
                        <span style="font-size:13px; color:#334155;"><a href="#" style="color:#ef4444; text-decoration:underline;">利用規約</a> および <a href="#" style="color:#ef4444; text-decoration:underline;">プライバシーポリシー</a> に同意して登録します。</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-submit-reg" style="width:100%; height:50px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:10px; font-weight:800; font-size:16px; color:#fff;">
                    <i class="fa fa-user-plus"></i> 無料会員登録を完了する (Register)
                </button>
            </form>

            <div style="text-align:center; margin-top:20px; font-size:13.5px; color:#64748b;">
                すでにアカウントをお持ちですか？ <a href="<?php echo esc_url( wp_login_url() ); ?>" style="color:#ef4444; font-weight:700;">ログインはこちら</a>
            </div>

            <?php endif; ?>

        </div>
    </div>
    <?php
endif;

get_footer();

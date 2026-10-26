<?php
/**
 * Template Name: Register & Login (会員登録・ログイン)
 * 
 * Customer Account Registration & Login Portal.
 * Supports Elementor page builder.
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
    // Detect whether to show Login or Register tab
    global $post;
    $is_login_page = ( isset( $post->post_name ) && $post->post_name === 'login' ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'login' );
    ?>
    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
        <div class="auth-container" style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <!-- Auth Navigation Tabs -->
            <div style="display:flex; border-bottom:2px solid #e2e8f0; margin-bottom:28px;">
                <button type="button" id="tabBtnRegister" onclick="switchAuthTab('register')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login_page ? 'transparent' : '#ef4444'; ?>; color:<?php echo $is_login_page ? '#64748b' : '#ef4444'; ?>; cursor:pointer;">
                    <i class="fa fa-user-plus"></i> <?php _e( '新規会員登録 (Register)', 'kaitoriakiba' ); ?>
                </button>
                <button type="button" id="tabBtnLogin" onclick="switchAuthTab('login')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login_page ? '#ef4444' : 'transparent'; ?>; color:<?php echo $is_login_page ? '#ef4444' : '#64748b'; ?>; cursor:pointer;">
                    <i class="fa fa-sign-in"></i> <?php _e( 'ログイン (Login)', 'kaitoriakiba' ); ?>
                </button>
            </div>

            <?php if ( is_user_logged_in() ) : 
                $current_user = wp_get_current_user();
            ?>
                <div class="alert alert-info text-center" style="border-radius:10px; padding:24px;">
                    <div style="font-size:32px; color:#10b981; margin-bottom:8px;"><i class="fa fa-user-circle"></i></div>
                    <p style="margin:0 0 14px 0; font-size:15px;">現在 <strong><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></strong> 様としてログインしています。</p>
                    <div style="display:flex; gap:10px; justify-content:center;">
                        <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px; padding:10px 20px;">買取申込へ進む &rarr;</a>
                        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-default" style="font-weight:700; border-radius:8px; padding:10px 20px;">ログアウト</a>
                    </div>
                </div>
            <?php else : ?>

                <!-- 1. REGISTER PANEL -->
                <div id="authRegisterPanel" style="display:<?php echo $is_login_page ? 'none' : 'block'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php _e( 'Kaitori Akiba 無料会員登録', 'kaitoriakiba' ); ?>
                        </h2>
                        <p style="color: #64748b; font-size: 13.5px; margin:0;">
                            <?php _e( '会員登録を行うと、次回からの入力省略や買取履歴の確認、会員限定ボーナスがご利用いただけます。', 'kaitoriakiba' ); ?>
                        </p>
                    </div>

                    <form id="registerForm" method="post">
                        <?php if ( function_exists( 'kaitoriakiba_honeypot_fields' ) ) { kaitoriakiba_honeypot_fields(); } ?>
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
                            <input type="password" name="password" id="reg_pwd" class="form-control-custom" placeholder="半角英数8文字以上（英字と数字を含む）" required minlength="8" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
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
                        すでにアカウントをお持ちですか？ <a href="javascript:switchAuthTab('login')" style="color:#ef4444; font-weight:700;">ログインはこちら</a>
                    </div>
                </div>

                <!-- 2. LOGIN PANEL -->
                <div id="authLoginPanel" style="display:<?php echo $is_login_page ? 'block' : 'none'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php _e( '会員ログイン (Login)', 'kaitoriakiba' ); ?>
                        </h2>
                        <p style="color: #64748b; font-size: 13.5px; margin:0;">
                            <?php _e( 'ご登録済みのメールアドレスとパスワードでログインしてください。', 'kaitoriakiba' ); ?>
                        </p>
                    </div>

                    <form id="loginForm" method="post">
                        <?php if ( function_exists( 'kaitoriakiba_honeypot_fields' ) ) { kaitoriakiba_honeypot_fields(); } ?>
                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>メールアドレスまたはユーザー名</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="text" name="log" class="form-control-custom" placeholder="taro@example.com" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>パスワード (Password)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="password" name="pwd" class="form-control-custom" placeholder="パスワードを入力" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#475569; margin:0;">
                                <input type="checkbox" name="rememberme" value="forever"> ログイン状態を保持する
                            </label>
                            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="font-size:12.5px; color:#ef4444; text-decoration:none;">パスワードをお忘れの方</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-submit-login" style="width:100%; height:50px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:10px; font-weight:800; font-size:16px; color:#fff;">
                            <i class="fa fa-sign-in"></i> ログインする (Sign In)
                        </button>
                    </form>

                    <div style="text-align:center; margin-top:20px; font-size:13.5px; color:#64748b;">
                        アカウントをお持ちでないですか？ <a href="javascript:switchAuthTab('register')" style="color:#ef4444; font-weight:700;">新規会員登録はこちら</a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <script>
        function switchAuthTab(tab) {
            var regPanel = document.getElementById('authRegisterPanel');
            var loginPanel = document.getElementById('authLoginPanel');
            var tabReg = document.getElementById('tabBtnRegister');
            var tabLogin = document.getElementById('tabBtnLogin');

            if (!regPanel || !loginPanel) return;

            if (tab === 'login') {
                regPanel.style.display = 'none';
                loginPanel.style.display = 'block';
                tabReg.style.borderBottomColor = 'transparent';
                tabReg.style.color = '#64748b';
                tabLogin.style.borderBottomColor = '#ef4444';
                tabLogin.style.color = '#ef4444';
            } else {
                regPanel.style.display = 'block';
                loginPanel.style.display = 'none';
                tabReg.style.borderBottomColor = '#ef4444';
                tabReg.style.color = '#ef4444';
                tabLogin.style.borderBottomColor = 'transparent';
                tabLogin.style.color = '#64748b';
            }
        }
    </script>
    <?php
endif;

get_footer();

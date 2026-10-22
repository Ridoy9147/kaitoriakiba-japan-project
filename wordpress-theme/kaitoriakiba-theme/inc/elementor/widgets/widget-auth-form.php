<?php
/**
 * Elementor Widget: Register & Login Auth Portal
 * 
 * Allows visual customization of the user registration and login forms
 * directly in Elementor page builder.
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Auth_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_auth_form';
    }

    public function get_title() {
        return __( 'Member Register & Login Portal (会員登録・ログイン)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-lock-user';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Portal Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'default_tab',
            array(
                'label'   => __( 'Default Active Tab', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'register' => 'Registration (新規会員登録)',
                    'login'    => 'Login (ログイン)',
                ),
                'default' => 'register',
            )
        );

        $this->add_control(
            'register_title',
            array(
                'label'   => __( 'Registration Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Kaitori Akiba 無料会員登録',
            )
        );

        $this->add_control(
            'login_title',
            array(
                'label'   => __( 'Login Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '会員ログイン',
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            array(
                'label' => __( 'Container Style', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'container_max_width',
            array(
                'label'      => __( 'Max Width (px)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 400,
                        'max' => 800,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 580,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .auth-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $def_tab = ! empty( $settings['default_tab'] ) ? $settings['default_tab'] : 'register';
        $is_login = ( $def_tab === 'login' ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'login' );
        $reg_title = ! empty( $settings['register_title'] ) ? $settings['register_title'] : __( '新規会員登録 (Register)', 'kaitoriakiba' );
        $log_title = ! empty( $settings['login_title'] ) ? $settings['login_title'] : __( '会員ログイン (Login)', 'kaitoriakiba' );
        ?>
        <div class="auth-container" style="margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <!-- Auth Navigation Tabs -->
            <div style="display:flex; border-bottom:2px solid #e2e8f0; margin-bottom:28px;">
                <button type="button" id="tabBtnRegister" onclick="switchAuthTab('register')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login ? 'transparent' : '#ef4444'; ?>; color:<?php echo $is_login ? '#64748b' : '#ef4444'; ?>; cursor:pointer;">
                    <i class="fa fa-user-plus"></i> <?php _e( '新規会員登録 (Register)', 'kaitoriakiba' ); ?>
                </button>
                <button type="button" id="tabBtnLogin" onclick="switchAuthTab('login')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login ? '#ef4444' : 'transparent'; ?>; color:<?php echo $is_login ? '#ef4444' : '#64748b'; ?>; cursor:pointer;">
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
                <div id="authRegisterPanel" style="display:<?php echo $is_login ? 'none' : 'block'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php echo esc_html( $reg_title ); ?>
                        </h2>
                        <p style="color: #64748b; font-size: 13.5px; margin:0;">
                            <?php _e( '会員登録を行うと、次回からの入力省略や買取履歴の確認、会員限定ボーナスがご利用いただけます。', 'kaitoriakiba' ); ?>
                        </p>
                    </div>

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
                                <span>電話番号 (Phone Number)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="tel" name="phone" class="form-control-custom" placeholder="09012345678" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:24px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>パスワード (Password)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="password" name="password" minlength="8" class="form-control-custom" placeholder="半角英数字8文字以上" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <small style="color:#64748b; font-size:12px;">※8文字以上の半角英数字を組み合わせて入力してください。</small>
                        </div>

                        <button type="submit" id="btnSubmitRegister" class="btn btn-danger btn-block" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-user-plus"></i> 会員登録を完了する
                        </button>
                    </form>
                </div>

                <!-- 2. LOGIN PANEL -->
                <div id="authLoginPanel" style="display:<?php echo $is_login ? 'block' : 'none'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php echo esc_html( $log_title ); ?>
                        </h2>
                        <p style="color: #64748b; font-size: 13.5px; margin:0;">
                            <?php _e( 'ご登録のメールアドレスとパスワードを入力してください。', 'kaitoriakiba' ); ?>
                        </p>
                    </div>

                    <form id="loginForm" method="post">
                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px; display:block;">
                                メールアドレス (Email)
                            </label>
                            <input type="email" name="log" class="form-control-custom" placeholder="example@kaitori-akiba.com" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:20px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                <label class="form-label" style="font-weight:700; font-size:14px; margin:0;">パスワード (Password)</label>
                                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="font-size:12px; color:#ef4444; font-weight:600;">パスワードをお忘れですか？</a>
                            </div>
                            <input type="password" name="pwd" class="form-control-custom" placeholder="パスワード" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:24px;">
                            <label style="font-size:13px; color:#475569; cursor:pointer;">
                                <input type="checkbox" name="rememberme" value="forever"> ログイン状態を保持する
                            </label>
                        </div>

                        <button type="submit" id="btnSubmitLogin" class="btn btn-danger btn-block" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-sign-in"></i> ログインする
                        </button>
                    </form>
                </div>

            <?php endif; ?>
        </div>

        <script>
            function switchAuthTab(tab) {
                var regPanel = document.getElementById('authRegisterPanel');
                var logPanel = document.getElementById('authLoginPanel');
                var btnReg   = document.getElementById('tabBtnRegister');
                var btnLog   = document.getElementById('tabBtnLogin');

                if (tab === 'login') {
                    if (regPanel) regPanel.style.display = 'none';
                    if (logPanel) logPanel.style.display = 'block';
                    if (btnLog) { btnLog.style.borderBottomColor = '#ef4444'; btnLog.style.color = '#ef4444'; }
                    if (btnReg) { btnReg.style.borderBottomColor = 'transparent'; btnReg.style.color = '#64748b'; }
                } else {
                    if (regPanel) regPanel.style.display = 'block';
                    if (logPanel) logPanel.style.display = 'none';
                    if (btnReg) { btnReg.style.borderBottomColor = '#ef4444'; btnReg.style.color = '#ef4444'; }
                    if (btnLog) { btnLog.style.borderBottomColor = 'transparent'; btnLog.style.color = '#64748b'; }
                }
            }
        </script>
        <?php
    }
}

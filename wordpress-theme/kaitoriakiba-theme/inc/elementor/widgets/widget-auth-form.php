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
        // Section: Portal Settings & Tabs
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Portal General Settings', 'kaitoriakiba' ),
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
            'tab_reg_label',
            array(
                'label'   => __( 'Register Tab Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '新規会員登録 (Register)',
            )
        );

        $this->add_control(
            'tab_log_label',
            array(
                'label'   => __( 'Login Tab Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'ログイン (Login)',
            )
        );

        $this->add_control(
            'req_badge_text',
            array(
                'label'   => __( 'Required Badge Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '必須',
            )
        );

        $this->end_controls_section();

        // Section: Registration Form Fields
        $this->start_controls_section(
            'section_reg_fields',
            array(
                'label' => __( 'Registration Form Fields', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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
            'register_subtitle',
            array(
                'label'   => __( 'Registration Subtitle', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '会員登録を行うと、次回からの入力省略や買取履歴の確認、会員限定ボーナスがご利用いただけます。',
            )
        );

        $this->add_control(
            'reg_lastname_label',
            array(
                'label'   => __( 'Last Name Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '姓 (Last Name)',
            )
        );

        $this->add_control(
            'reg_lastname_placeholder',
            array(
                'label'   => __( 'Last Name Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '山田',
            )
        );

        $this->add_control(
            'reg_firstname_label',
            array(
                'label'   => __( 'First Name Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '名 (First Name)',
            )
        );

        $this->add_control(
            'reg_firstname_placeholder',
            array(
                'label'   => __( 'First Name Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '太郎',
            )
        );

        $this->add_control(
            'reg_email_label',
            array(
                'label'   => __( 'Email Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'メールアドレス (Email)',
            )
        );

        $this->add_control(
            'reg_email_placeholder',
            array(
                'label'   => __( 'Email Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'taro@example.com',
            )
        );

        $this->add_control(
            'reg_phone_label',
            array(
                'label'   => __( 'Phone Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '電話番号 (Phone Number)',
            )
        );

        $this->add_control(
            'reg_phone_placeholder',
            array(
                'label'   => __( 'Phone Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '09012345678',
            )
        );

        $this->add_control(
            'reg_password_label',
            array(
                'label'   => __( 'Password Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'パスワード (Password)',
            )
        );

        $this->add_control(
            'reg_password_placeholder',
            array(
                'label'   => __( 'Password Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '半角英数字8文字以上',
            )
        );

        $this->add_control(
            'reg_password_hint',
            array(
                'label'   => __( 'Password Helper Hint', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '※8文字以上の半角英数字を組み合わせて入力してください。',
            )
        );

        $this->add_control(
            'reg_btn_text',
            array(
                'label'   => __( 'Register Submit Button', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '会員登録を完了する',
            )
        );

        $this->end_controls_section();

        // Section: Login Form Fields
        $this->start_controls_section(
            'section_login_fields',
            array(
                'label' => __( 'Login Form Fields', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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

        $this->add_control(
            'login_subtitle',
            array(
                'label'   => __( 'Login Subtitle', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'ご登録のメールアドレスとパスワードを入力してください。',
            )
        );

        $this->add_control(
            'login_email_label',
            array(
                'label'   => __( 'Email Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'メールアドレス (Email)',
            )
        );

        $this->add_control(
            'login_email_placeholder',
            array(
                'label'   => __( 'Email Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'example@kaitori-akiba.com',
            )
        );

        $this->add_control(
            'login_password_label',
            array(
                'label'   => __( 'Password Label', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'パスワード (Password)',
            )
        );

        $this->add_control(
            'login_password_placeholder',
            array(
                'label'   => __( 'Password Placeholder', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'パスワード',
            )
        );

        $this->add_control(
            'login_forgot_text',
            array(
                'label'   => __( 'Forgot Password Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'パスワードをお忘れですか？',
            )
        );

        $this->add_control(
            'login_remember_text',
            array(
                'label'   => __( 'Remember Me Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'ログイン状態を保持する',
            )
        );

        $this->add_control(
            'login_btn_text',
            array(
                'label'   => __( 'Login Submit Button', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'ログインする',
            )
        );

        $this->end_controls_section();

        // Section: Logged-in State
        $this->start_controls_section(
            'section_loggedin_fields',
            array(
                'label' => __( 'Logged-In Status View', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'loggedin_apply_btn',
            array(
                'label'   => __( 'Apply Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '買取申込へ進む →',
            )
        );

        $this->add_control(
            'loggedin_logout_btn',
            array(
                'label'   => __( 'Logout Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'ログアウト',
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

        $def_tab     = ! empty( $settings['default_tab'] ) ? $settings['default_tab'] : 'register';
        $is_login    = ( $def_tab === 'login' ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'login' );
        $tab_reg     = ! empty( $settings['tab_reg_label'] ) ? $settings['tab_reg_label'] : '新規会員登録 (Register)';
        $tab_log     = ! empty( $settings['tab_log_label'] ) ? $settings['tab_log_label'] : 'ログイン (Login)';
        $req_badge   = ! empty( $settings['req_badge_text'] ) ? $settings['req_badge_text'] : '必須';

        $reg_title   = ! empty( $settings['register_title'] ) ? $settings['register_title'] : 'Kaitori Akiba 無料会員登録';
        $reg_sub     = ! empty( $settings['register_subtitle'] ) ? $settings['register_subtitle'] : '会員登録を行うと、次回からの入力省略や買取履歴の確認、会員限定ボーナスがご利用いただけます。';
        $reg_ln_lbl  = ! empty( $settings['reg_lastname_label'] ) ? $settings['reg_lastname_label'] : '姓 (Last Name)';
        $reg_ln_ph   = ! empty( $settings['reg_lastname_placeholder'] ) ? $settings['reg_lastname_placeholder'] : '山田';
        $reg_fn_lbl  = ! empty( $settings['reg_firstname_label'] ) ? $settings['reg_firstname_label'] : '名 (First Name)';
        $reg_fn_ph   = ! empty( $settings['reg_firstname_placeholder'] ) ? $settings['reg_firstname_placeholder'] : '太郎';
        $reg_em_lbl  = ! empty( $settings['reg_email_label'] ) ? $settings['reg_email_label'] : 'メールアドレス (Email)';
        $reg_em_ph   = ! empty( $settings['reg_email_placeholder'] ) ? $settings['reg_email_placeholder'] : 'taro@example.com';
        $reg_ph_lbl  = ! empty( $settings['reg_phone_label'] ) ? $settings['reg_phone_label'] : '電話番号 (Phone Number)';
        $reg_ph_ph   = ! empty( $settings['reg_phone_placeholder'] ) ? $settings['reg_phone_placeholder'] : '09012345678';
        $reg_pw_lbl  = ! empty( $settings['reg_password_label'] ) ? $settings['reg_password_label'] : 'パスワード (Password)';
        $reg_pw_ph   = ! empty( $settings['reg_password_placeholder'] ) ? $settings['reg_password_placeholder'] : '半角英数字8文字以上';
        $reg_pw_hint = ! empty( $settings['reg_password_hint'] ) ? $settings['reg_password_hint'] : '※8文字以上の半角英数字を組み合わせて入力してください。';
        $reg_btn     = ! empty( $settings['reg_btn_text'] ) ? $settings['reg_btn_text'] : '会員登録を完了する';

        $log_title   = ! empty( $settings['login_title'] ) ? $settings['login_title'] : '会員ログイン';
        $log_sub     = ! empty( $settings['login_subtitle'] ) ? $settings['login_subtitle'] : 'ご登録のメールアドレスとパスワードを入力してください。';
        $log_em_lbl  = ! empty( $settings['login_email_label'] ) ? $settings['login_email_label'] : 'メールアドレス (Email)';
        $log_em_ph   = ! empty( $settings['login_email_placeholder'] ) ? $settings['login_email_placeholder'] : 'example@kaitori-akiba.com';
        $log_pw_lbl  = ! empty( $settings['login_password_label'] ) ? $settings['login_password_label'] : 'パスワード (Password)';
        $log_pw_ph   = ! empty( $settings['login_password_placeholder'] ) ? $settings['login_password_placeholder'] : 'パスワード';
        $log_forgot  = ! empty( $settings['login_forgot_text'] ) ? $settings['login_forgot_text'] : 'パスワードをお忘れですか？';
        $log_rem     = ! empty( $settings['login_remember_text'] ) ? $settings['login_remember_text'] : 'ログイン状態を保持する';
        $log_btn     = ! empty( $settings['login_btn_text'] ) ? $settings['login_btn_text'] : 'ログインする';

        $apply_btn   = ! empty( $settings['loggedin_apply_btn'] ) ? $settings['loggedin_apply_btn'] : '買取申込へ進む →';
        $logout_btn  = ! empty( $settings['loggedin_logout_btn'] ) ? $settings['loggedin_logout_btn'] : 'ログアウト';
        ?>
        <div class="auth-container" style="margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <!-- Auth Navigation Tabs -->
            <div style="display:flex; border-bottom:2px solid #e2e8f0; margin-bottom:28px;">
                <button type="button" id="tabBtnRegister" onclick="switchAuthTab('register')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login ? 'transparent' : '#ef4444'; ?>; color:<?php echo $is_login ? '#64748b' : '#ef4444'; ?>; cursor:pointer;">
                    <i class="fa fa-user-plus"></i> <?php echo esc_html( $tab_reg ); ?>
                </button>
                <button type="button" id="tabBtnLogin" onclick="switchAuthTab('login')" style="flex:1; padding:12px; font-weight:800; font-size:15px; border:none; background:transparent; border-bottom:3px solid <?php echo $is_login ? '#ef4444' : 'transparent'; ?>; color:<?php echo $is_login ? '#ef4444' : '#64748b'; ?>; cursor:pointer;">
                    <i class="fa fa-sign-in"></i> <?php echo esc_html( $tab_log ); ?>
                </button>
            </div>

            <?php if ( is_user_logged_in() ) : 
                $current_user = wp_get_current_user();
            ?>
                <div class="alert alert-info text-center" style="border-radius:10px; padding:24px;">
                    <div style="font-size:32px; color:#10b981; margin-bottom:8px;"><i class="fa fa-user-circle"></i></div>
                    <p style="margin:0 0 14px 0; font-size:15px;">現在 <strong><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></strong> 様としてログインしています。</p>
                    <div style="display:flex; gap:10px; justify-content:center;">
                        <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px; padding:10px 20px;"><?php echo esc_html( $apply_btn ); ?></a>
                        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-default" style="font-weight:700; border-radius:8px; padding:10px 20px;"><?php echo esc_html( $logout_btn ); ?></a>
                    </div>
                </div>
            <?php else : ?>

                <!-- 1. REGISTER PANEL -->
                <div id="authRegisterPanel" style="display:<?php echo $is_login ? 'none' : 'block'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php echo esc_html( $reg_title ); ?>
                        </h2>
                        <?php if ( ! empty( $reg_sub ) ) : ?>
                            <p style="color: #64748b; font-size: 13.5px; margin:0;">
                                <?php echo esc_html( $reg_sub ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <form id="registerForm" method="post">
                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:18px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                    <span><?php echo esc_html( $reg_ln_lbl ); ?></span>
                                    <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php echo esc_html( $req_badge ); ?></span>
                                </label>
                                <input type="text" name="last_name" class="form-control-custom" placeholder="<?php echo esc_attr( $reg_ln_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:18px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                    <span><?php echo esc_html( $reg_fn_lbl ); ?></span>
                                    <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php echo esc_html( $req_badge ); ?></span>
                                </label>
                                <input type="text" name="first_name" class="form-control-custom" placeholder="<?php echo esc_attr( $reg_fn_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php echo esc_html( $reg_em_lbl ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php echo esc_html( $req_badge ); ?></span>
                            </label>
                            <input type="email" name="email" class="form-control-custom" placeholder="<?php echo esc_attr( $reg_em_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php echo esc_html( $reg_ph_lbl ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php echo esc_html( $req_badge ); ?></span>
                            </label>
                            <input type="tel" name="phone" class="form-control-custom" placeholder="<?php echo esc_attr( $reg_ph_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:24px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php echo esc_html( $reg_pw_lbl ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php echo esc_html( $req_badge ); ?></span>
                            </label>
                            <input type="password" name="password" minlength="8" class="form-control-custom" placeholder="<?php echo esc_attr( $reg_pw_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <?php if ( ! empty( $reg_pw_hint ) ) : ?>
                                <small style="color:#64748b; font-size:12px; display:block; margin-top:4px;"><?php echo esc_html( $reg_pw_hint ); ?></small>
                            <?php endif; ?>
                        </div>

                        <button type="submit" id="btnSubmitRegister" class="btn btn-danger btn-block" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-user-plus"></i> <?php echo esc_html( $reg_btn ); ?>
                        </button>
                    </form>
                </div>

                <!-- 2. LOGIN PANEL -->
                <div id="authLoginPanel" style="display:<?php echo $is_login ? 'block' : 'none'; ?>;">
                    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                            <?php echo esc_html( $log_title ); ?>
                        </h2>
                        <?php if ( ! empty( $log_sub ) ) : ?>
                            <p style="color: #64748b; font-size: 13.5px; margin:0;">
                                <?php echo esc_html( $log_sub ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <form id="loginForm" method="post">
                        <div class="form-group" style="margin-bottom:18px;">
                            <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px; display:block;">
                                <?php echo esc_html( $log_em_lbl ); ?>
                            </label>
                            <input type="email" name="log" class="form-control-custom" placeholder="<?php echo esc_attr( $log_em_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:20px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                <label class="form-label" style="font-weight:700; font-size:14px; margin:0;"><?php echo esc_html( $log_pw_lbl ); ?></label>
                                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="font-size:12px; color:#ef4444; font-weight:600;"><?php echo esc_html( $log_forgot ); ?></a>
                            </div>
                            <input type="password" name="pwd" class="form-control-custom" placeholder="<?php echo esc_attr( $log_pw_ph ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>

                        <div class="form-group" style="margin-bottom:24px;">
                            <label style="font-size:13px; color:#475569; cursor:pointer;">
                                <input type="checkbox" name="rememberme" value="forever"> <?php echo esc_html( $log_rem ); ?>
                            </label>
                        </div>

                        <button type="submit" id="btnSubmitLogin" class="btn btn-danger btn-block" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-sign-in"></i> <?php echo esc_html( $log_btn ); ?>
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

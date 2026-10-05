<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<!-- <link rel="canonical" href="https://kaitoriakiba.jp/" hreflang="ja"/> -->
    <title>iPhone &amp; Smartphone Buyback Specialist [Kaitori Akiba] Tokyo - Akihabara</title>
    <meta name="robots" content="index,follow">
	<meta name="google-site-verification" content="rfQ2yyh7wNSlcVkXwRRtDBPgfA7lDx4VvisuXr6y-Bg" />
	<meta http-equiv="Cache-Control" content="private,max-age=86400" />
    <meta name="description" content="iPhone &amp; Android, smartphones, unlocked devices, carrier phones (docomo, au, SoftBank). Instant cash in-store, nationwide mail-in buyback. Best price guarantee at Kaitori Akiba Tokyo!">
    <meta name="keywords" content="iPhone buyback, Android buyback, iPad buyback, smartphone buyback, smartphone trade-in, mobile phone buyback, docomo buyback, au buyback, SoftBank buyback, SIM free, Kaitori Akiba, Tokyo phone buyback, Akihabara buyback">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <meta name="format-detection" content="telephone=no, email=no">
    
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/base.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/swiper3.1.0.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/custom-modern.css">
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery-2.2.0.min.js" type="text/javascript"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery-ui.min.js" type="text/javascript"></script>
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5shiv.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/respond.min.js"></script>
    <![endif]-->
	<!-- GTM disabled for local dev -->

<!-- Google tag disabled for local dev -->

<!--<script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
        d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
        $.src="https://v2.zopim.com/?5i1IoEnEtuUSED2vPFF5dx9IJn0YpgnR";z.t=+new Date;$.
            type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<script>
	var zopim = {
		init: function(){
			$zopim(function(){
					$zopim.livechat.button.hide();	
			})
		}
	}
	zopim.init();	
</script>-->

	<style type="text/css">
		.dropdownbutton{
			width: 100%;
			height: 40px;
			text-align: center;
		}
		.dropdown{
			color: #333;
			background-color: #fab0b0;
			padding:0px 12px;
			height: 26px;
			margin: 0 auto;
			border: none;
			border-radius: 3px;
		}

		.dropdown:hover{
			background-color: ed9c9c;
			color: #fff;
		}
		.qingbao{
			height: 200px;
			overflow: hidden;
		}
	</style>
	<script>
		function qbopen(){
			if($('#qb').hasClass('qingbao')){
				$("#qb").removeClass('qingbao');
				$("#moreless").text('Show Less')
			}else{
				$("#qb").addClass('qingbao');
				$("#moreless").text('Show More')
			}
		}
	</script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!-- ================================================================ -->
<!-- WORDPRESS THEME TEMPLATE: header.php starts here                  -->
<!-- ================================================================ -->
<!--header-s-->
<div class="top-utility-bar">
    <div class="container">
        <div class="top-bar-inner">
            <div class="top-bar-notice">
                <span class="badge-official">Official Buyback Portal</span>
                <span class="notice-text hidden-xs">iPhone, Smartphone &amp; Mobile Phone Specialist [Tokyo Akihabara]</span>
            </div>
            <ul class="top-nav-links">
                <li><a href="<?php echo home_url('/sell-apply/'); ?>" class="link-highlight"><i class="fa fa-shopping-cart"></i> iPhone Buyback</a></li>
                <li><a href="<?php echo home_url('/kyc-verify/'); ?>"><i class="fa fa-id-card-o"></i> eKYC Verify</a></li>
                <li><a href="<?php echo home_url('/register/'); ?>"><i class="fa fa-user-plus"></i> Register</a></li>
                <li><a href="<?php echo home_url('/login/'); ?>"><i class="fa fa-sign-in"></i> Login</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<header class="site-header-modern">
    <div class="container">
        <div class="header-main-row">
            <div class="header-brand">
            	<a href="<?php echo home_url('/'); ?>" class="brand-link">
                    <img src="<?php echo get_template_directory_uri(); ?>/upload/logo/logo.svg" title="Kaitori Akiba - iPhone &amp; Smartphone Buyback Tokyo Akihabara" alt="Kaitori Akiba" class="brand-logo-img" />
                </a>
            </div>
            <div class="header-actions">
                <a href="<?php echo home_url('/sell-apply/'); ?>" class="header-cta-btn">
                    <i class="fa fa-paper-plane"></i>
                    <span>Apply for Buyback</span>
                </a>
                <a href="<?php echo home_url('/cart/'); ?>" class="header-cart-badge">
                    <i class="fa fa-shopping-bag"></i>
                    <span class="cart-label">Cart</span>
                    <em id="cart_quantity">0</em>
                </a>
                <div class="header-contact hidden-xs">
                    <a href="<?php echo home_url('/qa/shop-access'); ?>" class="shop-info-link">
                        <i class="fa fa-map-marker"></i>
                        <div class="shop-info-text">
                            <span class="shop-title">Akihabara Store</span>
                            <span class="shop-sub">Open 10:00 - 19:30</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="navbar-header visible-xs">
		<div class="header-menu ">
			<ul>
				<li class="btn-dt"><a href="javascript:void(0)">Search</a></li>
				<li class="btn-yj"><a href="javascript:void(0)">Menu</a></li>
				<li class="btn-fr"><a href="javascript:void(0)">Account</a></li>
				<!--<li><a class="liveChat" href="javascript:void($zopim.livechat.window.openPopout())"></a></li>-->
			</ul>
			<a href="/cart/"><em id="mobile_cart_quantity">0</em></a>
		</div>
	</div>
	<div class="navbar-box visible-xs">
		<div class="fiexBox-item">
			<div class="container search collapse in mtop0"> 
				<div class="row">
					<div class="col-lg-12 col-mg-12 col-sm-12">
						<div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
							<div class="search_title">
								<div class="search_title_h2">Search by device model name or model number!</div>
								<div class="search_title_h3">Please enter device name, model number, JAN code, etc.</div>					
							</div>
							<div class="search_box">
								<form class="m_fullsearch" method="get" action="/search#searchtop">
									<div class="form-group mbtm0 clearfix">
										<div class="input-group keyword_search">
											<select class="form-control" name="type" class="fw_cat">
												<option value="">All Categories</option>
																									<option value="4">Smartphones</option>
																									<option value="15">Tablets</option>
																									<option value="34">Gaming Consoles</option>
																									<option value="8">Cosmetics</option>
																									<option value="35">Home Appliances</option>
																									<option value="32">Cameras</option>
																									<option value="17">PC &amp; Laptops</option>
																									<option value="36">Gift Cards &amp; Tickets</option>
																									<option value="16">Kaitori Akiba Specials</option>
																							</select>
											<input type="text" class="form-control keyword_search_text ui-autocomplete-input q" name="q" placeholder="Keyword search (2+ characters)" required="required" autocomplete="off">
											<button class="btn search_word" type="submit" >Search <i class="fa fa-search" aria-hidden="true"></i></button>
										</div>
									</div>
								</form>
						        <div class="title">Popular Search Keywords!
							        <ul class="hotword">
							        								        		<li>
							        			<a href="javascript:void(0)">iphone</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">Nintendo switch</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">Pixel</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">iphone 18</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">iPhone 17</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">IPhone 16</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">OPPO</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">airpods</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">iPhone 15</a>
							        		</li>
							        								        		<li>
							        			<a href="javascript:void(0)">Google Pixel 7</a>
							        		</li>
							        								        </ul>
						    	</div>
							</div>
						</div>
						<div class="col-lg-6 col-mg-12 col-sm-12 col-xm-12">
							<div class="search_title">
								<div class="search_title_h2">Step-by-Step Category Search</div>
								<div class="search_title_h3">Select one or more categories (Multiple selection supported)</div>					
							</div>
							<div class="search_box">
								<form class="snssearch" method="get" action="/search#searchtop">
									<div class="form-group">
										<div class="input-group step_search">
											<select class="form-control s_cat" name="cat">
											<option value="">Select Category</option>
																							<option value="2">iPhone</option>
																							<option value="9">Smartphones</option>
																							<option value="998">Accessories</option>
																							<option value="1041">Mobile Routers</option>
																					</select>
											<i class="fa fa-plus  hidden-xs" aria-hidden="true"></i><i class="fa fa-plus visible-xs" aria-hidden="true"></i>
											<select class="form-control s_career" name="s_career" >
												<option value="">By Carrier</option>
																									<option value="1" >docomo</option>
																									<option value="2" >au</option>
																									<option value="3" >SoftBank</option>
																									<option value="5" >Y!mobile</option>
																									<option value="7" >UQ mobile</option>
																									<option value="10" >SIM FREE</option>
																									<option value="21" >RakutenMobile</option>
																							</select>
											<i class="fa fa-plus  hidden-xs" aria-hidden="true"></i><i class="fa fa-plus visible-xs" aria-hidden="true"></i>
											<select class="form-control s_brand" name="brand">
												<option value="">By Brand</option>
																									<option value="1" >Apple</option>
																									<option value="981" >Xiaomi</option>
																									<option value="866" >OPPO</option>
																									<option value="2" >ASUS</option>
																									<option value="4" >FUJITSU</option>
																									<option value="14" >SAMSUNG</option>
																									<option value="5" >Google</option>
																									<option value="6" >HUAWEI</option>
																									<option value="8" >KYOCERA</option>
																									<option value="11" >MOTOROLA</option>
																									<option value="353" >SHARP</option>
																									<option value="354" >SONY</option>
																							</select>
											<i class="fa fa-plus  hidden-xs" aria-hidden="true"></i><i class="fa fa-plus visible-xs" aria-hidden="true"></i>
											<select class="form-control s_series" name="series">
												<option value="">By Series</option>
																									<option value="1" >iPhone</option>
																									<option value="2" >Xperia</option>
																									<option value="3" >Galaxy</option>
																									<option value="4" >arrows</option>
																									<option value="5" >Sweety</option>
																									<option value="6" >STREAM</option>
																									<option value="7" >Zenfone</option>
																									<option value="8">Simple Smartphone</option>
																									<option value="9" >AQUOS</option>
																									<option value="10" >RAIJIN</option>
																									<option value="11">Boss Den</option>
																									<option value="21" >SIRIUS</option>
																									<option value="22" >Nexus</option>
																									<option value="23" >Qua phone</option>
																									<option value="29" >VEGA</option>
																									<option value="30" >Android One</option>
																									<option value="31" >G'zOne</option>
																									<option value="43" >dynapocket</option>
																									<option value="45" >Ascend</option>
																							</select>
											<button class="btn" type="submit">Search <i class="fa fa-search" aria-hidden="true"></i></button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bg-white">
			    <div class="href_list">
			        <div class="container">

			            <div class="gutter-10 clearflex">
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kaitoriakiba.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/phone.jpg" alt="Smartphones" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://ipadkaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/ipad.jpg" alt="Tablets" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://gamekaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/game.jpg" alt="Gaming Consoles" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://cosmekaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/meizhuang.jpg" alt="Cosmetics" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kadenkaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171113.jpg" alt="Home Appliances" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://camerakaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171104.jpg" alt="Cameras" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://pckaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628170919.jpg" alt="PC &amp; Laptops" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kaitoriakiba.jp/" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180712/kaitoriakiba_link.jpg" alt="kaitoriakiba" class="img-responsive" />
									</a>
								</div>
										            </div>           

			        </div>
			    </div>				
			</div>
		</div>

		<div class="fiexBox-item">

			<div class="navigation-ad">
				<ul class="gutter-0 img-margin">
					<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
						<a href="/start/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_top_1.jpg" alt="First-Time Guide" class="img-responsive"></a>
					</li>
					<li class="free_service">
			            <div class="consult">
	<div class="consult_header">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_01.jpg" width="100%" /> 
	</div>
	<div class="content">
		<!--<img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_02.jpg" width="100%" />--> <a href="/contact/free-personal/"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_03.jpg" width="100%" /> </a> <!--<a href="/contact/free-personal/"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_04.jpg" width="100%" /> </a> <a href="/contact/free-personal/"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_05.jpg" width="100%" /> </a> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_06.jpg" width="100%" /> <a href="/line_guidance/"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_07.jpg" width="100%" /> </a> <a href="/wechat_guidance/"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_08.jpg" width="100%" /> 
		<div class="wechat_qr">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_09.jpg" width="75%" /> 
		</div>
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/nav_consult_10.jpg" width="100%" /> </a> -->
	</div>
</div>					</li>
											<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
							<a href="/member_rank" >
							<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20210621/member_rank02.png" alt="Kaitori Akiba - Member Benefits &amp; Rewards" class="img-responsive col-center poster"></a>
						</li>
											<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
							<a href="/series/iphone/" >
							<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180508/left_iphone.jpg" alt="iphone Buyback Rates" class="img-responsive col-center poster"></a>
						</li>
											<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
							<a href="/series/xperia/" >
							<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180508/left_xperia.jpg" alt="Xperia Buyback Price Chart" class="img-responsive col-center poster"></a>
						</li>
											<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
							<a href="/series/aquos/" >
							<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180508/left_aquos.jpg" alt="aquos Buyback Rates" class="img-responsive col-center poster"></a>
						</li>
											<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
							<a href="/series/galaxy/" >
							<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180508/left_galaxy.jpg" alt="galaxy Buyback Rates" class="img-responsive col-center poster"></a>
						</li>
									</ul>
				<div class="clearfix"></div>
			</div>

			<div class="navigation-box">
	<div class="navigation-h2">
		<a href="/career/"><i class="fa fa-circle"><span>Browse by Carrier</span></i></a>
	</div>
	<ul>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/docomo/" title="docomo"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/docomo.jpg" alt="docomo" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/au/" title="au"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/au.jpg" alt="au" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/softbank/" title="SoftBank"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/softbank.jpg" alt="SoftBank" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/Y!mobile/" title="Y!mobile"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/Y!mobile.jpg" alt="Y!mobile" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/uqmobile/" title="UQ mobile"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/uqmobile.jpg" alt="UQ mobile" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/simfree/" title="SIM FREE"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/simfree.jpg" alt="SIM FREE" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/career/RakutenMobile/" title="RakutenMobile"><img src="<?php echo get_template_directory_uri(); ?>/upload/career/20220526/楽天.jpg" alt="Rakuten Mobile" class="img-responsive mcenter"></a>
			</li>
			</ul>
	<div class="clearfix"></div>
</div>
<div class="navigation-box">
	<div class="navigation-h2">
		<a href="/brand/"><i class="fa fa-circle"><span>Browse by Brand</span></i></a>
	</div>
	<ul class="quick_side">
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/apple/" title="Apple"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/apple.jpg" alt="Apple" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/Xiaomi/" title="Xiaomi"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/20200603/3.png" alt="Xiaomi" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/OPPO/" title="OPPO"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/OPPO.jpg" alt="OPPO" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/asus/" title="ASUS"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/asus.jpg" alt="ASUS" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/fujitsu/" title="FUJITSU"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/fujitsu.jpg" alt="FUJITSU" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/samsung/" title="SAMSUNG"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/samsung.jpg" alt="SAMSUNG" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/google/" title="Google"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/google.jpg" alt="Google" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/huawei/" title="HUAWEI"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/huawei.jpg" alt="HUAWEI" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/kyocera/" title="KYOCERA"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/kyocera.jpg" alt="KYOCERA" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/motorola/" title="MOTOROLA"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/moto.jpg" alt="MOTOROLA" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/sharp/" title="SHARP"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/sharp.jpg" alt="SHARP" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/brand/sony/" title="SONY"><img src="<?php echo get_template_directory_uri(); ?>/upload/brand/sony.jpg" alt="SONY" class="img-responsive mcenter"></a>
			</li>
			</ul>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 btn-more"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/more_open.jpg" alt="Open" class="img-responsive mcenter"></div>
	<div class="clearfix"></div>
</div>
<div class="navigation-box">
	<div class="navigation-h2">
		<a href="/series/"><i class="fa fa-circle"><span>Browse by Series</span></i></a>
	</div>
	<ul class="quick_side">
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/iphone/" title="iPhone"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/iphone.jpg" alt="iPhone" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/xperia/" title="Xperia"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/xperia.jpg" alt="Xperia" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/galaxy/" title="Galaxy"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/galaxy.jpg" alt="Galaxy" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/arrows/" title="arrows"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/arrows.jpg" alt="arrows" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/sweety/" title="Sweety"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/sweety.jpg" alt="Sweety" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/stream/" title="STREAM"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/stream.jpg" alt="STREAM" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/zenfone/" title="Zenfone"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/zenfone.jpg" alt="Zenfone" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/simple-smartphone/" title="Simple Smartphone"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/simple-smartphone.jpg" alt="Simple Smartphone" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/aquos/" title="AQUOS"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/aquos.jpg" alt="AQUOS" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/raijin/" title="RAIJIN"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/raijin.jpg" alt="RAIJIN" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/bossden/" title="Boss Den"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/bossden.jpg" alt="Boss Den" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/sirius/" title="SIRIUS"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/sirius.jpg" alt="SIRIUS" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/nexus/" title="Nexus"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/nexus.jpg" alt="Nexus" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/qua-phone/" title="Qua phone"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/qua-phone.jpg" alt="Qua phone" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/vega/" title="VEGA"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/vega.jpg" alt="VEGA" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/android-one/" title="Android One"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/android-one.jpg" alt="Android One" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/gzone/" title="G'zOne"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/gzone.jpg" alt="G'zOne" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/dynapocket/" title="dynapocket"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/dynapocket.jpg" alt="dynapocket" class="img-responsive mcenter"></a>
			</li>
					<li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
				<a href="/series/ascend/" title="Ascend"><img src="<?php echo get_template_directory_uri(); ?>/upload/series/ascend.jpg" alt="Ascend" class="img-responsive mcenter"></a>
			</li>
			</ul>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 btn-more"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/more_open.jpg" alt="Open" class="img-responsive mcenter"></div>
	<div class="clearfix"></div>
</div>

			<div class="navigation-box2">
				<div class="navigation-h2"><i class="fa fa-circle"><span>Menu</span></i></div>
				<ul class="navigation-h3">
											<li>
															<a href="/start/">
								    <i class="fa fa-caret-right"></i><span>First-Time Guide</span>
								</a>
													</li>
											<li>
															<a href="/shop-purchase-flow/">
								    <i class="fa fa-caret-right"></i><span>In-Store Buyback Flow</span>
								</a>
													</li>
											<li>
															<a href="/delivery-purchase-flow/">
								    <i class="fa fa-caret-right"></i><span>Mail-in Buyback Flow</span>
								</a>
													</li>
											<li>
															<a href="/corporation-purchase-flow/">
								    <i class="fa fa-caret-right"></i><span>Corporate Buyback Flow</span>
								</a>
													</li>
											<li>
															<a href="/qa/shop-access/">
									<i class="fa fa-caret-right"></i><span>Store Location &amp; Access</span>
								</a>
														</li>
										<li>
						<a href="/qa/"><i class="fa fa-caret-right"></i><span>FAQ </span></a>
					</li>
					<li>
						<a href="/contact/personal/"><i class="fa fa-caret-right"></i><span>Contact Us (Individual) </span></a>
					</li>
					<li>
						<a href="/contact/business/"><i class="fa fa-caret-right"></i><span>Contact Us (Corporate) </span></a>
					</li>
				</ul>
			</div>

			<div class="navigation-ad nad_2">
				<ul class="gutter-0 img-margin">
					<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
						<a href="/contact/business/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_bottom_1.jpg" alt="Contact Us (Corporate)" class="img-responsive"></a>
					</li>
					<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-right">
						<a href="/contact/personal/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_bottom_2.jpg" alt="Contact Us (Individual)" class="img-responsive"></a>
					</li>
				<!--	<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left">
						<a href="<?php echo get_template_directory_uri(); ?>/upload/attached/pdf/purchase-consent.pdf" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_bottom_3.jpg" alt="Download Buyback Consent Form" class="img-responsive"></a>
					</li>-->
					<li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-right">
						<a href="<?php echo get_template_directory_uri(); ?>/upload/attached/pdf/doisho.pdf" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/navigation/ad_bottom_4.jpg" alt="Download Parental Consent Form (Under 18)" class="img-responsive"></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
            
            <!--<div style="margin-top:10px; width:100%; padding-bottom:0px;" class="visible-xs">
                <a class="twitter-timeline" data-width="100%" data-height="500" href="https://twitter.com/kaitoriakiba?ref_src=twsrc%5Etfw">Tweets by kaitoriakiba</a> 
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>-->
            
			<div class="bg-white">
			    <div class="href_list">
			        <div class="container">

			            <div class="gutter-10 clearflex">
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kaitoriakiba.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/phone.jpg" alt="Smartphones" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://ipadkaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/ipad.jpg" alt="Tablets" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://gamekaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/game.jpg" alt="Gaming Consoles" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://cosmekaitori.jp" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/meizhuang.jpg" alt="Cosmetics" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kadenkaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171113.jpg" alt="Home Appliances" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://camerakaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171104.jpg" alt="Cameras" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://pckaitori.tokyo" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628170919.jpg" alt="PC &amp; Laptops" class="img-responsive" />
									</a>
								</div>
															<div class="href_item mtop10 col-xs-6 col-sm-3">
									<a href="https://kaitoriakiba.jp/" target="_blank">
									<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180712/kaitoriakiba_link.jpg" alt="kaitoriakiba" class="img-responsive" />
									</a>
								</div>
										            </div>           

			        </div>
			    </div>				
			</div>
		</div>
		<div class="fiexBox-item">
							<div class="container mtop15">
					<div class="row register-tab">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="sub-title">
									<h2>Member Login</h2>
									<div class="clearfix"></div>
								</div>
								<div class="row mtop20">
									<section class="login_form">
										<form id="mLoginForm" method="post" class="form-horizontal" action="">
											<div class="col-lg-4 col-md-4 col-sm-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
												<div class="form-group">
													<label class="col-lg-12 col-md-12 col-sm-12 control-label">Email Address <i class="hidden-xs">Required</i></label>
													<div class="col-lg-12 col-md-12 col-sm-12">
														<input type="email" required="required" class="form-control" name="username" placeholder="abc@abc.com" />
													</div>
												</div>
												<div class="form-group">
													<label class="col-lg-12 col-md-12 col-sm-12 control-label">Password <i class="hidden-xs">Required</i></label>
													<div class="col-lg-12 col-md-12 col-sm-12">
														<input type="password" required="required" class="form-control" name="password" autocomplete="off" />
													</div>
												</div>
												<input type="hidden" name="referurl" value="" />
												<div class="text-center mtop30 mbtm20">
													<button class="btn-ys block-btn login_btn">Log In</button>
												</div>
												<p>※ <a href="/forget-pwd">Forgot your password? Click here</a></p>
												<p>※ <a href="/register">New user? Register an account here</a></p>
											</div>
										</form>
									</section>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="bg-white">
				    <div class="href_list">
				        <div class="container">

				            <div class="gutter-10 clearflex">
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://kaitoriakiba.jp" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/phone.jpg" alt="Smartphones" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://ipadkaitori.jp" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/ipad.jpg" alt="Tablets" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://gamekaitori.jp" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/game.jpg" alt="Gaming Consoles" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://cosmekaitori.jp" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180512/meizhuang.jpg" alt="Cosmetics" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://kadenkaitori.tokyo" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171113.jpg" alt="Home Appliances" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://camerakaitori.tokyo" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628171104.jpg" alt="Cameras" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://pckaitori.tokyo" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180702/20180628170919.jpg" alt="PC &amp; Laptops" class="img-responsive" />
										</a>
									</div>
																	<div class="href_item mtop10 col-xs-6 col-sm-3">
										<a href="https://kaitoriakiba.jp/" target="_blank">
										<img src="<?php echo get_template_directory_uri(); ?>/upload/ad/20180712/kaitoriakiba_link.jpg" alt="kaitoriakiba" class="img-responsive" />
										</a>
									</div>
												            </div>           

				        </div>
				    </div>					
				</div>
					</div>
	</div>

<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrapValidator.min.css">
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.jsonp.js"></script>
<script type="text/javascript">
	var cache = null;
	$(".header-menu ul").on("click","li",function(){
		if($(this).index() === cache){
			$(".header-menu ul li").eq($(this).index()).toggleClass("active");
			$(".navbar-box .fiexBox-item").eq($(this).index()).toggleClass("show");
		}else{
			$(".header-menu ul li").siblings().removeClass('active').eq($(this).index()).toggleClass("active");
			$(".navbar-box .fiexBox-item").siblings().removeClass("show").eq($(this).index()).addClass('show');
			cache = $(this).index();
		}
	})
	$(document).ready(function() {
		$("#mLoginForm").bootstrapValidator({
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					
				}
			})
			.on('success.form.bv', function(e) {
				e.preventDefault();
				var $form = $(e.target),
					validator = $form.data('bootstrapValidator');
				$.ajax({
					type: "POST",
					url: "/home/user/do_login",
					dataType: "json",
					data: $('#mLoginForm').serialize(),
					success: function(data) {
						if(data.status == 1) {
							layer.msg(data.msg, {
								icon: 1,
								time:3000
							}, function() {
								if(data.url)
								{
									window.location.href = data.url;
								}
								else
								{
									window.location.href = "/";
								}
							});
						} else {
							layer.alert(data.msg, {
								icon: 2
							}, function(index) {
								layer.close(index);
							});
						}
					}
				});
			})
            .on('error.form.bv', function(e) {
                layer.msg('There is an error in your input. Please review and try again.', {
                    icon: 2,
                    time:2000
                });
            });
	});

</script>

<!--header-e-->
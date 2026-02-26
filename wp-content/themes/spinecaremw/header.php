<?php 
    global $post;
	if (isset($post)) {
    	$slug = $post->post_name;
	} else {
		$slug = '';
		$section = 'error-404';
	}
	$ancestors = get_post_ancestors($post);
	if (count($ancestors) > 0) {
		$section = get_post_field('post_name', $ancestors[count($ancestors)-1]) . ' ';
	} else {
		if (!isset($section)) {
			if (get_post_type() != 'page') {
				$section = get_post_type() . ' ';
			} else {
				$section = '';
			}
		}
	}
	if ($section == 'provider ') {
		$blocks = get_field('content_blocks');
		// echo('Location: ' . get_site_url() . '/about-nura/our-team/');
		foreach ($blocks as $block) {
			if ($block['acf_fc_layout'] == 'block_bio' && strlen($block['bio_content']) == 0) {
				header('Location: ' . get_site_url() . '/about-nura/our-team/');
			}
		}
	}
	if ($GLOBALS['leadattribution']) {
		require(get_template_directory() . '/partials/admin/lead_attribution.php');
		$attribution = ' data-campaign="' . $campaign . '"';
		$attribution .= ' data-source="' . $source . '"';
		$attribution .= ' data-medium="' . $medium . '"';
		$attribution .= ' data-attrsrc="' . $attrsrc . '"';
	}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php 
	$dev = false;
	if (isset($GLOBALS['produrl'])) {
		$produrl = $GLOBALS['produrl']; 
		$server = $_SERVER['SERVER_NAME'];
		if (strpos($server, $produrl) === false) {
			$dev = true;
		}
	}
	$blocks = get_field('content_blocks');
	if ($blocks) {
		$block0type = $blocks[0]['acf_fc_layout'];
		$block0bkgnd = str_replace('|', ' ', $blocks[0][str_replace('block_', '', $blocks[0]['acf_fc_layout']) . '_settings']['background']);
	} else {
		$block0type = 'block_text';
		$block0bkgnd = 'bkgnd-white light';
	}
?>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset='<?php bloginfo('charset'); ?>' />
    <title><?php wp_title(''); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-5X665TK');</script>
	<!-- End Google Tag Manager -->
    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="https://ondemand.viewmedica.com/lib/vm.js" defer></script>
	<link rel="apple-touch-icon" sizes="57x57" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php bloginfo('stylesheet_directory'); ?>/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php bloginfo('stylesheet_directory'); ?>/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?php bloginfo('stylesheet_directory'); ?>/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

	<?php global $post;

	if (isset($post) && $post->post_name == 'request-an-apointment-confirmation') { ?>

		<!-- Code for Pixel: Nura Clinic Conversion Pixel -->
		<!-- Begin DSP Conversion Action Tracking Code Version 9 -->
		<script type='text/javascript'>
		(function() {
		    var w = window, d = document;
		    var s = d.createElement('script');
		    s.setAttribute('async', 'true');
		    s.setAttribute('type', 'text/javascript');
		    s.setAttribute('src', '//c1.rfihub.net/js/tc.min.js');
		    var f = d.getElementsByTagName('script')[0];
		    f.parentNode.insertBefore(s, f);
		    if (typeof w['_rfi'] !== 'function') {
		        w['_rfi']=function() {
		            w['_rfi'].commands = w['_rfi'].commands || [];
		            w['_rfi'].commands.push(arguments);
		        };
		    }
		    _rfi('setArgs', 'ver', '9');
		    _rfi('setArgs', 'rb', '46371');
		    _rfi('setArgs', 'ca', '20841177');
		    _rfi('setArgs', '_o', '46371');
		    _rfi('setArgs', '_t', '20841177');
		    _rfi('track');
		})();
		</script>
		<noscript>
		<iframe src='//20841177p.rfihub.com/ca.html?rb=46371&ca=20841177&_o=46371&_t=20841177&ra=YOUR_CUSTOM_CACHE_BUSTER' style='display:none;padding:0;margin:0' width='0' height='0'>
		</iframe>
		</noscript>
		<!-- End DSP Conversion Action Tracking Code Version 9 -->

	<?php } ?>
	
    <?php wp_head(); ?>
</head>
<body class="<?php echo($section); ?><?php echo($slug); ?><?php if ($dev) { ?> dev-env<?php } ?><?php if (is_user_logged_in()) { ?> logged-in<?php } ?>" data-slug="<?php echo($slug); ?>">
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5X665TK"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php if ($GLOBALS['leadattribution']) { ?>
		<div class="attribution">
			<div class="data"<?php echo($attribution); ?><?php echo($attr_settings); ?>>
				<div class="visits" style="display:none;">
					<?php echo(trim($pagevisits)); ?>
				</div>
			</div>
		</div>
	<?php } ?>
    <?php wp_body_open(); ?>
	<?php $sitebanner = false; ?>
	<?php if (isset(get_field('sitebanner_settings', 'options')['display'])) { $sitebanner = get_field('sitebanner_settings', 'options')['display']; } ?>
	<?php if ($sitebanner) { ?>
		<div class="top-bar" data-nosnippet>
			<div class="container">
				<div class="row">
					<div class="col-12 notice" data-nosnippet>
						<?php $type = get_field('sitebanner_settings', 'options')['type']; 
							if ($type == 'internal') {
								$dest = get_field('sitebanner_settings', 'options')['page'];
							} elseif ($type == 'external') {
								$dest = get_field('sitebanner_settings', 'options')['url'];
							} elseif ($type == 'email') {
								$dest = "mailto:" . get_field('sitebanner_settings', 'options')['email'];
							}
							if ($type != 'email' && get_field('sitebanner_settings', 'options')['new_tab']) {
								$target = "_blank";
							}
						?>
						<?php echo(get_field('sitebanner_text', 'options')); ?><?php if (isset(get_field('sitebanner_settings', 'options')['text']) && get_field('sitebanner_settings', 'options')['text'] != '') { ?>&nbsp;&nbsp;<a class="more" <?php if(isset($target)) { ?>target=<?php echo($target); ?> <?php } ?>href="<?php echo($dest); ?>"><?php echo(get_field('sitebanner_settings', 'options')['text']); ?></a><?php } ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<header class="<?php echo($block0type . ' ' . $block0bkgnd); ?>">
		<div class="container">
            <div class="row header-top align-items-center">
                <div class="logo col-3">
                    <a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/nura_logo.svg" alt="<?php echo(get_field('company', 'options')); ?>"/></a>
                </div>
                <div class="hamburger d-lg-none">
					<div class="hamburger-container">
						<div class="line top"></div>
						<div class="line middle"></div>
						<div class="line bottom"></div>
					</div>
				</div>
                <div class="menu secondary-menu col-lg-5">
                    <ul>
                    	<?php $appointments = get_field('appointments_number', 'options'); ?>
                        <li class="<?php $me = 'patient-portal'; echo($me); ?><?php if ($slug == $me || $me == $section) { ?> current<?php } ?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Patient Portal</a></li>
                        <li class="<?php $me = 'careers-at-nura'; echo($me); ?><?php if ($slug == $me || $me == $section) { ?> current<?php } ?>"><a href="<?php bloginfo('url'); ?>/about-nura/<?php echo($me); ?>/">Careers</a></li>
                        <li class="appointments"><a href="tel:<?php echo($appointments); ?>/"><?php echo($appointments); ?></a></li>
                    </ul>
                </div>
                <div class="menu appointments-button col-lg-4">
                	<ul>
                		<li class="<?php $me = 'request-appointment'; echo($me); ?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Request an Appointment</a></li>
                	</ul>
                </div>
            </div>
			<div class="divider col-12"><div class="line"></div></div>
            <div class="row header-bottom align-items-center">
            	<div class="menu-reset"><a href="#">Back</a></div>
                <div class="menu main-menu col-lg-7">
                    <ul>
                        <li class="<?php $me = 'conditions'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">Conditions</a>
                        	<div class="mega drop">
                        		<div class="container">
                        			<div class="row">
                        				<div class="mouse-hit"></div>
                        				<div class="col-12">
			                        		<div class="view-all"><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/">View All Conditions</a></div>
			                        		<?php $myid = get_page_by_path($me)->ID; ?>
											<?php // echo('<pre>'); print_r($myid); echo('</pre>'); ?>
			                        		<div class="descriptor"><p><?php the_field('megadrop_descriptor', $myid); ?></p></div>
			                        		<?php
											    $args = array(
											        'post_type' => 'page',
											        'post_status' => 'publish',
											        'orderby' => 'menu_order',
											        'order' => 'ASC',
											        'numberposts' => -1,
											        'post_parent' => $myid
											    );
											    $categories = get_posts($args);
											    $conditions = [];
											    foreach ($categories as $i => $cat) {
											    	$mychildren = get_children(array(
											    		'post_parent' => $cat->ID,
												        'post_status' => 'publish',
												        'orderby' => 'menu_order',
												        'order' => 'ASC',
											    	));
											    	$conditions[$i]['category'] = $cat;
											    	$conditions[$i]['children'] = $mychildren;
											    	// echo('<pre>'); print_r($mychildren); echo('</pre>');
											    }
											    // echo('<pre>'); print_r($conditions); echo('</pre>');
			                        		?>
			                        		<ul class="nav-container">
			                        			<?php foreach ($conditions as $cat) { ?>
			                        				<li class="category">
			                        					<?php echo($cat['category']->post_title); ?>
			                        					<ul class="conditions">
			                        						<?php foreach($cat['children'] as $condition) { ?>
			                        							<li><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/<?php echo($cat['category']->post_name); ?>/<?php echo($condition->post_name); ?>/"><?php echo($condition->post_title); ?></a></li>
			                        						<?php } ?>
			                        					</ul>
			                        				</li>
			                        			<?php } ?>
			                        		</ul>
		                        		</div>
                        			</div>
                        		</div>
                        	</div>
                        </li>
                        <li class="<?php $me = 'treatments'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">Treatments</a>
                        	<div class="mega drop">
                        		<div class="container">
                        			<div class="row">
                        				<div class="mouse-hit"></div>
                        				<div class="col-12">
			                        		<div class="view-all"><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/">View All Treatments</a></div>
			                        		<?php $myid = get_page_by_path($me)->ID; ?>
											<?php // echo('<pre>'); print_r($myid); echo('</pre>'); ?>
			                        		<div class="descriptor"><p><?php the_field('megadrop_descriptor', $myid); ?></p></div>
			                        		<?php
											    $args = array(
											        'post_type' => 'page',
											        'post_status' => 'publish',
											        'orderby' => 'menu_order',
											        'order' => 'ASC',
											        'numberposts' => -1,
											        'post_parent' => $myid
											    );
											    $categories = get_posts($args);
											    $treatments = [];
											    foreach ($categories as $i => $cat) {
											    	$mychildren = get_children(array(
											    		'post_parent' => $cat->ID,
												        'post_status' => 'publish',
												        'orderby' => 'menu_order',
												        'order' => 'ASC',
											    	));
											    	$treatments[$i]['category'] = $cat;
											    	$treatments[$i]['children'] = $mychildren;
											    	// echo('<pre>'); print_r($mychildren); echo('</pre>');
											    }
											    // echo('<pre>'); print_r($treatments); echo('</pre>');
			                        		?>
			                        		<ul class="nav-container">
			                        			<?php foreach ($treatments as $cat) { ?>
			                        				<li class="category">
			                        					<?php echo($cat['category']->post_title); ?>
			                        					<ul class="treatments">
			                        						<?php foreach($cat['children'] as $condition) { ?>
			                        							<li><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/<?php echo($cat['category']->post_name); ?>/<?php echo($condition->post_name); ?>/"><?php echo($condition->post_title); ?></a></li>
			                        						<?php } ?>
			                        					</ul>
			                        				</li>
			                        			<?php } ?>
			                        		</ul>
		                        		</div>
                        			</div>
                        		</div>
                        	</div>
                        </li>
                        <li class="<?php $me = 'about-nura'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">About Nura</a>
                        	<div class="mouse-hit"></div>
                        	<div class="drop">
                        		<h5>About Nura</h5>
                        		<ul>
                        			<li><a href="<?php bloginfo('url'); ?>/about-nura/why-nura/">Why Nura?</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/about-nura/our-team/">Our Team</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/about-nura/careers-at-nura/">Careers</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/about-nura/news/">News</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/about-nura/contact/">Contact Us</a></li>
                        		</ul>
                        	</div>
                        </li>
                        <li class="<?php $me = 'locations'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">Locations</a>
                        	<div class="mouse-hit"></div>
                        	<div class="drop">
                        		<h5>Locations</h5>
                        		<ul>
                        			<li><a href="<?php bloginfo('url'); ?>/locations/edina-pain-clinic/">Edina Clinic</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/locations/edina-pump-services/">Edina Pump Services</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/locations/coon-rapids-pain-clinic/">Coon Rapids Clinic</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/locations/woodbury-pain-clinic/">Woodbury Clinic</a></li>
                        		</ul>
                        	</div>
                        </li>
                    </ul>
                </div>
                <div class="menu audience-menu col-lg-5">
                    <ul>
                        <li class="<?php $me = 'for-patients'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">For Patients</a>
                        	<div class="mouse-hit"></div>
                        	<div class="drop">
                        		<h5>For Patients</h5>
                        		<ul>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/patient-portal/">Patient Portal</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/insurance-and-billing/">Insurance & Billing</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/medical-records/">Medical Records</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/prepare-for-an-appointment/">Prepare for an Appointment</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/caregiver-support/">Caregiver Support</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/submit-feedback/">Submit Feedback</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/blog/">Blog</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-patients/resources-and-events/">Resources & Events</a></li>
                        		</ul>
                        	</div>
                        </li>
                        <li class="<?php $me = 'for-physicians'; echo($me); ?> has-submenu<?php if ($slug == $me || $me == $section) { ?> current<?php } ?>">
                        	<a href="#">For Physicians</a>
                        	<div class="mouse-hit"></div>
                        	<div class="drop">
                        		<h5>For Physicians</h5>
                        		<ul>
                        			<li><a href="<?php bloginfo('url'); ?>/for-physicians/why-refer-to-nura/">Why Refer to Nura</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-physicians/refer-a-patient/">Refer a Patient</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-physicians/trials-and-research/">Trials & Research</a></li>
                        			<li><a href="<?php bloginfo('url'); ?>/for-physicians/education-and-training/">Education & Training</a></li>
                        		</ul>
                        	</div>
                        </li>
                    </ul>
                </div>
                <div class="menu secondary-menu d-lg-none">
                    <ul>
                    	<?php $appointments = get_field('appointments_number', 'options'); ?>
                        <li class="<?php $me = 'patient-portal'; echo($me); ?><?php if ($slug == $me || $me == $section) { ?> current<?php } ?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Patient Portal</a></li>
                        <li class="<?php $me = 'careers-at-nura'; echo($me); ?><?php if ($slug == $me || $me == $section) { ?> current<?php } ?>"><a href="<?php bloginfo('url'); ?>/about-nura/<?php echo($me); ?>/">Careers</a></li>
                        <li class="appointments"><a href="tel:<?php echo($appointments); ?>/"><?php echo($appointments); ?></a></li>
                    </ul>
                </div>
                <div class="menu appointments-button d-lg-none">
                	<ul>
                		<li class="<?php $me = 'request-appointment'; echo($me); ?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Request an Appointment</a></li>
                	</ul>
                </div>
            </div>
        </div>
	</header>
    <div class="content-container">
        <div class="content">
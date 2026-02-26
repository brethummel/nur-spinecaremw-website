        </div>
    </div>
    <div class="pop-overlay">
        <div class="pop-modal">
            <div class="pop-close"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/icon_pop_close.png" alt="Close" /></div>
            <div class="pop-content"></div>
        </div>
    </div>
    <footer>
        <div class="footer-container">
            <div class="content top-footer">
            	<div class="container">
            		<div class="row">
						<?php  
							$locations = get_field('locations', 'options');
							$company = get_field('company', 'options');
						
							global $post;
							if (isset($post)) {
								$slug = $post->post_name;
							} else {
								$slug = '';
								$section = 'error-404';
							}
							$ancestors = get_post_ancestors($post);
							if (count($ancestors) > 0) {
								$section = get_post_field('post_name', $ancestors[count($ancestors)-1]);
							} else {
								$section = '';
							}
						?>
		                <div class="logo col-12 col-lg-3">
		                    <a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/nura_logo.svg" alt="<?php echo(get_field('company', 'options')); ?>"/></a>
		                </div>
						<div class="navigation col-12 col-lg-9">
							<div class="nav-container">
								<ul class="categories">
									<li class="for-patients">
										<p>For Patients</p>
										<ul>
											<li class="<?php $me = 'patient-portal'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Patient Portal</a></li>
											<li class="<?php $me = 'insurance-and-billing'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/">Pay Bill</a></li>
											<li class="<?php $me = 'request-appointment'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/">Request Appointment</a></li>
											<li class="<?php $me = 'medical-records'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>/">Medical Records</a></li>
										</ul>
									</li>
									<li class="for-physicians">
										<p>For Physicians</p>
										<ul>
											<li class="<?php $me = 'refer-a-patient'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/for-physicians/<?php echo($me); ?>/">Refer a Patient</a></li>
											<li class="<?php $me = 'current-trials-and-research'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/for-physicians/<?php echo($me); ?>/">Trials & Research</a></li>
										</ul>
									</li>
									<li class="feedback">
										<p>We want to hear from you!</p>
										<ul>
											<!--<li class="<?php $me = 'submit-feedback'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/for-patients/<?php echo($me); ?>">Submit Feedback</a></li>-->
											<li class="<?php $me = 'contact'; echo($me);?>"><a href="<?php bloginfo('url'); ?>/about-nura/<?php echo($me); ?>/">Contact Us</a></li>
										</ul>
										<!--<div class="hipaa-seal"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/hipaa_seal.png" alt="HIPAA Verified"/></div>-->
									</li>
								</ul>
							</div>
						</div>
						<div class="divider col-12"><div class="line"></div></div>
						<div class="address col-lg-12">
							<div class="address-container">
								<ul class="locations">
									<?php 
										if (isset($locations)) {
											foreach ($locations as $location) { ?>
												<?php // echo('<pre>'); print_r($location); echo('</pre>'); ?>
												<li>
													<p class="title"><?php echo($location['name']); ?></p>
													<p><?php echo($location['address']['street_address']); ?></p>
													<p><?php echo($location['address']['street_address2']); ?></p>
													<p><?php echo($location['address']['city']['city']); ?>, <?php echo($location['address']['city']['state']); ?> <?php echo($location['address']['city']['zip']); ?></p>
													<p class="title hours">Clinic & Office Hours</p>
													<p><?php echo($location['hours']); ?></p>
												</li>
										<?php }
										} 
									?>
									<li class="phone-number">
										<p class="title">Phone Number</p>
										<p><a href="tel:<?php echo(get_field('appointments_number', 'options')); ?>"><?php echo(get_field('appointments_number', 'options')); ?></a></p>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content bottom-footer">
            	<div class="container">
            		<div class="row">
            			<div class="legal-links col-12 col-md-9 col-lg-10">
							<div class="nav-container">
								<ul>
									<li class=""><a href="<?php bloginfo('url'); ?>/privacy-policy/">Privacy Policy</a></li>
									<li class=""><a href="<?php bloginfo('url'); ?>/terms-and-conditions/">Terms & Conditions</a></li>
									<li class=""><a href="<?php bloginfo('url'); ?>/good-faith-estimates/">Good Faith Estimates</a></li>
									<li class=""><a href="<?php bloginfo('url'); ?>/no-surprises-billing-act/">No Surprises Billing Act</a></li>
									<li class=""><a href="<?php echo(get_field('privacy_practices', 'options')['url']); ?>" target="_blank">Notice of Privacy Practices</a></li>
								</ul>
							</div>
            			</div>
						<?php  
							$social = get_field('social', 'options');
							if (isset($social)) {
								$linkedin = $social['linkedin'];
								$facebook = $social['facebook'];
								$instagram = $social['instagram'];
								$twitter = $social['twitter'];
								$youtube = $social['youtube'];
							}
						?>
						<?php if (isset($social) && $social['social_display']) { ?>
						<div class="social col-12 col-md-3 col-lg-2">
							<ul>
								<?php if ($facebook['facebook_include']) { ?>
								<li>
									<a href="<?php echo $facebook['facebook_url']; ?>" target="_blank"><svg version="1.1" x="0px" y="0px" width="32" height="32" viewBox="0 0 32 32">
									<path d="M16,0C7.2,0,0,7.2,0,16s7.2,16,16,16s16-7.2,16-16S24.8,0,16,0z M20.6,10.6h-1.2
										c-1.7,0-1.7,0.8-1.7,1.7v1.7h2.9l-0.8,3.3h-2.1v7.1h-3.3v-7.1h-2.5v-3.3h2.5v-2.3c0-2.5,1.6-3.9,3.9-3.9c1.1,0,2.1,0.1,2.4,0.1V10.6
										z"/>
									</svg>
									</a>
								</li>
								<?php } ?>
								<?php if ($youtube['youtube_include']) { ?>
								<li>
									<a href="<?php echo $youtube['youtube_url']; ?>" target="_blank">
										<svg width="28" height="20" viewBox="0 0 28 20"><path d="M27.43,3.77c-0.24-2.02-0.72-2.61-1.06-2.88c-0.55-0.43-1.54-0.58-2.87-0.67C21.36,0.09,17.75,0,13.84,0C9.93,0,6.32,0.09,4.19,0.23C2.86,0.32,1.87,0.47,1.32,0.9C0.98,1.17,0.49,1.76,0.26,3.77c-0.34,2.91-0.34,9.54,0,12.45c0.24,2.02,0.72,2.61,1.06,2.87c0.55,0.43,1.54,0.58,2.87,0.67C6.32,19.91,9.93,20,13.84,20c3.91,0,7.52-0.09,9.65-0.23c1.33-0.09,2.32-0.24,2.87-0.67c0.34-0.27,0.83-0.86,1.06-2.87C27.77,13.32,27.77,6.68,27.43,3.77z M10.5,14.35V5.65L18.85,10L10.5,14.35z"/></svg>
									</a>
								</li>
								<?php } ?>
								<?php if ($linkedin['linkedin_include']) { ?>
								<li>
									<a href="<?php echo $linkedin['linkedin_url']; ?>" target="_blank">
										<svg version="1.1" x="0px" y="0px" width="32" height="32" viewBox="0 0 32 32" xml:space="preserve">
										<path d="M16,0C7.2,0,0,7.2,0,16c0,8.8,7.2,16,16,16c8.8,0,16-7.2,16-16C32,7.2,24.8,0,16,0z M8.9,9
											c0.3-0.3,0.7-0.5,1.2-0.5c0.4,0,0.9,0.2,1.2,0.5c0.3,0.3,0.5,0.7,0.5,1.2c0,0.4-0.2,0.9-0.5,1.2c-0.3,0.3-0.7,0.5-1.2,0.5
											c-0.4,0-0.9-0.2-1.2-0.5c-0.3-0.3-0.5-0.7-0.5-1.2C8.5,9.7,8.6,9.3,8.9,9z M11.8,23.5H8.5V13.1h3.3V23.5z M24.3,23.5H21V18
											c0-3.3-4-3-4,0v5.5h-3.3V13.1h3.3l0,1.4c1.4-2.5,7.3-2.7,7.3,2.4V23.5z"/>
										</svg>
									</a>
								</li>
								<?php } ?>
								<?php if ($instagram['instagram_include']) { ?>
								<li>
									<a href="<?php echo $instagram['instagram_url']; ?>" target="_blank">
										<svg width="20px" height="20px" viewBox="0 0 20 20"><g><path d="M10,4.8c-2.8,0-5.2,2.3-5.2,5.2s2.3,5.2,5.2,5.2s5.2-2.3,5.2-5.2S12.8,4.8,10,4.8z M10,13.3c-1.8,0-3.3-1.5-3.3-3.3 S8.2,6.7,10,6.7s3.3,1.5,3.3,3.3S11.8,13.3,10,13.3z"/><circle cx="15.4" cy="4.7" r="1.2"/><path d="M18.4,1.7c-1-1.1-2.5-1.7-4.2-1.7H5.8C2.3,0,0,2.3,0,5.8v8.3c0,1.7,0.6,3.2,1.7,4.3c1.1,1,2.5,1.6,4.2,1.6h8.2 c1.7,0,3.2-0.6,4.2-1.6c1.1-1,1.7-2.5,1.7-4.3V5.8C20,4.2,19.4,2.7,18.4,1.7z M18.2,14.2c0,1.3-0.4,2.3-1.2,2.9 c-0.7,0.7-1.7,1-2.9,1H5.9c-1.2,0-2.2-0.4-2.9-1c-0.7-0.7-1.1-1.7-1.1-3V5.8c0-1.2,0.4-2.2,1.1-2.9c0.7-0.7,1.7-1,2.9-1h8.3 c1.2,0,2.2,0.4,2.9,1.1c0.7,0.7,1.1,1.7,1.1,2.9V14.2L18.2,14.2z"/></g></svg>
									</a>
								</li>
								<?php } ?>
								<?php if ($twitter['twitter_include']) { ?>
								<li>
									<a href="<?php echo $twitter['twitter_url']; ?>" target="_blank">
										<svg width="22" height="18" viewBox="0 0 22 18"><path d="M382.27,121a9,9,0,0,1-2.6.72,4.51,4.51,0,0,0,2-2.52,9.16,9.16,0,0,1-2.87,1.11,4.45,4.45,0,0,0-3.29-1.44,4.53,4.53,0,0,0-4.52,4.55,4.4,4.4,0,0,0,.12,1,12.77,12.77,0,0,1-9.3-4.75,4.58,4.58,0,0,0,1.39,6.07,4.42,4.42,0,0,1-2-.57v.06a4.55,4.55,0,0,0,3.62,4.46,4.68,4.68,0,0,1-1.19.15,4,4,0,0,1-.85-.08,4.53,4.53,0,0,0,4.22,3.16,9,9,0,0,1-5.61,1.94,9.46,9.46,0,0,1-1.07-.06A12.82,12.82,0,0,0,380,123.9c0-.19,0-.39,0-.58A9.39,9.39,0,0,0,382.27,121Z" transform="translate(-360.27 -118.83)"/></svg>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>
						<div class="divider col-12"><div class="line"></div></div>
						<div class="legal col-12">
							<p>The contents of the Nura Website, such as text, graphics, videos, and other material contained on the Website ("Content") are provided to you for general information only. The Content is not intended to be a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition. Do not disregard, avoid or delay getting professional medical advice from a qualified healthcare professional because of something you have seen on this Website. Further, the sharing of this information for educational purposes does not constitute an endorsement of the product, method or author.</p>
							<div class="copyright">
								<div class="copyright-container">
									<p>&copy;<?php echo date("Y"); ?> <?php echo($company); ?>. All rights reserved.</p>
								</div>
							</div>
						</div>
            		</div>
            	</div>
			</div>
		</div>
	</footer>
<?php wp_footer(); ?>
<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/49109065.js"></script>
<!-- End of HubSpot Embed Code -->
</body>
</html>
<?php
/*
 * Template Name: Contact
 */
get_header(); ?>

<?php
	$primary_location = MakespaceChild::get_primary_location();

	$contact_address = '';
	$phone = '';
	$fax = '';
	$email = '';
	$contact_url = '';

	$map_location = '';
	$directions_link = '';

	if($primary_location){

		$address_1 = get_field('street_address_line_1', $primary_location->ID);
		$address_2 = get_field('street_address_line_2', $primary_location->ID);
		$address_city = get_field('city', $primary_location->ID);
		$address_state = get_field('state_region', $primary_location->ID);
		$address_zip = get_field('zip_postal_code', $primary_location->ID);
		$address_country = get_field('country', $primary_location->ID);


		if($address_1){
			$contact_address .= $address_1 . '<br>';
		}
		if($address_2){
			$contact_address .= $address_2 . '<br>';
		}
		if($address_city){
			$contact_address .= $address_city . ', ';
		}
		if($address_state){
			$contact_address .= $address_state;
		}
		if($address_zip){
			$contact_address .= ' ' . $address_zip . '<br>';
		}
		if($address_country){
			$contact_address .= $address_country;
		}
		
		$phone = get_field('phone', $primary_location->ID);
		$fax = get_field('fax', $primary_location->ID);
		$email = get_field('email', $primary_location->ID);
		$contact_url = get_field('url', $primary_location->ID);

		$map_location = get_field('google_map', $primary_location->ID);
		$directions_link = makespaceChild::get_google_directions_url( $map_location['address'] );
	}
?>

	<div class="container">
		<?php while( have_posts() ): the_post(); ?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="contact-page-content">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
					<section class="contact-info">
						<h2>Contact Info</h2>
						<?php if($directions_link && $contact_address){ ?>
							<p><a title="Get directions" href="<?php echo $directions_link; ?>" target="_blank"><?php echo $contact_address; ?></a></p>
						<?php }
						elseif($contact_address){ ?>
							<p><?php echo $contact_address; ?></p>
						<?php } ?>
						<?php if($phone){ ?>
							<p>Phone: <a title="Phone number" href="tel:<?php echo preg_replace('/\D+/', '', $phone); ?>"><?php echo $phone; ?></a></p>
						<?php } ?>
						<?php if($fax){ ?>
							<p>Fax: <a title="Fax Number" href="tel:<?php echo preg_replace('/\D+/', '', $fax); ?>"><?php echo $fax; ?></a></p>
						<?php } ?>
						<?php if($email){ ?>
							<p>Email: <a title="Email" href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
						<?php } ?>
					</section>
					<?php if($primary_location && have_rows('social_media_links', $primary_location->ID)): ?>
						<ul class="footer-social">
							<?php while(have_rows('social_media_links', $primary_location->ID)): the_row(); ?>
								<?php 
									$accessiblity = get_sub_field('icon_or_image') ? get_sub_field('icon_or_image') : 'Social Media Link'; 
									if(strpos($accessiblity, '-') !== false) {
										$accessiblity = explode('-', $accessiblity)[1];
									}
								?>
								<li>
									<a title="<?php echo ucfirst($accessiblity); ?>" href="<?php the_sub_field('link'); ?>" target="_blank">
										<span class="<?php the_sub_field('icon_or_image'); ?>"></span>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="contact-page-form">
					<?php echo do_shortcode('[gravityform id="1" title="true" description="false" ajax="true"]'); ?>
				</div>
				<div id="gmap" data-maxZoom="18" data-minZoom="1"></div>
			</article>
		<?php endwhile; ?>
	</div>

<?php get_footer();

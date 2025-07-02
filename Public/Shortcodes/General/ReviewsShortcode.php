<?php
namespace RevixReviews\Public\Shortcodes\General;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ReviewsShortcode
{

	public function __construct()
	{
		add_shortcode('revixreviews', array($this, 'display_grid_review_markup')); // Display grid review.
	}

	public function display_grid_review_markup($atts = [])
	{

		// Shortcode attributes: count, min_rating, max_rating
		$atts = shortcode_atts([
			'count' => -1,
			'min_rating' => 0,
			'max_rating' => 5,
		], $atts, 'revixreviews');

		ob_start();

		$args = array(
			'post_type' => 'revixreviews',
			'post_status' => 'publish',
			'posts_per_page' => intval($atts['count']),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'revixreviews_rating',
					'value' => floatval($atts['min_rating']),
					'compare' => '>=',
					'type' => 'NUMERIC'
				),
				array(
					'key' => 'revixreviews_rating',
					'value' => floatval($atts['max_rating']),
					'compare' => '<=',
					'type' => 'NUMERIC'
				)
			),
		);

		$query = new \WP_Query($args);

		// Check if the query returns any posts.
		if ($query->have_posts()) {
			echo '<div class="revix-testimonial-wrapper-area"><div class="revix-testimonial-wrapper"><div class="revix-testimonial-slider revix-testimonial-grids">';
			// Loop through the posts.
			while ($query->have_posts()) {
				$query->the_post();
				$testimonial_rating = get_post_meta(get_the_ID(), 'revixreviews_rating', true);
				?>
				<div class="revix-testimonial-single-item">
					<div class="revix-testimonial-client-info">
						<h3>
							<?php the_title(); ?>
						</h3>
					</div>
					<div class="revix-testimonial-rating">
						<?php for ($i = 1; $i <= 5; $i++): ?>
							<?php if ($i <= $testimonial_rating): ?>
								<!-- Filled Star SVG -->
								<svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 0 32 32" width="18" fill="#f5a623">
									<path
										d="m29.911 13.75-6.229 6.072 1.471 8.576c.064.375-.09.754-.398.978-.174.127-.381.191-.588.191-.159 0-.319-.038-.465-.115l-7.702-4.049-7.701 4.048c-.336.178-.745.149-1.053-.076-.308-.224-.462-.603-.398-.978l1.471-8.576-6.23-6.071c-.272-.266-.371-.664-.253-1.025s.431-.626.808-.681l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25c.377.055.69.319.808.681s.019.758-.253 1.025z" />
								</svg>
							<?php else: ?>
								<!-- Outlined Star SVG -->
								<svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 -10 512 512" width="18" fill="#ccc">
									<path
										d="m114.59375 491.140625c-5.609375 0-11.179688-1.75-15.933594-5.1875-8.855468-6.417969-12.992187-17.449219-10.582031-28.09375l32.9375-145.089844-111.703125-97.960937c-8.210938-7.167969-11.347656-18.519532-7.976562-28.90625 3.371093-10.367188 12.542968-17.707032 23.402343-18.710938l147.796875-13.417968 58.433594-136.746094c4.308594-10.046875 14.121094-16.535156 25.023438-16.535156 10.902343 0 20.714843 6.488281 25.023437 16.511718l58.433594 136.769532 147.773437 13.417968c10.882813.980469 20.054688 8.34375 23.425782 18.710938 3.371093 10.367187.253906 21.738281-7.957032 28.90625l-111.703125 97.941406 32.9375 145.085938c2.414063 10.667968-1.726562 21.699218-10.578125 28.097656-8.832031 6.398437-20.609375 6.890625-29.910156 1.300781l-127.445312-76.160156-127.445313 76.203125c-4.308594 2.558594-9.109375 3.863281-13.953125 3.863281zm141.398438-112.875c4.84375 0 9.640624 1.300781 13.953124 3.859375l120.277344 71.9375-31.085937-136.941406c-2.21875-9.746094 1.089843-19.921875 8.621093-26.515625l105.472657-92.5-139.542969-12.671875c-10.046875-.917969-18.6875-7.234375-22.613281-16.492188l-55.082031-129.046875-55.148438 129.066407c-3.882812 9.195312-12.523438 15.511718-22.546875 16.429687l-139.5625 12.671875 105.46875 92.5c7.554687 6.613281 10.859375 16.769531 8.621094 26.539062l-31.0625 136.9375 120.277343-71.914062c4.308594-2.558594 9.109376-3.859375 13.953126-3.859375z" />
								</svg>
							<?php endif; ?>
						<?php endfor; ?>
					</div>

					<div class="revix-testimonial-content">
						<?php the_content(); ?>
					</div>
					<div class="revix-testimonial-quote">
						<svg xmlns="http://www.w3.org/2000/svg" width="68" height="50" viewBox="0 0 68 50" fill="none">
							<mask id="mask0_147_7210" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="68"
								height="50">
								<path d="M68 0H0V49.8333H68V0Z" fill="white"></path>
							</mask>
							<g mask="url(#mask0_147_7210)">
								<path
									d="M18.4355 0.958008C27.4644 0.958008 33.9999 8.62457 33.9999 19.7796C33.9999 36.0712 21.8354 47.6097 4.41985 49.833C4.08581 49.8628 3.75216 49.773 3.47656 49.5792C3.20097 49.3853 3.00094 49.0996 2.91119 48.7717C2.82144 48.4439 2.84762 48.0944 2.9852 47.7841C3.12279 47.4738 3.36294 47.2224 3.66425 47.0731C10.3509 44.0831 13.751 40.2497 14.1666 36.4547C14.4006 35.2745 14.2186 34.0483 13.6524 32.9903C13.0862 31.9324 12.1718 31.1099 11.0688 30.6663C6.11989 29.478 2.83325 23.2297 2.83325 16.7897C2.83325 12.5909 4.47708 8.56403 7.40306 5.59502C10.329 2.62601 14.2975 0.958008 18.4355 0.958008Z"
									fill="white"></path>
								<path
									d="M52.4351 0.958008C61.464 0.958008 67.9995 8.62457 67.9995 19.7796C67.9995 36.0712 55.835 47.6097 38.4195 49.833C38.0854 49.8628 37.7518 49.773 37.4762 49.5792C37.2006 49.3853 37.0006 49.0996 36.9108 48.7717C36.8211 48.4439 36.8473 48.0944 36.9848 47.7841C37.1224 47.4738 37.3626 47.2224 37.6639 47.0731C44.3506 44.0831 47.7507 40.2497 48.1662 36.4547C48.4002 35.2745 48.2182 34.0483 47.652 32.9903C47.0858 31.9324 46.1714 31.1099 45.0684 30.6663C40.1195 29.478 36.8329 23.2297 36.8329 16.7897C36.8329 12.5909 38.4767 8.56403 41.4027 5.59502C44.3287 2.62601 48.2972 0.958008 52.4351 0.958008Z"
									fill="white"></path>
							</g>
						</svg>
					</div>
				</div>
				<?php
			}
			echo '</div></div></div>';
		}

		// Restore original Post Data.
		wp_reset_postdata();

		// Return the buffered content.
		return ob_get_clean();
	}
}
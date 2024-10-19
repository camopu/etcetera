<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">
			<a href="<?php echo esc_url(wp_get_referer()); ?>" class="btn btn-secondary mb-3">Назад</a>
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <div class="row mt-3">
            <div class="col-md-6">
                <?php
                $images = get_field('images');
                if ($images) {
                    echo '<div class="property-image">';
                    if (!empty($images['url'])) {
                        echo '<img src="' . esc_url($images['url']) . '" alt="' . esc_attr($images['alt']) . '" class="img-fluid" />';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="col-md-6">
                <?php
                // Extract data from ACF
                $house_name = get_field('house_name');
                $location_coordinates = get_field('location_coordinates');
                $floors = get_field('floors');
                $building_type = get_field('building_type');
                $ecological_rating = get_field('ecological_rating');
								$count = get_field('rooms_room_count');
								$area = get_field('rooms_area');
								$balcony = get_field('rooms_balcony');
								$bathroom = get_field('rooms_bathroom');

                // Extract basic data
                if ($location_coordinates) {
                    echo '<p>Координати: ' . esc_html($location_coordinates) . '</p>';
                }
                if ($floors) {
                    echo '<p>Кількість поверхів: ' . esc_html($floors) . '</p>';
                }
                if ($building_type) {
                    echo '<p>Тип будівлі: ' . esc_html($building_type) . '</p>';
                }
                if ($ecological_rating) {
                    echo '<p>Екологічність: ' . esc_html($ecological_rating) . '</p>';
                }
								if ($count) {
									echo '<p>Кількість кiмнат: ' . esc_html($count) . '</p>';
								}
								if ($area) {
									echo '<p>Площа: ' . esc_html($area) . '</p>';
								}
								if ($balcony) {
									echo '<p>Балкони: ' . esc_html($balcony) . '</p>';
								}
								if ($bathroom) {
									echo '<p>Санузол: ' . esc_html($bathroom) . '</p>';
								}
              ?>
            </div>
        </div><!-- .row -->

        <div class="description mt-4">
            <?php
            the_content();
            ?>
        </div><!-- .description -->
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php understrap_entry_footer(); ?>
    </footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
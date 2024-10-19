<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Real_Estate
 * @subpackage Real_Estate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Real_Estate
 * @subpackage Real_Estate/includes
 * @author     Alex Akimchenko
 */
class Real_Estate {

    protected $loader;
    protected $real_estate;
    protected $version;

    public function __construct() {
        if ( defined( 'REAL_ESTATE_VERSION' ) ) {
            $this->version = REAL_ESTATE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->real_estate = 'real-estate';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_custom_hooks(); 
    }

    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-real-estate-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-real-estate-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-real-estate-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-real-estate-public.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-real-estate-query.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-real-estate-widget.php';

        $this->loader = new Real_Estate_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Real_Estate_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    private function define_admin_hooks() {
        $plugin_admin = new Real_Estate_Admin( $this->get_real_estate(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    }

    private function define_public_hooks() {
        $plugin_public = new Real_Estate_Public( $this->get_real_estate(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

    private function define_custom_hooks() {
        // JavaScript connection for AJAX
        add_action('wp_enqueue_scripts', array($this, 'real_estate_enqueue_scripts'));

        // Registration of the custom post type and taxonomy
        add_action('init', array($this, 'real_estate_plugin_init'));

        // Shortcode initialization
        add_shortcode('real_estate_filter', array($this, 'real_estate_shortcode'));

        // Widget registration
        add_action('widgets_init', array($this, 'register_real_estate_widget'));

        // AJAX
        add_action('wp_ajax_real_estate_search', array($this, 'real_estate_ajax_search'));
        add_action('wp_ajax_nopriv_real_estate_search', array($this, 'real_estate_ajax_search'));

        // Class to modify query
        new Real_Estate_Query();
    }

    public function run() {
        $this->loader->run();
    }

    public function get_real_estate() {
        return $this->real_estate;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    // JavaScript connection for AJAX
    public function real_estate_enqueue_scripts() {
        wp_enqueue_script('real-estate-ajax', plugin_dir_url(__FILE__) . '../public/js/real-estate-public.js', array('jquery'), null, true);
        wp_localize_script('real-estate-ajax', 'ajaxurl', admin_url('admin-ajax.php')); 
    }

    // Register new post type
    public function real_estate_plugin_init() {
        register_post_type('real_estate', array(
            'labels' => array(
                'name' => __("Об'єкт нерухомості"),
                'singular_name' => __("Об'єкт нерухомості")
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'real-estate'),
        ));

        // Registration taxonomy
        register_taxonomy('district', 'real_estate', array(
            'label' => __('Район'),
            'rewrite' => array('slug' => 'district'),
            'hierarchical' => true,
        ));
    }

    // Shortcode initialization
    public function real_estate_shortcode() {
        ob_start();
        ?>
        <form id="real-estate-filter" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="house_name" class="form-control" placeholder="Назва будинку">
            </div>
            <div class="col-md-4">
                <input type="text" name="location_coordinates" class="form-control" placeholder="Координати">
            </div>
            <div class="col-md-4">
                <select name="floors" class="form-control">
                    <option value="">Кількість поверхів</option>
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <select name="building_type" class="form-control">
                    <option value="">Тип будівлі</option>
                    <option value="panel">Панель</option>
                    <option value="brick">Цегла</option>
                    <option value="foam">Піноблок</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="ecological_rating" class="form-control">
                    <option value="">Eкологічність</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
						<div class="col-md-4">
                <input type="text" name="area" class="form-control" placeholder="Площа">
            </div>
        </div>
        <div class="row mt-3">
            
            <div class="col-md-4">
                <select name="room_count" class="form-control">
                    <option value="">Кількість кімнат</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="balcony" class="form-control">
                    <option value="">Балкон</option>
                    <option value="yes">Так</option>
                    <option value="no">Ні</option>
                </select>
            </div>
						<div class="col-md-4">
                <select name="bathroom" class="form-control">
                    <option value="">Санвузол</option>
                    <option value="yes">Так</option>
                    <option value="no">Ні</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
						<div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-block">Фільтрувати</button>
            </div>
        </div>
    	</form>
        <div id="real-estate-results"></div>
        <?php
        return ob_get_clean();
    }

    // Registration widget
    public function register_real_estate_widget() {
        register_widget('Real_Estate_Widget');
    }

    // AJAX
    public function real_estate_ajax_search() {
        $args = array(
            'post_type' => 'real_estate',
            'posts_per_page' => 5,
            'paged' => isset($_POST['paged']) ? intval($_POST['paged']) : 1,
        );

        // Adding filters to fields
				if (!empty($_POST['house_name'])) {
					$args['s'] = sanitize_text_field($_POST['house_name']);
				}
				if (!empty($_POST['location_coordinates'])) {
					$coordinates = sanitize_text_field($_POST['location_coordinates']);
					$args['meta_query'][] = array(
							'key' => 'location_coordinates',
							'value' => $coordinates,
							'compare' => '='
					);
				}
				if (!empty($_POST['floors'])) {
						$args['meta_query'][] = array(
								'key' => 'floors',
								'value' => intval($_POST['floors']),
								'compare' => '='
						);
				}
				if (!empty($_POST['building_type'])) {
						$args['meta_query'][] = array(
								'key' => 'building_type',
								'value' => sanitize_text_field($_POST['building_type']),
								'compare' => '='
						);
				}
				if (!empty($_POST['ecological_rating'])) {
						$args['meta_query'][] = array(
								'key' => 'ecological_rating',
								'value' => intval($_POST['ecological_rating']),
								'compare' => '='
						);
				}
				if (!empty($_POST['area'])) {
						$args['meta_query'][] = array(
								'key' => 'rooms_area',
								'value' => sanitize_text_field($_POST['area']),
								'compare' => '='
						);
				}
				if (!empty($_POST['room_count'])) {
						$args['meta_query'][] = array(
								'key' => 'room_count',
								'value' => intval($_POST['room_count']),
								'compare' => '='
						);
				}
				if (!empty($_POST['balcony'])) {
						$args['meta_query'][] = array(
								'key' => 'rooms_balcony',
								'value' => sanitize_text_field($_POST['balcony']),
								'compare' => '='
						);
				}
				if (!empty($_POST['bathroom'])) {
						$args['meta_query'][] = array(
								'key' => 'rooms_bathroom',
								'value' => sanitize_text_field($_POST['bathroom']),
								'compare' => '='
						);
				}

        $query = new WP_Query($args);
				if ($query->have_posts()) {
					// Open a common container for all items
					echo '<div class="row">'; 

					while ($query->have_posts()) {
							$query->the_post();
							// Show post information
							echo '<div class="col-md-4 mb-4 d-flex">'; 
							
							echo '<div class="card flex-fill">'; 
							echo '<div class="card-body">';
							
							// Get image from user field
							$property_image = get_field('field_images');

							// Check if there is an image
							if ($property_image) {
									echo '<img src="' . esc_url($property_image['url']) . '" class="card-img-top mb-3" alt="' . get_the_title() . '">'; 
							}

							echo '<h5 class="card-title">' . get_the_title() . '</h5>';
							echo '<p class="card-text">' . get_the_excerpt() . '</p>';
							echo '<a href="' . get_permalink() . '" class="btn btn-primary">Детальніше</a>';
							echo '</div>'; 
							echo '</div>'; 
							echo '</div>';
					}

					echo '</div>';

					// Page navigation
					$total_pages = $query->max_num_pages;
					if ($total_pages > 1) {
							echo '<div class="pagination mt-3">';
							for ($i = 1; $i <= $total_pages; $i++) {
									echo '<a href="#" class="page-link" data-page="' . $i . '">' . $i . '</a>';
							}
							echo '</div>';
					}
			} else {
					echo 'No objects found.';
			}
			wp_reset_postdata();
			wp_die();
    }

}
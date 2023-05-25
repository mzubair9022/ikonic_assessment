<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );
// END ENQUEUE PARENT ACTION


// Child Theme Enqueue
function child_theme_enqueue_styles() {
	// wp_enqueue_style( 'child_style', get_stylesheet_uri() );
    wp_enqueue_style( 'child_style', get_stylesheet_directory_uri().'/style.css' );
	wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri().'/js/custom.js', array( 'jquery' ), time(), true );
    wp_localize_script( 'custom-js', 'js_data', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );


// Bootstrap Config
function enqueue_bootstrap() {
    wp_enqueue_style('bootstrap', 'https://cdn.example.com/bootstrap.min.css', );
    wp_enqueue_script('bootstrap', 'https://cdn.example.com/bootstrap.min.js', true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

// Register Menu
// function theme_name_register_menu() {
//     register_nav_menu('menu_location', 'Main Menu');
// }
// add_action('after_setup_theme', 'theme_name_register_menu');


// 3. Hook into the template redirect action
function ip_redirect_check() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $ip_prefix = '77.29';

    if (substr($ip, 0, strlen($ip_prefix)) === $ip_prefix) {
        wp_redirect('https://google.com/');
        exit;
    }
}
add_action('template_redirect', 'ip_redirect_check');


// 4. Register the "Projects" post type
function register_projects_post_type() {
    $args = array(
        'labels'        => array(
                            'name'               => 'Projects',
                            'singular_name'      => 'Project',
                            'add_new'            => 'Add New',
                            'add_new_item'       => 'Add New Project',
                            'edit_item'          => 'Edit Project',
                            'new_item'           => 'New Project',
                            'view_item'          => 'View Project',
                            'search_items'       => 'Search Projects',
                            'not_found'          => 'No projects found',
                            'not_found_in_trash' => 'No projects found in trash',
                            'parent_item_colon'  => 'Parent Project:',
                            'menu_name'          => 'Projects',
                            ),
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => array(
                            'title',
                            'editor',
                            'thumbnail',
                            'excerpt' 
                            ),
        'rewrite'       => array(
                            'slug' => 'projects'
                            ),
    );

    register_post_type( 'project', $args );
}
add_action( 'init', 'register_projects_post_type' );


// 5. Register the "Project Type" taxonomy for "Projects" post type
function register_project_type_taxonomy() {
    $args = array(
        'labels'        => array(
                            'name'                       => 'Project Types',
                            'singular_name'              => 'Project Type',
                            'search_items'               => 'Search Project Types',
                            'popular_items'              => 'Popular Project Types',
                            'all_items'                  => 'All Project Types',
                            'edit_item'                  => 'Edit Project Type',
                            'view_item'                  => 'View Project Type',
                            'update_item'                => 'Update Project Type',
                            'add_new_item'               => 'Add New Project Type',
                            'new_item_name'              => 'New Project Type Name',
                            'separate_items_with_commas' => 'Separate project types with commas',
                            'add_or_remove_items'        => 'Add or remove project types',
                            'choose_from_most_used'      => 'Choose from the most used project types',
                            'menu_name'                  => 'Project Types',
                            ),
        'hierarchical'  => true,
        'public'        => true,
        'rewrite'       => array( 'slug' => 'project-type' ),
    );

    register_taxonomy( 'project-type', 'project', $args );
}
add_action( 'init', 'register_project_type_taxonomy' );


// Register Project Archive URL
function custom_projects_archive_rewrite_rule() {
    add_rewrite_rule('^projects-archive/?$', 'index.php?post_type=project', 'top');
}
add_action('init', 'custom_projects_archive_rewrite_rule');




// 6. AJAX handler function
function my_ajax_handler() {

    if(is_user_logged_in()){
        $postperpage = 6;
    }else{
        $postperpage = 3;
    }

    $args = array(
        'post_type'      => 'project',
        'posts_per_page' => $postperpage, 
        'tax_query'      => array(
            array(
                'taxonomy' => 'project-type', 
                'field'    => 'slug',
                'terms'    => 'architecture', 
            )
        ),
        'orderby'        => 'ID',
        'order'          => 'DESC',
    );
    
    $projects = get_posts( $args );
    
    if ( count($projects) > 0 ) {
        foreach ( $projects as $project ) {
            setup_postdata( $project );
            $datasingle[] = $project;
        }
        wp_reset_postdata();

        $response = array(
            'success' => true,
            'data' => $datasingle,
            'message' => 'records found',
        );
    } else {
        $response = array(
            'success' => true,
            'data' => [],
            'message' => 'No records found',
        );
    }

    wp_send_json( $response );
}
add_action( 'wp_ajax_get_project_posts', 'my_ajax_handler' );
add_action( 'wp_ajax_nopriv_get_project_posts', 'my_ajax_handler' );



// 7. RANDOM COFFEE API JSON
function hs_give_me_coffee() {
    $api_url = 'https://coffee.alexflipnote.dev/random.json'; 
    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
        return 'Sorry, unable to get coffee right now.';
    }

    $body = wp_remote_retrieve_body( $response );
    $coffee_data = json_decode( $body, true );

    if ( isset( $coffee_data['file'] ) ) {
        $coffee_link = $coffee_data['file'];
        return $coffee_link;
    } else {
        return 'No coffee link found.';
    }
}


// 8. Kanye Quotes function
function kanye_quotes() {
    $api_url = 'https://api.kanye.rest/';

    echo '<ul>';
    for ($i = 0; $i < 5; $i++) {
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            echo 'Sorry, unable to fetch Kanye quotes right now.';
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $quote_data = json_decode($body, true);
        if (isset($quote_data['quote'])) {
            $quote = $quote_data['quote'];
            echo '<li>' . $quote . '</li>';
        } else {
            echo 'No quote found.';
        }
    }
    echo '</ul>';
}

// Shortcode to call kanye_quotes() to get Quotes 
function kanye_quotes_shortcode() {
    ob_start();
    echo "<h3 class='coffee-cup'>Cup of Coffee</h3>";
    $coffee_link = hs_give_me_coffee();
    echo '<a href="' . $coffee_link . '" class="coffee-cup-url btn">Get a cup of coffee</a><br/><hr>';


    echo "<h3 class='kanye-quotes'>Kanye Quotes</h3>";
    kanye_quotes(); 

    $output = ob_get_clean(); 
    return $output; 
}
add_shortcode( 'kanye_quotes', 'kanye_quotes_shortcode' );



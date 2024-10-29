<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Ajax_SearchWP {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'ajax_searchwp_enqueue_scripts'));
        add_action('wp_ajax_ajax_searchwp_handle_search', array($this, 'ajax_searchwp_handle_search'));
        add_action('wp_ajax_nopriv_ajax_searchwp_handle_search', array($this, 'ajax_searchwp_handle_search'));
        add_shortcode('ajax_searchwp', array($this, 'ajax_searchwp_search_form_shortcode'));
    }
    
    public function ajax_searchwp_enqueue_scripts() {
        if (!is_admin()) {
            wp_enqueue_style('ajax_searchwp_css', AJAX_SEARCHWP_URL . 'assets/css/ajax-searchwp.css', array(), '1.2.0');
            wp_enqueue_script('ajax_searchwp_js', AJAX_SEARCHWP_URL . 'assets/js/ajax-searchwp.js', array('jquery'), '1.2.0', true);
            wp_localize_script('ajax_searchwp_js', 'ajax_searchwp_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'no_results_text' => get_option('ajax_searchwp_no_results_text', __('No results found', 'ajax-searchwp')),
                'ajax_nonce' => wp_create_nonce('ajax_searchwp_nonce')
            ));
        }
    }
    
    public function ajax_searchwp_handle_search() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax_searchwp_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed' ), 403 );
            wp_die();
        }
    
        $query      = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
        $post_types = get_option( 'ajax_searchwp_post_types', array( 'post' ) );
        $limit      = get_option('ajax_searchwp_limit', 5);
        if ( ! is_array( $post_types ) ) {
            $post_types = array( 'post' );
        }
    
        $args = array(
            's' => $query,
            'post_type' => array_map( 'sanitize_key', $post_types ),
            'posts_per_page' => $limit
        );
    
        $search_query = new WP_Query( $args );
        $results = array();
    
        if ( $search_query->have_posts() ) {
            while ( $search_query->have_posts() ) {
                $search_query->the_post();
                $results[] = array(
                    'title' => get_the_title(),
                    'url' => get_permalink()
                );
            }
        }
    
        wp_send_json_success( $results );
        wp_die();
    }
    
    public function ajax_searchwp_search_form_shortcode() {
        ob_start();
        $search_placeholder = get_option('ajax_searchwp_search_placeholder', 'Search here');
        ?>
        <div class="searchwp-form">
            <form id="searchwpform" method="post" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="text" id="s" name="s" placeholder="<?php echo $search_placeholder; ?>" class="" autocomplete="off" />
                <input type="hidden" name="ajax_searchwp_nonce" value="<?php echo esc_attr(wp_create_nonce('ajax_searchwp_nonce')); ?>" />
                <button type="submit" id="searchsubmit">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="22" height="22" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
                        <path
                            d="M460.355,421.59L353.844,315.078c20.041-27.553,31.885-61.437,31.885-98.037
                            C385.729,124.934,310.793,50,218.686,50C126.58,50,51.645,124.934,51.645,217.041c0,92.106,74.936,167.041,167.041,167.041
                            c34.912,0,67.352-10.773,94.184-29.158L419.945,462L460.355,421.59z M100.631,217.041c0-65.096,52.959-118.056,118.055-118.056
                            c65.098,0,118.057,52.959,118.057,118.056c0,65.096-52.959,118.056-118.057,118.056C153.59,335.097,100.631,282.137,100.631,217.041
                            z"
                        ></path>
                    </svg>
                </button>
            </form>
        </div>
        <div id="ajax_searchwp_results"></div>
        <?php
        return ob_get_clean();
    }    
}

?>


<?php

/*
|--------------------------------------------------------------------------
| Date helper -  Format french date litteraly into english date
|--------------------------------------------------------------------------
*/
function date_in_english() {
  global $post;
  $date_limite = get_post_meta( $post->ID, '_date_limite', true); //retrieve from post meta >ordpress as example

  $the_date = preg_replace_callback('/(\s+)((janvier|février|mars|avril|mai|juin|juillet|août|septembre|octobre|novembre|décembre).*?)(\s+)/i', function ($matches) {
    switch ($matches[3]) {
      case 'janvier':
      return 'January';
      case 'février':
      return 'February';
      case 'mars':
      return 'March';
      case 'avril':
      return 'April';
      case 'mai':
      return 'May';
      case 'juin':
      return 'June';
      case 'juillet':
      return 'July';
      case 'août':
      return 'August';
      case 'septembre':
      return 'September';
      case 'octobre':
      return 'October';
      case 'novembre':
      return 'November';
      case 'décembre':
      return 'December';
    }
    return $matches[0];
  }, $date_limite);

    if( !empty($the_date) ) {
    $date = DateTime::createFromFormat('j F Y', $the_date);
    return $date->format( 'Y-m-d');
    }
}
/*
|--------------------------------------------------------------------------
| Return number of day left before the previous date
|--------------------------------------------------------------------------
*/
  function day_left() {
    $expire = strtotime(date_in_english());
    $now = time();
    $day_left = $expire - $now;
    return round($day_left / (60 * 60 * 24));
}

/*
|--------------------------------------------------------------------------
| Display today date in french format
|--------------------------------------------------------------------------
*/
setlocale (LC_TIME, 'fr_FR.utf8','fra');
  $t = strftime("%d %B %G");
echo $t;


/*
|--------------------------------------------------------------------------
| WORDPRESS REST API HELPER - Register custom endpoint for a post type and return some data on it
|--------------------------------------------------------------------------
*/

add_action( 'rest_api_init', 'data_route' );

function offers_route() {
    register_rest_route( 'datanamespace/v1', 'data', array(
                    'methods' => 'GET',
                    'callback' => 'get_data',
                    'permission_callback' => '__return_true',
                )
            );
}


//Return data of offers on this route
function get_data() {

   $args = array(
    'post_type'          => 'post_type',
    'posts_per_page'     => 12,
    'meta_key'           => '_meta_key_need_for_comparison',
    'meta_type'          => 'meta_type_need',
    'meta_query'         => array(
        array(
          'key' => '_meta_key_need_for_comparison',
          'value' => 0,
          'compare' => '>=',
          'type'    => 'numeric',
        )
     ),
    'paged'              => ($_REQUEST['paged'] ? $_REQUEST['paged'] : 1)
);

    $query = new WP_Query( $args );
    $posts = get_posts($args);

    $output = array();
    $total_posts = $query->found_posts;
    $max_pages = $query->max_num_pages;

    foreach( $posts as $post ) {

        $output[] = array(
          'id'          => $post->ID,
          'title'       => $post->post_title,
          'content'     => $post->post_content,
          'link'        => get_permalink($post->ID),
          'custom_taxonomy'        => get_the_terms($post->ID, 'custom_taxonomy'),
          'post_type_meta'  => get_post_meta($post->ID)  //here return all metafields created for post type
          );

    }
    $response = new WP_REST_Response( $output, 200 );
    $response->header( 'X-WP-Total', (int) $total_posts );
    $response->header( 'X-WP-TotalPages', (int) $max_pages );
    // ...
    return $response;

}

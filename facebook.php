<?php
add_action( 'wp_ajax_get_getNumberOfFbFollowers', 'getNumberOfFbFollowers' );
add_action( 'wp_ajax_nopriv_get_getNumberOfFbFollowers', 'getNumberOfFbFollowers' );
function getNumberOfFbFollowers () {
  $args = array(
    'posts_per_page' => -1,
    'post_type' => 'artist',
    'orderby' => 'name',
    'order' => 'ASC'
  );

  $the_query_ = new WP_Query($args);
  if($the_query_->have_posts()) { 
    $i = 0;
    $count = $the_query_->found_posts;
    // for ($i; $i < 10000; $i++) {      // it usefull when maximum time of script is limited
    for ($i; $i < $count; $i++) {
      $the_query_->the_post();
      // if ($i < 10000) {      // it usefull when maximum time of script is limited
      //   continue;
      // }
      $postTitle = get_the_title();
      $postID = get_the_ID();
      if(get_field( 'facebook', $postID)) {
        $facebookUrl = get_field( 'facebook', $postID);
        ob_start();
        echo '<script>console.log("';
        do_shortcode('[aps-get-count social_media="facebook" slug="'.str_replace(array('en-gb'),'www',($facebookUrl)).'"]');
        echo '");</script>';
        $cacheValue = ob_get_contents();
        ob_end_clean();
        if ($cacheValue) {
          $keywords  = preg_split('{\(\"}', $cacheValue);
          $keywords  = preg_split('{\"\)}', $keywords[1]);
          $keywords = $keywords[0];
          $followersFbAmount = floatval($keywords);
          if(strpos($keywords, 'K')) {
            $followersFbAmount = $followersFbAmount * 1000;
          }
          elseif(strpos($keywords, 'M')) {
            $followersFbAmount = $followersFbAmount * 1000000;
          }
        }
        $tagId = 0;
        if (0 < $followersFbAmount && $followersFbAmount <= 50000) {
          $tagId = 5316;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 0-50k' . '`);</script>';
        }
        elseif (50000 < $followersFbAmount && $followersFbAmount <= 100000) {
          $tagId = 5317;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 50k-100k' . '`);</script>';
        }
        elseif (100000 < $followersFbAmount && $followersFbAmount <= 500000) {
          $tagId = 5318;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 100k-500k' . '`);</script>';
        }
        elseif (500000 < $followersFbAmount && $followersFbAmount <= 1000000) {
          $tagId = 5319;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 500k-1m' . '`);</script>';
        }
        elseif (1000000 < $followersFbAmount && $followersFbAmount <= 10000000) {
          $tagId = 5320;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 1m-10m' . '`);</script>';
        }
        elseif (10000000 < $followersFbAmount) {
          $tagId = 5321;
          echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersFbAmount . '========== 10m+' . '`);</script>';
        }
        if ($tagId) {
          wp_set_post_terms($postID, $tagId, 'followersFB');
          echo '<script>console.log("success");</script>';
        }
        $termFbValue = get_the_terms($postID, 'followersFB');
        if( is_array( $termFbValue ) ) {  
          echo '<br>now this post has following value of followersFB taxonomy: ';
          foreach( $termFbValue as $termFbValueOne ){
            echo $termFbValueOne->name;
          }
        }
        // 5316 0-50
        // 5317 50-100
        // 5318 100-500
        // 5319 500-1000
        // 5320 1000-10000
        // 5321 10000+
      }
      // echo '<br>===============================NEXT==========================================<br><br>';
      echo '<br><br>';
    } // endwhile
    wp_reset_query ();
    wp_reset_postdata();
  } // endif
}
?>

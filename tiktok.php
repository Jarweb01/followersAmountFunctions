<?php

function scrappingTiktok($url){
  $referer = 'http://www.google.com';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36");
  curl_setopt($ch, CURLOPT_REFERER, $referer);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  curl_close($ch);
  $html = preg_split('{followers-count">}', $data);
  $html = preg_split('{</strong><span}', $html[1]);
  $html = strip_tags($html[0]);
  return $html;
}


add_action( 'wp_ajax_get_getNumberOfTiktokFollowers', 'getNumberOfTiktokFollowers' );
add_action( 'wp_ajax_nopriv_get_getNumberOfTiktokFollowers', 'getNumberOfTiktokFollowers' );
function getNumberOfTiktokFollowers () {
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
    for ($i; $i < $count; $i++) {
      $the_query_->the_post();
      // if ($i < 0) {    // it usefull when maximum time of script is limited
      //   continue;
      // }
        $postTitle = get_the_title();
        $postID = get_the_ID();
        if(get_field( 'tiktok', $postID)) {
          $tiktokUrl = get_field( 'tiktok', $postID);
          $followersTiktokAmount = scrappingTiktok($tiktokUrl);
          $followersTiktokAmountInt = floatval($followersTiktokAmount);
          if(strpos($followersTiktokAmount, 'K')) {
            $followersTiktokAmountInt = $followersTiktokAmountInt * 1000;
          }
          elseif(strpos($followersTiktokAmount, 'M')) {
            $followersTiktokAmountInt = $followersTiktokAmountInt * 1000000;
          }
          $tagId = 0;
          if (0 < $followersTiktokAmountInt && $followersTiktokAmountInt <= 50000) {
            $tagId = 5404;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 0-50k' . '`);</script>';
          }
          elseif (50000 < $followersTiktokAmountInt && $followersTiktokAmountInt <= 100000) {
            $tagId = 5405;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 50k-100k' . '`);</script>';
          }
          elseif (100000 < $followersTiktokAmountInt && $followersTiktokAmountInt <= 500000) {
            $tagId = 5406;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 100k-500k' . '`);</script>';
          }
          elseif (500000 < $followersTiktokAmountInt && $followersTiktokAmountInt <= 1000000) {
            $tagId = 5407;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 500k-1m' . '`);</script>';
          }
          elseif (1000000 < $followersTiktokAmountInt && $followersTiktokAmountInt <= 10000000) {
            $tagId = 5408;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 1m-10m' . '`);</script>';
          }
          elseif (10000000 < $followersTiktokAmountInt) {
            $tagId = 5409;
            echo '<script>console.log(`' . $postTitle . '\n==========================' . $followersTiktokAmountInt . '========== 10m+' . '`);</script>';
          }
          if ($tagId) {
            wp_set_post_terms($postID, $tagId, 'followersTiktok');
            echo '<script>console.log("success");</script>';
          }
          $termTiktokValue = get_the_terms($postID, 'followersTiktok');
          if( is_array( $termTiktokValue ) ) {  
            echo '<br>now this post has following value of followersFB taxonomy: ';
            foreach( $termTiktokValue as $termTiktokValueOne ){
              echo $termTiktokValueOne->name . '<br>';
            }
          }
          //         // 5404 0-50
          //         // 5405 50-100
          //         // 5406 100-500
          //         // 5407 500-1000
          //         // 5408 1000-10000
          //         // 5409 10000+
        }
      // } // endwhile
    } // end for loop
    wp_reset_query ();
    wp_reset_postdata();
  } // endif
}
?>

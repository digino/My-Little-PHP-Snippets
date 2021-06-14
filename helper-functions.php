
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

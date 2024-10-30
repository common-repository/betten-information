<?php
/*
Plugin Name: Betten Information
Plugin URI: http://wordpress.org/extend/plugins/betten-information/
Description: Adds a customizeable widget which displays the latest Betten information by http://www.betten.de/
Version: 1.0
Author: Thomas Buchwald
Author URI: http://www.betten.de/
License: GPL3
*/

function bettennews()
{
  $options = get_option("widget_bettennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Betten Information',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=bett&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_bettennews($args)
{
  extract($args);
  
  $options = get_option("widget_bettennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Betten Information',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  bettennews();
  echo $after_widget;
}

function bettennews_control()
{
  $options = get_option("widget_bettennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Betten Information',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['bettennews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['bettennews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['bettennews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['bettennews-CharCount']);
    update_option("widget_bettennews", $options);
  }
?> 
  <p>
    <label for="bettennews-WidgetTitle">Widget Title: </label>
    <input type="text" id="bettennews-WidgetTitle" name="bettennews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="bettennews-NewsCount">Max. News: </label>
    <input type="text" id="bettennews-NewsCount" name="bettennews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="bettennews-CharCount">Max. Characters: </label>
    <input type="text" id="bettennews-CharCount" name="bettennews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="bettennews-Submit"  name="bettennews-Submit" value="1" />
  </p>
  
<?php
}

function bettennews_init()
{
  register_sidebar_widget(__('Betten Information'), 'widget_bettennews');    
  register_widget_control('Betten Information', 'bettennews_control', 300, 200);
}
add_action("plugins_loaded", "bettennews_init");
?>
<?php
/*
Plugin Name: Events plugin
Plugin URI: https://exampledomain.org/plugins/events/
Description: Renders a list of events
Version: 0.1.0
Author: Tavo Parra
*/

class Events {
  public function __construct(){
    add_filter('the_content', array($this, 'addEventInfo'));
    add_shortcode('twitterlink', array($this, 'insertTwitterLink'));
    add_action( 'init', [$this, 'create_post_type'] );
    wp_register_style(
      'events-styles',
      plugin_dir_url(__FILE__).'css/styles.css'
    );
  }

  public function create_post_type() {
    register_post_type( 'events',
      array(
        'labels' => array(
          'name' => 'Event lists',
          'singular_name' => 'Events list'
        ),
        'public' => true,
        'has_archive' => false,
      )
    );
  }

  public function insertTwitterLink($attr) {
    $attr = shortcode_atts(['username' => 'tavo_pa', 'text' => 'Click here!!!'], $attr);
    return '<div><a href="https://www.twitter.com/'.$attr['username'].'" target="_blank">'.$attr['text'].'</a></div>';
  }

  public function addEventInfo($content) {
    if (is_single()) {
      $content .= '<h2>Events</h2>';

      $events = $this->getDataFromAPI();

      wp_enqueue_style('events-styles');
      foreach($events['items'] as $event) {
        $content .= '<div class="event-info">'.$event['name'].'</div>';
      }
    }
  
    return $content;
  }

  private function getDataFromAPI() {
    $api_request = 'http://159.89.138.233/events?page=1&size=10';
    $api_response = wp_remote_get( $api_request );
    $api_data = json_decode( wp_remote_retrieve_body( $api_response ), true );

    return $api_data;
  }

  
}

$events = new Events();
<?php
/*
Plugin Name: NFL Scores Shortcode
Description: Uses the ESPN API to fetch NFL scores and display them using a shortcode
Version: 1.0
Author: Chad Lane
*/
function wpse_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'nflstyle', $plugin_url . 'nfl-scores.css' );
}
add_action( 'wp_enqueue_scripts', 'wpse_load_plugin_css' );
function nfl_scores_shortcode($atts) {
    $atts = shortcode_atts(array(
        'logos' => 'false',
        'abbreviation' => 'false',
    ), $atts);
    $url = 'http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard';
    $response = wp_remote_get($url);
    $data = json_decode(wp_remote_retrieve_body($response), true);
    $output = '<div class="nfl-scores">';
    foreach ($data['events'] as $event) {
        $home_team = $event['competitions'][0]['competitors'][0]['team'];
        $away_team = $event['competitions'][0]['competitors'][1]['team'];
        $home_score = $event['competitions'][0]['competitors'][0]['score'];
        $away_score = $event['competitions'][0]['competitors'][1]['score'];
        $output .= '<div class="game">';
        if ($atts['logos'] == 'true') {
            $output .= '<img class="team-logo" src="' . $home_team['logo'] . '" />';
        }
        if ($atts['abbreviation'] == 'true') {
            $output .= $home_team['abbreviation'] . ' <span class="score">' . $home_score . '</span>';
        } else {
            $output .= $home_team['displayName'] . ' <span class="score">' . $home_score . '</span>';
            
        }
        if ($home_score > $away_score) {
            $output .= '<span class="winner"> (W)</span>';
        } else {
            $output .= '';
        }
        $output .= ' vs ';
        if ($atts['logos'] == 'true') {
            $output .= '<img class="team-logo" src="' . $away_team['logo'] . '" />';
        }
        if ($atts['abbreviation'] == 'true') {
            $output .= $away_team['abbreviation'] . ' <span class="score">' . $away_score . '</span>';
        } else {
            $output .= $away_team['displayName'] . ' <span class="score">' . $away_score . '</span>';
        }
        if ($away_score > $home_score) {
            $output .= '<span class="winner"> (W)</span>';
        } else {
            $output .= '';
        }
        $output .= '</div>';
    }
    $output .= '</div>';
    return $output;
}
add_shortcode('nfl_scores', 'nfl_scores_shortcode');
?>

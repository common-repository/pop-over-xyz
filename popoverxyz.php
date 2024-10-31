<?php
/*
Plugin Name: Pop Over XYZ
Plugin URI: https://www.popover.xyz
Description: Show Custom Aligned Tool Tips Windows Over Any Content
Version: 1.0.1
Author: Webmuehle e.U.
Author URI: https://www.webmuehle.at
License: GPL2
*/
/*  Copyright 2021 Webmuehle e.U.  (email : office@webmuehle.at)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Integration Freemius SDK
if ( ! function_exists( 'pop_fs' ) ) {
    // Create a helper function for easy SDK access.
    function pop_fs() {
        global $pop_fs;

        if ( ! isset( $pop_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $pop_fs = fs_dynamic_init( array(
                'id'                  => '8736',
                'slug'                => 'popover',
                'type'                => 'plugin',
                'public_key'          => 'pk_a64b756f45be48a25c513b48b769a',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'popover',
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $pop_fs;
    }

    // Init Freemius.
    pop_fs();
    // Signal that SDK was initiated.
    do_action( 'pop_fs_loaded' );
}

global $wpdb;

add_action('admin_menu', 'popoverxyz_top_menu');

function popoverxyz_top_menu() {
add_menu_page('PopOverXYZ', 'PopOverXYZ', 'read', 'popoverxyz_slug', 'popoverxyz_mainpage');
}

function popoverxyz_mainpage() {
global $wpdb;
?>
<div align="center">
<p align="center"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>/slogo.PNG"></p>
<h2 align="center">Pop Over XYZ Plugin By ARA Web Services</h2>
Thanks for installing the plugin. Use instructions below to generate custom aligned pop over windows over any text.
<br><br>
Use tags [popxyz1]...[/popxyz1] to show pop over windows up to ten instances (that is, up to [popxyz10]...[/popxyz10]).
<br><br>
In place of .... you have to provide options in the format below.
<br><br>
[popxyz1]<strong>[linktxt]</strong>Link Text<strong>[/linktxt][wintxt]</strong>Text In Pop Over Window<strong>[/wintxt][wintitle]</strong>Pop Over Window Title<strong>[/wintitle][winalign]</strong>Pop Over Window Alignment<strong>[/winalign]</strong>[/popxyz1]
<br><br>
where<br><br>
<strong>Link Text:</strong> Any word on which you wish to generate a pop over window upon click.
<br><br>
<strong>Text In Pop Over Window:</strong> Whatever text you wish to show in the pop over window.
<br><br>
<strong>Pop Over Window Title:</strong> Title Of Pop Over Window, Default "Is Learn More".
<br><br>
<strong>Pop Over Window Alignment:</strong> Can be any of the following values: auto, top, bottom, right & left, default is auto as it works best.
<br><br>
For any help, email support@popover.xyz<br>
</div>
<?php
}


function popoverxyz_in_content($content) {
global $wpdb;

if (!function_exists('popoverxyz_ara_get_string_between'))   {
function popoverxyz_ara_get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
}

$i = 1;
while($i<11)
{
$popcheckstart = '[popxyz'.$i.']';
$popcheckend = '[/popxyz'.$i.']';

$fullstring = $content;
$parsed = popoverxyz_ara_get_string_between($fullstring, $popcheckstart, $popcheckend);

$fullstring1 = $parsed;

$wintext = popoverxyz_ara_get_string_between($fullstring1, '[wintxt]', '[/wintxt]');
$linktext = popoverxyz_ara_get_string_between($fullstring1, '[linktxt]', '[/linktxt]');
$winalign = popoverxyz_ara_get_string_between($fullstring1, '[winalign]', '[/winalign]');
$wintitle = popoverxyz_ara_get_string_between($fullstring1, '[wintitle]', '[/wintitle]');

if($wintext=="")
$wintext = "test";
if($linktext=="")
$linktext = "test";
if($wintitle=="")
$wintitle = "Learn More";
if($winalign=="")
$winalign = "auto";

$popo = $linktext;
$popc = $wintext;
$popt = $wintitle;
$popp = $winalign;

$popoverrep = '<a href="javascript://" data-toggle="popover" data-placement="'.$popp.'" title="'.$popt.'" data-content="'.$popc.'">'.$popo.'</a>';
$content = str_replace($popcheckstart.$parsed.$popcheckend,$popoverrep,$content);
$i = $i + 1;
}

return($content);

}

add_filter('the_content', 'popoverxyz_in_content');


add_action( 'wp_footer', 'popoverxyz_addinfoot');

function popoverxyz_addinfoot() {
global $wpdb;

$arafoottext = '<script>
jQuery(document).ready(function(){
    jQuery(\'[data-toggle="popover"]\').popover({
  trigger: \'hover\'
});   
});
</script>';

echo($arafoottext);

}

function popoverxyz_add_my_script() {
wp_enqueue_script( 'popper-js', plugin_dir_url( __FILE__ ).'customs/popper.min.js', array('jquery'), NULL, false );
wp_enqueue_script('custom-script', plugin_dir_url( __FILE__ ).'customs/js/bootstrap.min.js', array('jquery'), null, false);
wp_enqueue_style( 'custom-style',  plugin_dir_url( __FILE__ ) . "customs/css/bootstrap.min.css");

}

add_action( 'wp_enqueue_scripts', 'popoverxyz_add_my_script' );
?>

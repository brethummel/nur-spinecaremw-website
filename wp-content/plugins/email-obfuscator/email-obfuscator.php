<?php
/*
Plugin Name: Email Obfuscator
Plugin URI: http://wordpress.org/extend/plugins/email-obfuscator/
Description: Obfuscates email addresses and mailto: links in your content using ROT13.
Version: 0.4.6
Author: Daniel Hong <Amagine, Inc.>
Author URI: http://amagine.net/
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Not used yet, but defining it anyways
define('AI_EO_VERSION', '0.4.6');

/**
 * Add our ROT13 JavaScript
 */
function ai_eo_javascript()
{
    wp_enqueue_script('ai_eo_rot13', plugins_url('rot13.js', __FILE__), array(), null);
}
add_action('wp_enqueue_scripts', 'ai_eo_javascript');

/**
 * Finds and replaces email addresses with ROT13 encoding
 * 
 * @param type $content
 * @return type 
 */
function ai_eo_obfuscate($content)
{
    $email_pattern = '([A-Za-z0-9._%-]+)\@([A-Za-z0-9._%-]+)\.([A-Za-z0-9._%-]+)';
    $mailto_pattern = '#(\<a[^>]*?href=\"mailto:\s?|)' . $email_pattern . '([^>]*?\>[^>]*?<\/a\>|)#';

    return preg_replace_callback($mailto_pattern, 'ai_rot13_callback', $content);
}
add_filter('the_content', 'ai_eo_obfuscate');

/**
 * Our preg_replace_callback function
 * 
 * @param type $matches
 * @return type 
 */
function ai_rot13_callback($matches)
{
    $script = "<script>document.write(str_rot13('" . str_replace("'", "\'", str_rot13($matches[0])) . "'));</script>";
    
    // If the anchor tag in match, assume mailto link
    if (stristr('<a', $matches[0]) !== false) {
        return $script . "<noscript>{$matches[3]} AT {$matches[4]} DOT {$matches[5]}</noscript>";
    }
    
    return $script . "<noscript>{$matches[2]} AT {$matches[3]} DOT {$matches[4]}</noscript>";
}

if (! function_exists('obfuscate_email')) {
    /**
     * Template tag function alias to ai_eo_obfuscate()
     * 
     * @param type $content
     * @return type 
     */
    function obfuscate_email($content)
    {
        return ai_eo_obfuscate($content);
    }
}
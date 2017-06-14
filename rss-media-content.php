<?php

/*
 * Plugin Name: RSS Media Content
 * Description: Add a post's featured image to the RSS media:content field for that item.
 * Version:     1.0.0
 * Plugin URI:  https://github.com/bhalash/rss-media-content
 * Author:      Mark Grealish
 * Author URI:  https://www.bhalash.com/
 * Text Domain: rss-media-content
 * Domain Path: /languages/
 * License:     GPL v2 or later
 *
 * Copyright 2017 Mark Grealish
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('ABSPATH') or die();

function rss_media_content() {
    global $post;

    if (has_post_thumbnail($post->ID)) {
        $image_metadata = wp_get_attachment_metadata(get_post_thumbnail_id($post->ID));
        $author = get_userdata($post->post_author)->display_name;

        // Image description.
        $description = printf('<media:description type="plain"><![CDATA[%s]]></media:description>', $post->post_title);
        // Post creator.
        $copyright = printf('<media:copyright>%s</media:copyright>', $author);

        printf('<media:content url="%s" type="%s" medium="image" width="%s" height="%s">%s%s</media:content>',
            wp_get_attachment_url(get_post_thumbnail_id($post->ID)), // src
            $image_metadata['sizes']['thumbnail']['mime-type'], // mimetype
            $image_metadata['width'], // width
            $image_metadata['height'], // height
            $description,
            $copyright
        );
    }
}

if (WP_DEBUG === true) {
    // Forcibly clear RSS cache.
    add_action('wp_feed_options', function(&$feed) { $feed->enable_cache(false); });
    add_filter('wp_feed_cache_transient_lifetime', function($seconds) { return 1; });
}

// Hook into RSS feed.
add_filter('rss2_item', 'rss_media_content');

?>

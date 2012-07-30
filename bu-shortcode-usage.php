<?php
/*
Plugin Name: Shortcode Usage
Plugin URI: http://jonasnordstrom.se/plugins/shortcode-usage/
Description: With this plugin you can search through all content and list the posts, pages and CPTs that use a specific shortcode, with direct links to edit each post.
Version: 0.4
Author: Jonas Nordstrom
Author URI: http://jonasnordstrom.se/
*/

/**
 * Copyright (c) 2012 Jonas Nordstrom. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/**
* Shortcode usage
*/
class BuShortcodeUsage {

	private $maxitems; // max number of posts in result list
	
	public function __construct() {
		$this->maxitems = 500;
	}
	public function init() {
		add_management_page( __('Shortcode Usage', 'busu'), __('Shortcode Usage', 'busu'), 'read', 'shortcode-usage', array(&$this, 'shortcode_page') );
	}

	public function shortcode_page() { ?>
		<div class="wrap">
			<div id="icon-plugins" class="icon32"></div>
			<h2><?php _e('Shortcode Usage', 'busu'); ?></h2>

		<?php
		$shortcode = "";
		if ( isset( $_GET['shortcode'] ) ) {
			$shortcode =  $_GET['shortcode'];
		}

		if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'su-search' ) ) {
			if ( ! current_user_can( 'read' ) ) {
				return;
			}
			global $wpdb;
			$q = "select ID, post_title, post_type, post_status, post_date "
				. "from $wpdb->posts "
				. "where post_content like '%[{$shortcode}%' "
				. "and post_status in ('publish', 'draft') "
				. "and post_type not in ('revision', 'attachment', 'nav_menu_item') "
				. "order by post_title "
				. "limit {$this->maxitems}";

			$myrows = $wpdb->get_results( $q );
			if ( ! empty( $myrows ) ) : ?>
				<h2>Usage of shortcode [<?php echo $shortcode; ?>]</h2>
				<table style="margin-bottom: 40px; margin-top: 20px" class="widefat">
				<thead>
					<tr>
						<th><?php _e('Type', 'busu');?></th>
						<th><?php _e('Id', 'busu');?></th>
						<th><?php _e('Title', 'busu');?></th>
						<th><?php _e('Status', 'busu');?></th>
						<th><?php _e('Date', 'busu');?></th>
				    </tr>
				</thead>
				<tbody>
				<?php
				foreach ( $myrows as $row ) : ?>
					<tr>
						<td><?php echo $row->post_type; ?></td>
						<td><?php edit_post_link($row->ID, '', '', $row->ID); ?></td>
						<td><a target="_blank" href="<?php echo get_permalink( $row->ID); ?>"><?php echo $row->post_title; ?></a></td>
						<td><?php echo $row->post_status; ?></td>
						<td><?php echo $row->post_date; ?></td>
					   </tr>

				<?php endforeach; ?>

				</tbody>
				<tfoot>
					<tr>
						<th><?php _e('Type', 'busu');?></th>
						<th><?php _e('Id', 'busu');?></th>
						<th><?php _e('Title', 'busu');?></th>
						<th><?php _e('Status', 'busu');?></th>
						<th><?php _e('Date', 'busu');?></th>
					</tr>
				</tfoot>
				</table>

				<?php
			else : ?>
				<h4><?php printf(__('Shortcode [%s] is not used anywhere.', 'busu'), $shortcode); ?></h4>
				<?php
			endif;
		}
		?>

			<form action="tools.php">
				<input type="hidden" name="page" value="shortcode-usage" />
				<input type="text" name="shortcode" value="<?php echo $shortcode; ?>" />
				<input type="hidden" name="action" value="su-search" />
				<input type="submit" value="<?php _e('Search', 'busu');?>" />
			</form>
			
			<h3>Registered shortcodes</h3>
			<?php
			global $shortcode_tags;
			foreach ($shortcode_tags as $shortcode => $function ) : ?>
				<p>
					<a href="<?php echo esc_url(get_admin_url() . 
						'tools.php?page=shortcode-usage&shortcode=' . 
						$shortcode . 
						'&action=su-search'); ?>"><?php echo $shortcode; ?></a>
				</p>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
$bu_shortcode_usage = new BuShortcodeUsage();

add_action( 'admin_menu', array(&$bu_shortcode_usage, 'init') );
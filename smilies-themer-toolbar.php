<?php
/*
Plugin Name: Smilies Themer Toolbar
Plugin URI: http://polpoinodroidi.com/wordpress-plugins/smilies-themer-toolbar/
Description: Adds a toolbar to easily add to comments and posts your smilies managed by <a href='http://wordpress.org/extend/plugins/smilies-themer/'>Smilies Themer</a> plugin.
Version: 2.0.8
Text Domain: smilies-themer-toolbar
Author: Frasten
Author URI: http://polpoinodroidi.com
License: GPL3
*/

/* TODO:
 * [ ] cache
 * [ ] dinamic image sprites, see sexybookmarks for inspiration
 * */


if ( ! class_exists( 'SmiliesThemerToolbar' ) ) :
class SmiliesThemerToolbar {
	var $plugin_slug = 'smilies-themer-toolbar';
	var $plugin_dir = false;
	var $plugin_dir_url = false;
	var $plugin_url = false;
	var $smilies_path = false;
	var $already_shown = false;
	var $scripts_to_output = array();


	/**
	 * Initializes some variables.
	 */
	function init() {
		global $smilies_themer;
		// Init variables
		$this->plugin_dir = WP_PLUGIN_DIR . '/' . $this->plugin_slug;
		$this->plugin_dir_url = WP_PLUGIN_URL . '/' . $this->plugin_slug;
		$this->plugin_url = $this->plugin_dir_url . '/' . basename( __FILE__ );

		if ( $smilies_themer && $smilies_themer->smilies->url_path )
			$this->smilies_path = $smilies_themer->smilies->url_path;

		/* Check if the theme has changes since the last time. */
		$opt = get_option( 'smilies-themer-toolbar' );
		if ( $opt['old_theme'] != $smilies_themer->current_smilies ) {
			$this->reset_order();
			// The line above edits the options, I need to reload them.
			$opt = get_option( 'smilies-themer-toolbar' );
		}
		$opt['old_theme'] = $smilies_themer->current_smilies;
		update_option( 'smilies-themer-toolbar', $opt );


		/* Load translations, tries to load:
		 * smilies-themer-toolbar/translations/smilies-themer-toolbar-LOCALE.mo */
		load_plugin_textdomain( $this->plugin_slug, false, $this->plugin_slug . '/translations' );


		/* It's loaded everywhere, because some themes/extensions could load
		 * the comment form not only in single.php */
		wp_enqueue_script( 'stt-common', $this->plugin_dir_url . '/stt-common.js', array( 'jquery' ), '2.0', true );

		if ( is_admin() ) {
			// Reset order POST call
			add_action( 'admin_post_stt_resetorder', array( &$this, 'order_reset_request' ) );
		}
	}


	/**
	 * Creates a link to the plugin's configuration page.
	 */
	function admin_menu() {
		global $wp_version;

		if ( current_user_can( 'manage_options' ) ) {
			$menutitle = '';
			if ( version_compare( $wp_version, '2.7alpha', '>' ) ) {
				$menutitle = '<img src="' . $this->get_resource_url('sttoolbar.png') . '" alt="" /> ';
			}
			$menutitle .= __( 'Smilies Toolbar', $this->plugin_slug );
			$page = add_options_page(
			        __( 'Smilies Toolbar', $this->plugin_slug ),
			        $menutitle,
			        'administrator', $this->plugin_slug,
			        array( &$this, 'options_page' )
			);

			/* Using registered $page handle to hook script load */
			add_action( 'admin_print_scripts-' . $page, array( &$this, 'admin_options_scripts' ) );
		}
	}


	/**
	 * Adds a link to the configuration page in the plugins list.
	 * 
	 * Directly called by filter 'plugin_action_links'.
	 */
	function add_action_link( $links, $file ) {
		/* create link */
		if ( $file == plugin_basename( __FILE__ ) ) {
			array_unshift(
				$links,
				sprintf( '<a href="%s">%s</a>', $this->plugin_options_url(), __( 'Settings' ) )
			);
		}

		return $links;
	}


	/**
	 * Returns the url for the configuration page.
	 * 
	 * @return string The URL for the configuration page.
	 */
	function plugin_options_url() {
		// WP >= 3.0: use menu_page_url() if it exists.
		if ( function_exists('menu_page_url') )
			return menu_page_url( $this->plugin_slug, false );

		return admin_url( 'options-general.php?page=' . $this->plugin_slug );
	}


	/**
	 * Display Images/ Icons in base64-encoding
	 * @return $resourceID
	 */
	function get_resource_url( $resourceID ) {
		return trailingslashit( get_bloginfo('url') ) . '?resource=' . $resourceID;
	}


	/**
	 * Prints the icon for this plugin, encoded in base64 format.
	 */
	function print_image( $filename ) {
		$b64 = <<<EOF
iVBORw0KGgoAAAANSUhEUgAAAAsAAAALCAYAAACprHcmAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A
/wD/oL2nkwAAAAlwSFlzAAAN1wAADdcBQiibeAAAAAd0SU1FB9oECwQIAwspx0YAAAGnSURBVBjT
PdE/q9pQAIbx99ykxyDBIbYY0eogKFjbL+Agok5+goKdRHB06Vzu4lIu4nKXkEFwEQe/QsUhY5Eu
tQQs+AdSCCEqAc9Jcjrd+6zP+CNCCLxkmuajEKJBCPkohPhFCFn3+/1vL58IIWCa5lDX9afD4ZD0
fR+tVgubzQaqqkJV1cBxnK+j0ehZBoBMJvNUq9WSlFKk02koioJ2uw3P81AsFpPr9fo7gGcpm80+
1uv1tm3baDab0HUdQgiUSiUUCgVst1tUq9U3k8lEfuCcN1zXBWMMkiRhOBzC8zwMBgMQQsAYw+12
QxzHnx6CIHh7uVxAKQUAGIYBTdNgGAYAIJFI4Hq9IgiCrMw53yuK8sH3fViWBVmWIUkSjscjoihC
FEVQFAVhGP594Jz/TKVSiOMYtm0jDEMwxsA5x263QxRFoJTifr//JkIITKfTf91u951lWTidTnAc
B7lcDvl8HpVKBcvl0hmPx7oMAPv9frZarb50Op1MuVx+RZJlGYvF4nQ+n2evKADQ6/U+E0JqmqY1
KKXvGWNH13V/APgzn89nAPAfehPEacy22kYAAAAASUVORK5CYII=
EOF;
		$content = base64_decode( $b64 );

		$last_mod = filemtime( __FILE__ );
		$client = ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false );
		// Checking if the client is validating his cache and if it is current.
		if ( isset( $client ) && ( strtotime( $client ) == $last_mod ) ) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $last_mod ) . ' GMT', true, 304 );
			exit;
		} else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $last_mod ) . ' GMT', true, 200 );
			header( 'Content-Length: ' . strlen( $content ) );
			header( 'Content-Type: image/' . substr( strrchr( $filename, '.' ), 1 ) );
			echo $content;
			exit;
		}
	}


	/**
	 * Loads needed scripts for admin interface.
	 */
	function admin_options_scripts() {
		if ( ! function_exists( 'has_post_format' ) ) {
			/* ui.sortable.js from jQuery UI v1.7.1 provided with WP <= 3.0 is
			 * buggy. So I'm using a custom version, backporting a fix to bug #4551 */
			wp_enqueue_script( 'sttjQueryUIsortable', $this->plugin_dir_url . '/ui.sortable.js', array( 'jquery-ui-core' ) );
		}
		else {
			// Fixed in WP >= 3.1
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}


	/**
	 * Configuration page for this plugin.
	 * This allows you to rearrange your smilies.
	 */
	function options_page() {
		global $smilies_themer;
		printf( "<div class='wrap'>\n<h2>%s</h2>", __( 'Smilies Themer Toolbar settings', $this->plugin_slug ) );

		if ( ! $smilies_themer ) {
			printf( __( "Smilies Themer is not installed. Download it <a href='%s'>here</a>.", $this->plugin_slug ), 'http://wordpress.org/extend/plugins/smilies-themer' );
			echo '</div>';
			return;
		}
		if ( ! $smilies_themer->use_smilies ) {
			printf( __( "Smilies Themer is installed but not active. <a href='%s'>Activate it now</a>.", $this->plugin_slug ), 'options-general.php?page=smilies-themer/smilies-themer.php' );
			echo '</div>';
			return;
		}

		$sm = $this->get_ordered_list();
		if ( ! $sm || ! is_array( $sm ) ) {
			_e( 'Error while fetching smilies list.', $this->plugin_slug );
			echo '</div>';
			return;
		}

		echo '<p>';
		_e( "Drag and drop the smilies to change their order.", $this->plugin_slug );
		echo '<br />';
		_e( "Move them to the bottom box to hide them: the user can show them by clicking on <em>More</em> in the toolbar.", $this->plugin_slug );
		echo '</p><p><em>';
		_e( "Note: the order is saved on every change.", $this->plugin_slug );
		echo '</em></p>';

		// Loading message, used for visual feedback in ajax operations.
		echo "<div id='stt_ajax_status' style='text-align: right;visibility: hidden'>";
		echo "<img src='{$this->plugin_dir_url}/ajax-loader.gif' alt='Loading' /> ";
		_e( 'Saving...' );
		echo "</div>";

		echo '<h3>' . __( "Visible smilies:", $this->plugin_slug ) . '</h3>';
		echo "<ul id='stt_enabled_smilies' class='sttSortable'>\n";
		// Get the visible smilies in the (eventually) custom order
		$this->__print_ordered_smilies( $sm[0] );
		echo "</ul>\n";

		echo "<br style='clear:both'/>\n";

		echo '<h3>' . __( "Hidden smilies:", $this->plugin_slug ) . '</h3>';
		echo "<ul id='stt_disabled_smilies' class='sttSortable'>\n";
		// Get the hidden smilies in the (eventually) custom order
		$this->__print_ordered_smilies( $sm[1] );
		echo "</ul>";

?>
		<form name="smilies_toolbar_reset_order" method="post" action="admin-post.php" class='clear'>
<?php _e( "If you want to reset your smiley theme's order, click here:", $this->plugin_slug ); ?>
			<?php if (function_exists( 'wp_nonce_field' ) === true) wp_nonce_field( 'smilies_toolbar_reset_order' ); ?>
			<p id="submitbutton">
				<input type="hidden" name="action" value="stt_resetorder" />
				<input type="submit" value="<?php _e( 'Reset order', $this->plugin_slug ); ?> &raquo;" class="button-secondary" />
			</p>
		</form>
<?php
		?>
<style type="text/css">
	.sttSortable { list-style-type: none; margin: 10px 10px 20px; padding: 5px; width: 50%;min-height: 20px;}
	.sttSortable .wp-smiley {cursor: move;}
	#stt_enabled_smilies {background-color: #cfc;border: 1px solid #8a8;float: left;}
	#stt_disabled_smilies {background-color: #aaa;border: 1px solid #666;float: left;}
	#stt_enabled_smilies li, #stt_disabled_smilies li { margin: 3px 3px 3px 0; padding: 1px; display: block; float: left; border: 1px solid #ddd}
</style>
<script type="text/javascript">
/* <![CDATA[ */
(function($) {
$(function() { // Executed on DOM ready
	$("#stt_enabled_smilies, #stt_disabled_smilies").sortable({
		connectWith: '.sttSortable',
		stop: function (event, ui) {
			enabled_order = $("#stt_enabled_smilies").sortable('toArray');
			disabled_order = $("#stt_disabled_smilies").sortable('toArray');
			for (i = 0;i < enabled_order.length;i++) {
				enabled_order[i] = enabled_order[i].split('|')[1]
			}
			for (i = 0;i < disabled_order.length;i++) {
				disabled_order[i] = disabled_order[i].split('|')[1]
			}
			$("#stt_ajax_status").css('visibility', '');
			$.ajax({
				type: 'post',
				url: 'admin-ajax.php',
				traditional: true,
				data: {
					'sttenabled[]': enabled_order,
					'sttdisabled[]': disabled_order,
					action: 'stt_saveorder',
					_ajax_nonce: '<?php echo wp_create_nonce( 'stt_order' ) ?>'
				},
				success: function(data) {
					$("#stt_ajax_status").css('visibility', 'hidden');
				}
			});
		}
	}).disableSelection();
});
})(jQuery);
/* ]]> */
</script>
<?php
	echo '</div>';
	}


	/**
	 * Prints every <li> in the order process.
	 * For internal use only. (in options_page())
	 * 
	 * @param array $list The list of smilies: imagepath => associated_text
	 * */
	function __print_ordered_smilies( $list ) {
		if ( ! $list || ! is_array( $list ) ) return;
		foreach ( $list as $image => $text ) {
			echo "<li id='sttelement|$image'><img src='{$this->smilies_path}/$image'";
			echo " class='wp-smiley'";
			echo " alt='" . str_replace( "'", '&#039;', $text ) . "'";
			echo " title='" . str_replace( "'", '&#039;', $text ) . "'";
			echo " /></li>\n";
		}
	}


	/**
	 * Receives the order through Ajax, and saves it to the database.
	 */
	function save_ajax_order() {
		check_ajax_referer( 'stt_order' );
		if ( ! current_user_can( 'manage_options' ) ) die( '1' );

		$opt = get_option( 'smilies-themer-toolbar' );
		// TODO: should I do some sanity check?
		$opt['enabled_order'] = $_POST['sttenabled'];
		$opt['disabled_order'] = $_POST['sttdisabled'];
		update_option( 'smilies-themer-toolbar', $opt );
		die( '0' );
	}


	/**
	 * Returns the list of the smilies. If a custom order is set, they will
	 * be returned in that order.
	 * 
	 * @return array Double list of visible + hidden smileys:
	 * $result[0] = visible
	 * $result[1] = hidden
	 */
	function get_ordered_list() {
		global $smilies_themer;
		if ( ! $smilies_themer ) return false;
		// $result[0]: visible ones
		// $result[1]: hidden ones
		$result = array( array(), array() );

		// Discard smilies containing quotes, they don't work. (WP limitation)
		$sm = array();
		foreach ( $smilies_themer->smilies->smilies as $key => $value ) {
			if ( strpos( $key, "'" ) ) continue;
			$sm[$key] = $value;
		}

		// Discard duplicates
		$sm = array_reverse( $sm );
		$sm = array_flip( $sm );
		$sm = array_reverse( $sm );

		$opt = get_option( 'smilies-themer-toolbar' );
		// Have I saved the smilies order into the database?
		if ( is_array( $opt['enabled_order'] ) ) {
			/* Visible smilies ($result[0]) */
			foreach ( $opt['enabled_order'] as $filename ) {
				// NOTE: this SHOULD keep the order when using foreach, but I'm
				// not that sure. BTW in my tests it was ok.
				$result[0][$filename] = $sm[$filename];
			}

			/* Hidden smilies ($result[1]) */
			if ( is_array( $opt['disabled_order'] ) ) {
				foreach ( $opt['disabled_order'] as $filename ) {
					$result[1][$filename] = $sm[$filename];
				}
			}
			/* Smilies that aren't in any array (errors?)
			 * Added to hidden smilies ($result[1]) */
			foreach ( $sm as $filename => $smiley ) {
				if ( ! array_key_exists( $filename, $result[0] ) &&
				     ! array_key_exists( $filename, $result[1] ) ) {
					$result[1][$filename] = $smiley;
				}
			}
			return $result;
		}
		else {
			// No order set. Return the default order.
			$result[0] = $sm;
			return $result;
		}
	}


	/**
	 * Registers the smilies toolbar in the admin page, when creating/editing
	 * a post or a page.
	 */
	function add_smilies_box() {
		if ( function_exists( 'add_meta_box' ) ) {
			// Done for posts and pages.
			foreach ( array( 'post', 'page' ) as $type ) {
				add_meta_box( 'stt_smilies_box', __( 'Smilies', $this->plugin_slug ),
				  /* TODO: from WP 3.0, we have custom post types. Check if
				     add_meta_box() allows setting every post type */
				  array( &$this, 'show_admin_smilies_box' ), $type,
				  /* 'side' in WP >= 2.7 only */
				  ( function_exists( 'post_class' ) ? 'side' : 'normal' ), 'high' );
			}
		}
	}


	/**
	 * Shows the smilies toolbar in the admin page, when creating/editing
	 * a post or a page.
	 */
	function show_admin_smilies_box() {
		$this->print_toolbar( 'admin_box' );
		printf( '<br /><a href="%s">%s</a>', $this->plugin_options_url(),
		     __( 'Manage smilies options...', $this->plugin_slug ) );
	}


	/**
	 * Shows the smilies toolbar near the comments form.
	 */
	function show_comments_toolbar() {
		// This is necessary to allow using the theme function sm_toolbar_show()
		if ( $this->already_shown ) return;
		$this->print_toolbar();

		$this->already_shown = true;
	}


	/**
	 * Creates the smilies toolbar.
	 *
	 * @param string $type Optiona. Can be one of:
	 * 'comment_box' (default) => near comments page
	 * 'admin_box' => create/edit page/post
	 * 'admin_order' (not used for now)
	 * */
	function print_toolbar( $type = 'comment_box' ) {
		$list = $this->get_ordered_list();
		echo "<div id='smilies_toolbar'>\n";
		// Visible smilies
		foreach ( $list[0] as $image => $text ) {
			echo "<img src='{$this->smilies_path}/$image'";
			echo " class='wp-smiley'";
			echo " alt='" . str_replace( "'", '&#039;', $text ) . "'";
			echo " title='" . str_replace( "'", '&#039;', $text ) . "'";
			echo " />\n";
		}

		// If I have some hidden smilies:
		if ( sizeof($list[1]) ) {
			echo '<a href="javascript:void(0)" rel="stt">' . __( 'More &raquo;', $this->plugin_slug ). "</a>\n";
			echo '<div style="display: none">';
			foreach ( $list[1] as $image => $text ) {
				echo "<img src='{$this->smilies_path}/$image'";
				echo " class='wp-smiley'";
				// htmlspecialchars 2 times
				echo " alt='" . str_replace( "'", '&#039;', $text ) . "'";
				echo " title='" . str_replace( "'", '&#039;', $text ) . "'";
				echo " />\n";
			}
			echo "</div>\n";
		}
		echo "</div>\n";


		/**** JavaScript stuff, these scripts will be called on footer ******/

		/* Admin only: */
		if ( $type == 'admin_box' ) {
			$this->scripts_to_output[] = 'admin_click';
		}
		elseif ( $type == 'comment_box' ) {
			/* Comment form only: */
			$this->scripts_to_output[] = 'comment_click';
		}

		/* Admin postnew + comment form:
		 * Sets mouse cursor on the smilies, add show/hide code for hidden smilies.
		 */
		if ( $type != 'admin_order' ) {
			$this->scripts_to_output[] = 'cursor_style';
			if ( $type == 'comment_box' ) {
				/* A fix to avoid flicker when clicking on More... */
				// Removed temporarily, it causes huge problems with some themes.
				// TODO: find a solution for this flicker.
				// $this->scripts_to_output[] = 'anti_flicker';
			}
			if ( sizeof($list[1]) ) { // If I have hidden smileys:
				$this->scripts_to_output[] = 'hide_show';
			}
		}

	} /* End print_toolbar() */


	/**
	 * Action called when clicking on 'Reset order' in the admin area.
	 * */
	function order_reset_request() {
		if ( ! current_user_can('manage_options') )
			wp_die( __( 'Order was not reset: you don&lsquo;t have the priviledges to do this!', $this->plugin_slug ) );

		// cross check the given referer
		check_admin_referer( 'smilies_toolbar_reset_order' );

		$this->reset_order();

		wp_redirect( $_POST['_wp_http_referer'] );
	}


	/**
	 * Resets the order of the smilies.
	 */
	function reset_order() {
		$opt = get_option( 'smilies-themer-toolbar' );
		if ( is_array( $opt ) ) {
			if ( array_key_exists( 'enabled_order', $opt ) ) unset( $opt['enabled_order'] );
			if ( array_key_exists( 'disabled_order', $opt ) ) unset( $opt['disabled_order'] );
		}
		update_option( 'smilies-themer-toolbar', $opt );
	}


	/**
	 * The scripts are printed on the footer.
	 */
	function print_scripts_footer() {
		if ( ! $this->scripts_to_output || ! sizeof( $this->scripts_to_output ) ) return;

		?>
<script type='text/javascript'>
/* <![CDATA[ */
(function($) {
<?php

	/* Click action: adds the smiley to the comment box */
	if ( in_array( 'comment_click', $this->scripts_to_output ) ):
?>
	/* OnClick action */
	$("#smilies_toolbar img").click(function() {
		var text = $(this).attr('alt');
		/* get an html escaped version of the text: */
		tempdiv = $('<div/>');
		text = tempdiv.text(text).html();
		tempdiv.remove(); // Free up memory
<?php
		// compatibility with (Tiny)MCEComments: http://wordpress.org/extend/plugins/tinymcecomments/
		if ( function_exists( 'mcecomment_init' ) ) { ?>
		insertHTML( ' ' + text + ' ' );
<?php } else { ?>
		var commentbox = $("form[action$=wp-comments-post.php] textarea:visible");
		if (!commentbox.length) return;
		commentbox.insertAtCaret(text);
<?php } ?>
	});
<?php
	endif;


	/* Hide/show hidden smileys */
	if ( in_array( 'hide_show', $this->scripts_to_output ) ):
		// i18n strings
		printf( "\tstt_moreless = new Array(\"%s\", \"%s\");\n",
				 __( 'More &raquo;', $this->plugin_slug ),
				 __( '&laquo; Less', $this->plugin_slug )
		);
?>
	/* Show/hide hidden smilies */
	$("#smilies_toolbar a[rel=stt]").click(function() {
		$(this).html(jQuery("#smilies_toolbar div").is(":visible") ? stt_moreless[0] : stt_moreless[1]);
		$("#smilies_toolbar div").toggle(100);
	});
<?php
	endif;


	/* Click on the toolbar when writing a new post (admin) */
	if ( in_array( 'admin_click', $this->scripts_to_output ) ):
?>
	/* OnClick action */
	$("#smilies_toolbar img").click(function() {
		var text = $(this).attr('alt');
		/* get an html escaped version of the text: */
		tempdiv = $('<div/>');
		text = tempdiv.text(text).html();
		tempdiv.remove(); // Free up memory

		var html_area = $("#content");
		if (!html_area.length) return;
		if (html_area.is(":hidden")) {
			/* Visual editor */
			tinyMCE.activeEditor.execCommand('mceInsertContent', false, ' ' + text + ' ')
		}
		else {
			/* HTML editor */
			$(html_area).insertAtCaret(text);
		}
	});
<?php
	endif;

	/* Cursor style on mouseover */
	if ( in_array( 'cursor_style', $this->scripts_to_output ) ) {
	?>
	$("#smilies_toolbar img").css('cursor', 'pointer');
<?php
	}

	/* Anti-flicker hack. */
	if ( in_array( 'anti_flicker', $this->scripts_to_output ) ) {
		/* ?>
	$("#smilies_toolbar").css("width", $("form[action$=wp-comments-post.php] textarea:visible").width());
<?php */
	}

?>
})(jQuery);
/* ]]> */
</script>
<?php
	$this->must_print_scripts = array();
	}
}
endif; /* End class SmiliesThemerToolbar */


/* Call this function in your themes to show the toolbar in a different
 * position than the default.
 * Insert this in your theme file:
 * <?php if ( function_exists( 'sm_toolbar_show' ) ) sm_toolbar_show(); ?>
 */
function sm_toolbar_show() {
	global $sm_toolbar;
	if ( $sm_toolbar && method_exists( $sm_toolbar, 'show_comments_toolbar' ) )
		$sm_toolbar->show_comments_toolbar();
}


$sm_toolbar = new SmiliesThemerToolbar();


/**
 * Images/ Icons in base64-encoding
 */
if ( isset( $_GET['resource'] ) && $_GET['resource'] == 'sttoolbar.png' ) {
	$sm_toolbar->print_image( $_GET['resource'] );
}


// Add settings menu to admin interface
add_action( 'admin_menu', array( &$sm_toolbar, 'admin_menu' ) );
add_filter( 'plugin_action_links', array( &$sm_toolbar, 'add_action_link' ), 10, 2 );

// Manage ajax communications when ordering smilies (admin only)
add_action( 'wp_ajax_stt_saveorder', array( &$sm_toolbar, 'save_ajax_order' ) );

// Smilies box inside write post/page
add_action( 'admin_menu', array( &$sm_toolbar, 'add_smilies_box' ) );

// 68 because smilies-themer uses 67 as its priority
add_action( 'init', array( &$sm_toolbar, 'init' ), 68 );

// Load the scripts on the footer, to leave the content cleaner.
add_action( 'wp_footer', array( &$sm_toolbar, 'print_scripts_footer' ) );
add_action( 'admin_footer', array( &$sm_toolbar, 'print_scripts_footer' ) );

// Show smilies toolbar in the comment form.
add_action( 'comment_form', array( &$sm_toolbar, 'show_comments_toolbar' ) );
?>

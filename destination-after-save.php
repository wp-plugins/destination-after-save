<?php
/**
 * Plugin Name: Destination After Save
 * Plugin URI: http://slimbobwp.com/plugins/destination-after-save/
 * Description: Allows user to choose destination after saving the page they are editing, or to leave the page they are editing for a new destination.
 * Version: 1.0.0
 * Author: Bob Whitis
 * Author URI: http://slimbobwp.com
 * Text Domain: destination
 * Network: true
 * License: GPL2
 */

//	Meta Box in Edit Page UI for Destination After Save
function destination_page_meta_box() {
    $screens = array( 'page' );
    foreach ( $screens as $screen ) {
        add_meta_box( 'destination_page_meta_box', __( 'Destination After Save', 'destination_page_meta_box' ), 'destination_page_meta_box_callback', $screen, 'side', 'high' );
    }
}
add_action( 'add_meta_boxes', 'destination_page_meta_box' );

//	Handle Destination After Save
function destination_page_meta_box_after_save() {
    if( isset( $_POST['destination-selection'] ) ) {
        if( $_POST['destination-selection'] == "view" ) {
            global $post;
			wp_redirect( site_url() . '/?p=' . $post->ID );
        } elseif( $_POST['destination-selection'] == "new" ) {
			wp_redirect( site_url() . '/wp-admin/post-new.php?post_type=page' );
		} else {
            wp_redirect( site_url() . '/wp-admin/post.php?post=' . $_POST['destination-selection'] . '&action=edit' );
        }
        exit;
    }
}
add_action( 'save_post', 'destination_page_meta_box_after_save' );

//	Meta Box Content
function destination_page_meta_box_callback( $post ) {
    $current_location = $post->ID;
    $args = array(
        'sort_order' => 'ASC',
        'sort_column' => 'post_title',
        'hierarchical' => 0,
        'exclude' => $current_location,
        'child_of' => 0,
        'parent' => -1,
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish,draft,future,private,pending'
    ); 
    $destinations = get_pages( $args );

	//	Handle Destination Without Saving
    echo '<script type="text/javascript">
        function destination() {
            var destination = document.getElementById( "destination-selection" ).value;
            if( destination == "view" ) {
                window.location = "' . site_url() . '/?p=' . $current_location . '";
            } else if( destination == "new" ) {
				window.location = "' . site_url() . '/wp-admin/post-new.php?post_type=page";
			} else {
                window.location = "' . site_url() . '/wp-admin/post.php?post=" + destination + "&action=edit";
            }
        }
    </script>';
	//	Disable Leave Button if Adding New Page and View This Page is Selected
	if( $post->post_status == 'auto-draft' ) {
		echo '<script type="text/javascript">
			function disable_leave() {
				if( document.getElementById( "destination-selection" ).value == "view" ) {
					document.getElementById( "destination-submit" ).disabled = true;
				} else {
					document.getElementById( "destination-submit" ).disabled = false;
				}
			}
		</script>';
	}
	//	Destination Selection
    echo '<select style="margin-top: 10px; width: 100%;" name="destination-selection" id="destination-selection"';
	if( $post->post_status == 'auto-draft' ) {
		echo ' ' . 'onChange="disable_leave();"';
	}
	echo '>';
        echo '<option value="' . $post->ID . '">Edit This Page</option>';
		echo '<option value="view">View This Page</option>';
        echo '<option value="new">Add New Page</option>';
		echo '<option value="separator" disabled>Edit Other Page</option>';
        foreach( $destinations as $destination ) {
            echo '<option value="' . $destination->ID . '">' . $destination->post_title . '</option>';
        }
    echo '</select>
        <button style="margin-top: 10px; float: right;" type="button" name="destination-submit" id="destination-submit" class="button button-primary" onClick="destination();">Leave</button><div style="padding-top: 15px;">Don\'t Need to Save?</div>';
}
 
 ?>
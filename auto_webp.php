<?php 
/***
 * Convert Uploaded Images to WebP Format
 *
 * This snippet converts uploaded images (JPEG, PNG, GIF) to WebP format
 * automatically in WordPress.
 * 
 * ImageMagick or GD should be enable from server
 */

add_filter( 'wp_handle_upload', 'devswizard_handle_upload_convert_to_webp' );
 
function devswizard_handle_upload_convert_to_webp( $upload ) {
    if ( $upload['type'] == 'image/jpeg' || $upload['type'] == 'image/png' || $upload['type'] == 'image/gif' ) {
        $file_path = $upload['file'];
 
        // Check if ImageMagick or GD is available
        if ( extension_loaded( 'imagick' ) || extension_loaded( 'gd' ) ) {
            $image_editor = wp_get_image_editor( $file_path );
            if ( ! is_wp_error( $image_editor ) ) {
                $file_info = pathinfo( $file_path );
                $dirname   = $file_info['dirname'];
                $filename  = $file_info['filename'];
 
                // Create a new file path for the WebP image
                $new_file_path = $dirname . '/' . $filename . '.webp';
 
                // Attempt to save the image in WebP format
                $saved_image = $image_editor->save( $new_file_path, 'image/webp' );
                if ( ! is_wp_error( $saved_image ) && file_exists( $saved_image['path'] ) ) {
                    // Success: replace the uploaded image with the WebP image
                    $upload['file'] = $saved_image['path'];
                    $upload['url']  = str_replace( basename( $upload['url'] ), basename( $saved_image['path'] ), $upload['url'] );
                    $upload['type'] = 'image/webp';
 
                    // Optionally remove the original image
                    @unlink( $file_path );
                }
            }
        }
    }
 
    return $upload;
}
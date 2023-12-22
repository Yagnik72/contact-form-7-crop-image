# contact-form-7-crop-image
Need Help message yagnikpadaliya72+github-contact-form-7-crop@gmail.com

Follow me For MORE :) 
I have save your time more then Ai :) give me like

# Contact Form 7 Crop Image Plugin

## Overview

The Contact Form 7 Crop Image Plugin is a WordPress extension that enhances the capabilities of Contact Form 7 by incorporating image cropping functionality. With this plugin, users can upload images through a Contact Form 7 file input field and subsequently crop them to meet specific dimensions. The plugin utilizes the JCrop library for efficient image cropping within the Contact Form 7 environment.

## Features

- Seamless integration with Contact Form 7 forms.
- Utilizes JCrop library for easy and interactive image cropping.
- Allows users to upload images and define a fixed cropping area.
- Supports both small and large images, adjusting the cropping process accordingly.
- Provides a visual preview of the original and cropped images within the form.

## Installation

1. **Download**: Obtain the plugin ZIP file from the official repository or clone the repository to your local machine.

2. **Upload to WordPress**: Navigate to the WordPress dashboard and upload the plugin folder to the `wp-content/plugins/` directory.

3. **Activate Plugin**: Activate the Contact Form 7 Crop Image Plugin through the WordPress dashboard.

## Usage

### Enqueues

Ensure that the following scripts and styles are enqueued in your WordPress theme's `functions.php` file:

```php enqueue
wp_enqueue_script('jcrop', includes_url('js/jcrop/jquery.Jcrop.min.js'), array('jquery'), '', true);
wp_enqueue_style('jcrop-style', includes_url('js/jcrop/jquery.Jcrop.min.css'), array(), '');


```html
<label for="headshot">Headshot (upload)
    [file headshot limit:1024kb id:headshot filetypes:jpeg|jpg|png]
    [hidden cropped-image id:cropped-image]

    <div class="image-preview"></div>
    <button id="show-cropped" class="my-2" style="display: none;">Cropped</button>
    <div class="cropped-preview"></div>
</label>

```js
 if(jQuery('#headshot').length > 0 ){

    // Wait for the page to load
    $('#headshot').change(function() { 
      var reader = new FileReader();

      reader.onload = function(e) {   
          // Show cropped button 
          jQuery('#show-cropped').show();

         // Remove the previous image and its Jcrop instance
         jQuery('.image-preview').empty();

         // Create an image element and append it to the body
         var img = jQuery('<img id="crop-target" src="' + e.target.result + '" alt="Selected Image" style=" min-height: 300px; min-width: 300px;">');
         jQuery('.image-preview').append(img);

        
          // Initialize Jcrop
            img.Jcrop({
              onSelect: function(c) {
                  // c contains the selected area coordinates
                  console.log(c);
                  jcropInstance = c;
                  jQuery('#show-cropped').click()
              },
              aspectRatio: 1,
              setSelect: [0, 0, 300, 300], // Set fixed selection size to 300x300
              allowResize: false, // Disable resizing
              minSize: [300, 300], // Minimum selection size
              maxSize: [300, 300], // Maximum selection size
          });
      };

      
      // Read the selected image file
      reader.readAsDataURL(this.files[0]);
    });

    // Add a button click event to show the final cropped view
    jQuery('#show-cropped').on('click', function (e) {
      e.preventDefault();
      if (jcropInstance) {
           
        var c = jcropInstance;

        let img = $('#crop-target').get(0);
        let img_width = img.naturalWidth;
        let img_height = img.naturalHeight;
        let screen_height = jQuery(img).height();
        let screen_width = jQuery(img).width();


        if(img_width < 300 || screen_height < 300 ) { // small image

           // Calculate width and height ratios
            var widthRatio = 300 / c.w;
            var heightRatio = 300 / c.h;

            // Adjust the selection area based on the image dimensions
            c.w = Math.min(300, $('#crop-target').get(0).naturalWidth);
            c.h = Math.min(300, $('#crop-target').get(0).naturalHeight);

            // Create a canvas element
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');

            // Set the canvas size to the final cropped size (300x300)
            canvas.width = 300;
            canvas.height = 300;

            // Draw the cropped image based on the adjusted selection coordinates
            ctx.drawImage($('#crop-target').get(0), c.x * widthRatio, c.y * heightRatio, c.w * widthRatio, c.h * heightRatio, 0, 0, 300, 300);

        }else{ // large img

          let _height_ratio = 1;
          let _width_ratio = 1;
  
          if(img_height>screen_height) {
            _height_ratio = img_height/screen_height;
          }
  
          if(img_width>screen_width) {
           _width_ratio = img_width/screen_width;
          }
  
          // Create a canvas element
          var canvas = document.createElement('canvas');
          var ctx = canvas.getContext('2d');
  
          
          // Set the canvas size to the cropped area
          canvas.width = c.w*_width_ratio;
          canvas.height = c.h*_height_ratio;
  
          // Draw the cropped image based on the selected coordinates // $('.jcrop-holder img')[0]
          ctx.drawImage($('.jcrop-holder img')[0], c.x*_width_ratio, c.y*_height_ratio, c.w*_width_ratio, c.h*_height_ratio, 0, 0, c.w*_width_ratio, c.h*_height_ratio);
        }


        // Convert the cropped image to base64 data URL
        var base64Data = canvas.toDataURL('image/jpeg');

        // Set the value of the hidden field with the cropped image data
        $('#cropped-image').val(base64Data);

        // Create a new image element with the cropped area
        var croppedImg = jQuery('<img src="' + base64Data + '" alt="Cropped Image" style="width:300px; height:300px;"/>');

        // Remove the previous cropped image
        jQuery('.cropped-preview').empty();

        // Append the new cropped image
        jQuery('.cropped-preview').append(croppedImg);

      }
      
    });

  }

``` wpcf7_before_send_mail action PHP
 // Check if 'cropped-image' is set in the form data
                        if (isset($posted_data['cropped-image'])) {
                            $base64_image = $posted_data['cropped-image'];

                            // Decode the base64 image data
                            $image_data = base64_decode(str_replace('data:image/jpeg;base64,', '', $base64_image));

                            // Generate a unique filename for the image
                            $filename = wp_unique_filename(wp_upload_dir()['path'], $user_id . '-avatar-'. time() .'.jpg');

                            // Save the image data to the uploads directory
                            $upload_path = wp_upload_dir()['path'] . '/' . $filename;
                            file_put_contents($upload_path, $image_data);

                            // Create an attachment post
                            $attachment = array(
                                'post_mime_type' => 'image/jpeg',
                                'post_title'     => sanitize_file_name($filename),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                            );

                            $attach_id = wp_insert_attachment($attachment, $upload_path);

                            // Update user meta with the new avatar
                            if (!is_wp_error($attach_id)) {

                                // Unlink (remove) the existing avatar file, if it exists
                                $existing_avatar_id = get_user_meta($user_id, 'wp_user_avatar', true);

                                if ($existing_avatar_id) {
                                    $existing_avatar_path = get_attached_file($existing_avatar_id);

                                    if ($existing_avatar_path && file_exists($existing_avatar_path)) {
                                        // unlink($existing_avatar_path);
                                         // Delete the main attachment
                                        wp_delete_attachment($existing_avatar_id, true);
                                    }
                                }
                                
                                update_user_meta($user_id, 'wp_user_avatar', $attach_id);
                            }
                        }else{
                            $uploaded_files = $submission->uploaded_files();                          
                            $filename      = isset($uploaded_files['headshot'][0]) ? $uploaded_files['headshot'][0] : '';
                            if (file_exists($filename)) {
                            // Prepare the file data
                            $upload = wp_upload_bits(basename($filename), null, file_get_contents($filename));
                                // Check if the upload was successful
                                if (!$upload['error']) {
                                    // File was successfully uploaded, now create an attachment post
                                    $filetype = wp_check_filetype(basename($filename), null);
                                    $attachment = array(
                                        'post_mime_type' => $filetype['type'],
                                        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                                        'post_content'   => '',
                                        'post_status'    => 'inherit',
                                    );
                                    $attach_id = wp_insert_attachment($attachment, $upload['file']);
                                    require_once ABSPATH . 'wp-admin/includes/image.php'; // Make sure to include this line
                                    // Generate attachment metadata
                                    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                                    wp_update_attachment_metadata($attach_id, $attach_data);
                                    if(!empty($attach_id)){
                                        update_user_meta( $user_id, 'wp_user_avatar', $attach_id );
                                    }
                                }
                            }
                        }

Copy and paste this template into your README.md file on GitHub and customize it further if needed. Don't forget to replace the placeholder `(Your JavaScript code here)` with your actual JavaScript code.

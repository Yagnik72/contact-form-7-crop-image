<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Cropping Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.15/css/jquery.Jcrop.min.css"  crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        /* Add your custom styles here */
        .image-preview {
            margin-top: 20px;
            height: 60vh;
            border: 2px solid #363636;
            text-align: center;
            text-align: -webkit-center;
        }
        .image-preview > img {
            max-width: 100%;
            height: 100%;
        }

        .cropped-preview {
            margin-top: 20px;
            border: 1px solid #886767;
            text-align: center;
            text-align: -webkit-center;
            margin-bottom: 10px;
        }
        .cropped-preview img{
            max-height: 100%;
            max-width: 100%;
            box-shadow: 1px 0px 4px #888888;
            border: 1px solid #888888;
        }

        /* Example CSS for the specified classes */
        .cropped-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .landscape-4-3,
        .portrait-16-9,
        .vertical-9-16,
        .vertical-1-1 {
            width: calc(25% - 10px);
            height: 25vh;
            background-color: #ddd;
        }


    </style>
</head>
<body>

<div class="container mt-1">
    <div class="row">
        <div class="col-7">
            <label for="headshot" class="form-label">Headshot (upload)</label> <!-- Yagnik - DEv88 -->
            <input type="file" id="headshot" class="form-control" accept="image/jpeg, image/jpg, image/png" />
            <input type="hidden" id="cropped-image" />
        </div>
        <div class="col-5">
            <!-- Add a dropdown for aspect ratio -->
            <label for="aspect-ratio" class="form-label">Aspect Ratio</label>
            <select id="aspect-ratio" class="form-select">
                <option value="4/3" data-class="landscape-4-3">Landscape (4:3)</option>
                <option value="16/9" data-class="portrait-16-9">Portrait (16:9)</option>
                <option value="9/16" data-class="vertical-9-16">Vertical (9:16)</option>
                <option value="1/1" data-class="vertical-1-1">Square (1:1)</option>
            </select>
        </div>
        <div class="col-12">
            <div class="image-preview"></div>
            <div class="cropped-preview">
                <div class="landscape-4-3"></div>
                <div class="portrait-16-9"></div>
                <div class="vertical-9-16"></div>
                <div class="vertical-1-1"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.15/js/jquery.Jcrop.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$(document).ready(function(jQuery){
    
    $('select#aspect-ratio').on('change', function(){
        $('#headshot').trigger("change");
    })
    
    $('#headshot').change(function() { 

        var reader = new FileReader();

        reader.onload = function(e) {   

            // Remove the previous image - dev88 
            jQuery('.image-preview').empty();

            // Create an image element and append it to the body
            var img = jQuery('<img id="crop-target" src="' + e.target.result + '" alt="Selected Image" >');// style=" min-height: 300px; min-width: 300px;"
            
            jQuery('.image-preview').append(img);

            var aspectRatio = eval($('#aspect-ratio').val()); // Get selected aspect ratio

            img.Jcrop({
              onSelect: function(c) {
                // c contains the selected area coordinates     
                let img = $('#crop-target').get(0);
                let img_width = img.naturalWidth;
                let img_height = img.naturalHeight;
                let screen_height = jQuery(img).height();
                let screen_width = jQuery(img).width();
                let _height_ratio = 1;
                let _width_ratio = 1;

                if(img_height>screen_height) {
                _height_ratio = img_height/screen_height;
                }

                if(img_width>screen_width) {
                    _width_ratio = img_width/screen_width;
                }

                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');

                canvas.width = c.w*_width_ratio;
                canvas.height = c.h*_height_ratio;

                ctx.drawImage($('.jcrop-holder img')[0], c.x*_width_ratio, c.y*_height_ratio, c.w*_width_ratio, c.h*_height_ratio, 0, 0, c.w*_width_ratio, c.h*_height_ratio);

                var base64Data = canvas.toDataURL('image/jpeg');

                $('#cropped-image').val(base64Data);

                var croppedImg = jQuery('<img src="' + base64Data + '" alt="Cropped Image" />'); // style="width:300px; height:300px;"

                jQuery('.cropped-preview .'+$('#aspect-ratio option:selected').attr('data-class')).empty().append(croppedImg);

              },
              aspectRatio: aspectRatio,
              setSelect: [0, 0, 300, 300], // Set fixed selection size to 300x300
              allowResize: true, // Disable resizing
            });
        };
      
        reader.readAsDataURL(this.files[0]);
    });
    
})
</script>

</body>
</html>

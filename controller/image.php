<?php

class Image {
	public function Process(){
		global $data;
		$thumb_square_size 		= $data['image']['thumbnail']['size']; //Thumbnails will be cropped to 200x200 pixels
		$small_image_width 		= $data['image']['small']['width']; //Maximum image size (height and width)
		$small_image_height 	= $data['image']['small']['height']; //Maximum image size (height and width)
		$max_image_width 		= ($_POST['ajaxUploadwidth']!=0)?$_POST['ajaxUploadwidth']:$data['image']['width']; //Maximum image size (height and width)
		$max_image_height 		= ($_POST['ajaxUploadheight']!=0)?$_POST['ajaxUploadheight']:$data['image']['height']; //Maximum image size (height and width)
		$thumb_prefix			= $data['image']['thumbnail']['prefix']; //Normal thumb Prefix
		$small_prefix			= $data['image']['small']['prefix']; //Normal small Prefix
		$destination_folder		= $data['image']['destination']; //upload directory ends with / (slash)
		$jpeg_quality 			= $data['image']['quality']; //jpeg quality
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

	// check $_FILES['ImageFile'] not empty
	if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
			die('Image file is Missing!'); // output error when above checks fail.
	}

	//uploaded file info we need to proceed
	$image_name = $_FILES['image_file']['name']; //file name
	$image_size = $_FILES['image_file']['size']; //file size
	$image_temp = $_FILES['image_file']['tmp_name']; //file temp

	$image_size_info 	= getimagesize($image_temp); //get image size

	if($image_size_info){
		$image_width 		= $image_size_info[0]; //image width
		$image_height 		= $image_size_info[1]; //image height
		$image_type 		= $image_size_info['mime']; //image type
	}else{
		die("Make sure image file is valid!");
	}

	//switch statement below checks allowed image type
	//as well as creates new image from given file
	switch($image_type){
		case 'image/png':
			$image_res =  imagecreatefrompng($image_temp); break;
		case 'image/gif':
			$image_res =  imagecreatefromgif($image_temp); break;
		case 'image/jpeg': case 'image/pjpeg':
			$image_res = imagecreatefromjpeg($image_temp); break;
		default:
			$image_res = false;
	}

	if($image_res){
		//Get file extension and name to construct new file name
		$image_info = pathinfo($image_name);
		$image_extension = strtolower($image_info["extension"]); //image extension
		$image_name_only = strtolower($this->clean($image_info["filename"]));//file name only, no extension

		//create a random name for new image (Eg: fileName_293749.jpg) ;
		$new_file_name = $image_name_only. '_' .  rand(0, 9999999999) . '.' . $image_extension;

		//folder path to save resized images and thumbnails
		$small_save_folder 	= $destination_folder . $small_prefix . $new_file_name;
		$thumb_save_folder 	= $destination_folder . $thumb_prefix . $new_file_name;
		$image_save_folder 	= $destination_folder . $new_file_name;

		//call normal_resize_image() function to proportionally resize image
		if($this->normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_width, $max_image_height, $image_width, $image_height, $jpeg_quality))
		{
			//call normal_resize_image() function to create square thumbnails
			if(!$this->normal_resize_image($image_res, $small_save_folder, $image_type, $small_image_width, $small_image_height, $image_width, $image_height, $jpeg_quality))
			{
				die('Error Creating small thumbnail');
			}

			//call crop_image_square() function to create square thumbnails
			if(!$this->crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality))
			{
				die('Error Creating thumbnail');
			}

			/* We have succesfully resized and created thumbnail image
			We can now output image to user's browser or store information in the database*/
			echo '<div align="center">';
			echo '<img id="ajaxUploadfix" data-img="'.$new_file_name.'" src="'.$data['constant']['url'].$destination_folder. $new_file_name.'" alt="Resized Image">';
			echo '</div>';
		}

		imagedestroy($image_res); //freeup memory
	}
}
}

#####  This function will proportionally resize image #####
public function normal_resize_image($source, $destination, $image_type, $max_width, $max_height, $image_width, $image_height, $quality){

	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize

	//do not resize if image is smaller than max size
	if($image_width <= $max_width && $image_height <= $max_height){
		if($this->save_image($source, $destination, $image_type, $quality)){
			return true;
		}
	}

	//Construct a proportional size of new image
	$image_scale	= min($max_width/$image_width, $max_height/$image_height);
	$new_width		= ceil($image_scale * $image_width);
	$new_height		= ceil($image_scale * $image_height);

	$new_canvas		= imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image
	imagealphablending($new_canvas, false);

	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
		$this->save_image($new_canvas, $destination, $image_type, $quality); //save resized image
	}

	return true;
}


##### This function corps image to create exact square, no matter what its original size! ######
public function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality){
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize

	if( $image_width > $image_height )
	{
		$y_offset = 0;
		$x_offset = ($image_width - $image_height) / 2;
		$s_size 	= $image_width - ($x_offset * 2);
	}else{
		$x_offset = 0;
		$y_offset = ($image_height - $image_width) / 2;
		$s_size = $image_height - ($y_offset * 2);
	}
	$new_canvas	= imagecreatetruecolor( $square_size, $square_size); //Create a new true color image
	imagealphablending($new_canvas, false);

	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)){
		$this->save_image($new_canvas, $destination, $image_type, $quality);
	}

	return true;
}

##### Saves image resource to file #####
public function save_image($source, $destination, $image_type, $quality){
	switch(strtolower($image_type)){//determine mime type
		case 'image/png':
			imagesavealpha($source, true); imagepng($source, $destination); return true; //save png file
			break;
		case 'image/gif':
			imagegif($source, $destination); return true; //save gif file
			break;
		case 'image/jpeg': case 'image/pjpeg':
			imagejpeg($source, $destination, $quality); return true; //save jpeg file
			break;
		default: return false;
	}
}
public function clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
}

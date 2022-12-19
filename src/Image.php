<?php
/*
 * example:
 * $path = '../../../images/photo_news/';
 * $img = new Image();
 * $img->make($path . "20151017021604_desain_header_toyota_promo_oktober.jpg");
 * $img->resize(800, 541);
 * or $img->crop(441, 500);
 * $img->save($path.'__');
 */
class Image {
	protected $pathToFile = '';
	protected $newImg;
	protected $originalWidth;
	protected $originalHeight;
	protected $newimageSampled;
	protected $name = '';
	protected $image;

	public function make(String $image) 
	{
		if( ! is_file($image) ) {
			throw new InvalidArgumentException("`{$image}` does not exist");
		}
		$this->image = $image;
		$size = getimagesize($image);
		$width = $size[0];
		$height = $size[1];
		$ext = pathinfo($image, PATHINFO_EXTENSION);
		$this->name = pathinfo($image, PATHINFO_BASENAME);

		switch($ext) {
			case 'jpg':
				$img = imagecreatefromjpeg($image);
				break;
			case 'jpeg':
				$img = imagecreatefromjpeg($image);
				break;
			case 'png':
				$img = imagecreatefrompng($image);
				break;
			case 'gif':
				$img = imagecreatefromgif($image);
				break;
		}
		$this->originalWidth = $width;
		$this->originalHeight = $height;
		$this->newImg = $img;
	}	

	public function resize(Int $width = null, Int $height = null)
	{
		if( !$height ) {
			$this->resizeByWidth($width);
		}
		else if (($height && $width) || (!$width)) {
			if ($this->originalWidth >= $this->originalHeight) {
				$this->resizeByWidth($width);
			}
			else if ($this->originalHeight >= $this->originalWidth) {
				$this->resizeByHeight($height);
			}
		}
	}	

	public function resizeByHeight(Int $height) {
		if($this->originalHeight >= $height) {
			$new_height = $height;
		}else {
			$new_height = $this->originalHeight;
		}
		$new_width = ($new_height / $this->originalHeight) * $this->originalWidth;

		$this->resampledImage($new_width, $new_height);
	}

	public function resizeByWidth(Int $width)
	{
		if($this->originalWidth >= $width) {
			$new_width = $width;
		}else {
			$new_width = $this->originalWidth;
		}

		$new_height = ($new_width / $this->originalWidth) * $this->originalHeight;

		$this->resampledImage($new_width, $new_height);
	}

	public function save($path, $quality = -1)
	{
		if(function_exists('imagejpeg')){
			imagejpeg($this->newimageSampled, $path . $this->name, $quality);
		}
		
		imagejpeg($this->new_image, $this->image);

		$this->destroy();
	}

	private function destroy()
	{
		imagedestroy($this->newImg);
		imagedestroy($this->newimageSampled);
	}

	private function resampledImage($new_width, $new_height, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0) {

		$new_image = imagecreatetruecolor($new_width, $new_height);

		imagecopyresampled($new_image, $this->newImg, $dst_x, $dst_y, $src_x, $src_y, $new_width, $new_height, $this->originalWidth, $this->originalHeight);

		$this->newimageSampled = $new_image;

		var_dump('oke');
	}

	public function crop(Int $width = null, Int $height = null)
	{
		if( !$height ) {
			$height = $width;
		}
		$new_image = imagecreatetruecolor($width, $height);

		$new_width = $this->originalWidth / $width;
		$new_height = $this->originalHeight / $height;

		$half_width = $width / 2;
		$half_height = $height / 2;

		if($this->originalWidth > $this->originalHeight) {
			$adjusted_width = $this->originalWidth / $new_height;
			$h_width = $adjusted_width / 2;
			$int_width = $h_width - $half_height;

			imagecopyresampled($new_image, $this->newImg, -$int_width, 0, 0, 0, $adjusted_width, $height, $this->originalWidth, $this->originalHeight);

		}

		elseif( ($this->originalWidth < $this->originalHeight) || ($this->originalWidth == $this->originalHeight) ) {
			$adjusted_height = $this->originalHeight / $new_width;
			$h_height = $adjusted_height / 2;
			$int_height = $h_height - $half_height;
			imagecopyresampled($new_image, $this->newImg, 0, -$int_height, 0, 0, $width, $adjusted_height, $this->originalWidth, $this->originalHeight);	
		} 

		else {
			imagecopyresampled($new_image, $this->newImg, 0, 0, 0, 0, $width, $height, $this->originalWidth, $this->originalHeight);
		}

		$this->newimageSampled = $new_image;

		var_dump('oke crop');
	}
}
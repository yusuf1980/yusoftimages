delete unwanted pictures in directory. Feature resize and crop all images.

# Delete unwanted pictures in directory, resize and crop
______

Call, ex:

	require '../../../vendor/yusoftimage/src/Images.php';

ex: 

	$path = '../../../images/photo_news/';
 	$images = new Images($path);
 	$images->process($imagesthatsnotdelete);

 Excecute delete unwanted pictures

 	$images->removeImagesNotInDb();

 resize all file in folder, array for width and height

 	$size = [800, 500];
 	$images->resize($path, $size);

 crop semua file dalam folder

 	$size = [300, 300];
 	$images->resize($path, $size);

 # Resize / Crop Image Example

 	$img = new Image();
	$img->make("../images/image_to_resize.jpg");
	$img->resize(800, 541);

or

	$img->crop(441, 500);

save to your path

	$img->save($path);
<?php
/*
 * Author: Achmad Yusuf
 */
// namespace YusoftImage;
require 'Image.php';

/*
 * call images where you put the lib 
 * ex: require '../../../vendor/yusoftimage/Images.php';
 * example how to use: 
 * $path = '../../../images/photo_news/';
 * $images = new Images($path);
 * $images->process($alldbimages, $arrayattributes)
 * ex: $images->process($imagenews, ['small_', 'thumbnail_']);
 * 
 * hapus semua gambar yang tidak ada di db atau tidak digunakan
 * $images->removeImagesNotInDb();
 *
 * resize semua file dalam folder
 * $size = [1000, 1000];
 * $images->resize($path, $size);
 * 
 * crop semua file dalam folder
 * $size = [300, 300];
 * $images->resize($path, $size);
 */

class Images 
{
	/* 
	 * tempat folder yang akan dieksekusi
	 * ex: ../../../images/photo_news/thumb/'
	 * Jika path lebih dari satu gunakan array
	 * ex: 
	 * $path1 = '../../../images/photo_news/thumb/';
	 * $path2 = '../../../images/photo_news/thumb2/';
		$arrayPath = [$path, $path2];
	 */
	protected $pathToFolder = '';

	/*
	Seluruh file dan folder yang ada di dalam folder
	 */
	protected $files = [];

	/*
	Gambar yg tidak ada di database
	 */
	public $notInDb = [];

	/*
	 * properi untuk menyimpan data seluruh gambar dari database
	 */
	protected $dbImages = [];

	// Seluruh folder yang ada di dalam folder path
	protected $folder = [];

	public function __construct($pathToFolder)
	{
		// if (! is_dir($pathToFolder)) {
  //           throw new InvalidArgumentException("`{$pathToFolder}` does not exist");
  //       }
		$this->pathToFolder = $pathToFolder;
	}

	/*
	 * Setelah menginitial new Images
	 * Pengecekan gambar2 dengan database
	 */
	public function process(Array $database, $attribute = '')
	{
		$this->dbImages = $database;
		$this->getAllFiles();

		$new_images = [];
		$notInDb = [];

		// var_dump($this->pathToFolder);
		/// Jika path berbentuk array
		if (is_array($this->pathToFolder)) {
			for($i = 0; $i <= count($this->pathToFolder) - 1; $i++) {
				foreach($database as $db) {
					$file = $this->pathToFolder[$i] . '/' . $db;
					// var_dump($file);
					if(in_array($file, $this->files)) {
						array_push($new_images, $file);
					}
				}
			}
		}

		// Jika path tidak berbentuk array
		else if(!is_array($this->pathToFolder)) {
			foreach($database as $db) {
				/*
				 * if type attribute is array
				 */
				if(is_array($attribute)) {
					foreach($attribute as $attr) {
						$file = $this->pathToFolder . $attr . $db;
						if(in_array($file, $this->files)) {
							array_push($new_images, $file);
						}
					}
				}
				else {
					$file = $this->pathToFolder . $attribute . $db;

					if(in_array($file, $this->files)) {
						array_push($new_images, $file);
					}
				}
			}
		}

		/*
		 * mengecek file yang tidak ada di database
		 */
		foreach($this->files as $f) {
			if(is_file($f)) {
				if(!in_array($f, $new_images)) {
					array_push($notInDb, $f);
				}
			}else {
				// Folder2 yang ada di dalam path
				if(is_dir($f)) {
					array_push($this->folder, $f);
				}
			}
		}

		$this->notInDb = $notInDb;

		/*
		 * get all files (files and directories)
		 */
		$this->getAllFiles();

		return $this->notInDb;
	}

	/*
	 * get all files (files and directories)
	*/
	public function getAllFiles()
	{
		// var_dump($this->pathToFolder[0]);
		$this->files = glob($this->pathToFolder.'*');
		// var_dump(glob($this->pathToFolder[0].'/*');
		return $this->files;
	}

	/*
	 * Get only files without directory (folder)
	 */
	public function getOnlyFiles()
	{
		$files = [];
		foreach($this->files as $file) {
			if(is_file($file)) {
				array_push($files, $file);
			}
		}
		return $files;
	}

	/* 
	 * Execution
	 * Remove images if files not in database
	 */
	public function removeImagesNotInDb()
	{
		foreach($this->notInDb as $ndb) {
			unlink($ndb);
		}
	}

	/*
	 * Count image without directory
	 */
	public function countImage()
	{
		$files = $this->getOnlyFiles();

		return count($files);
	}

	/*
	 * Count images from database
	 */
	public function countDB() 
	{
		return count($this->dbImages);
	}

	/*
	 * Count directory that in path directory
	 */
	public function countFolder()
	{
		return count($this->folder);
	}

	/*
	 * Execution
	 * Resize all images
	 * $path - path where to save after resize
	 */
	public function resize(String $path, Array $size)
	{
		$files = $this->getOnlyFiles();
		$width = $size[0];
		$height = $size[1];
		foreach($files as $file) {
			$img = new Image;
			$img->make($file);
			$img->resize($width, $height);
			$img->save($path);
		}
	}

	/*
	 * Execution
	 * Crop all images
	 * $path - path where to save after resize
	 */
	public function crop(String $path, Array $size)
	{
		$files = $this->getOnlyFiles();
		$width = $size[0];
		$height = $size[1];
		foreach($files as $file) {
			$img = new Image;
			$img->make($file);
			$img->crop($width, $height);
			$img->save($path);
		}
	}
}
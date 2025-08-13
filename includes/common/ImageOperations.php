<?php

class ImageOperations {
	private $quality;
	private $location;
	private $supported_types;

	function __construct($location='/tmp/',$quality=90){
		$this->quality = $quality;
		$this->location=$location;
		$this->supported_types = array(1, 2, 3, 7);
	}

	private function getNewImageDimensions($imagestring,$reqwidth,$reqheight){
        $dimension = Array();
        try{
			list($width_orig, $height_orig, $image_type) = $this->getImageSize($imagestring);
			
			$dimension['height'] = $reqheight;
			$dimension['width'] = $reqwidth;
			$dimension['type'] = $image_type;
			$dimension['height_orig'] = $height_orig;
			$dimension['width_orig'] = $width_orig;

            $dimension=$this->calculateResizeDimensions($dimension);

		}
		catch(Exception $ex){ }					
		return $dimension;
	}

	private function calculateResizeDimensions($dimension){
        try{
			if($dimension['height']==0){
				if($dimension['width_orig']<$dimension['width']){
					$dimension['width']=$dimension['width_orig'];
					$dimension['height']=$dimension['height_orig'];
				}
				else  $dimension['height']=floor(($dimension['height_orig']/$dimension['width_orig'])*$dimension['width']);
			}
			else if($dimension['width']==0){
				if($dimension['height_orig']<$dimension['height']){
					$dimension['width']=$dimension['width_orig'];
					$dimension['height']=$dimension['height_orig'];
				}
				else $dimension['width']=floor(($dimension['width_orig']/$dimension['height_orig'])*$dimension['height']);
			}
			else{
				if($dimension['width_orig']<$dimension['width'] && $$dimension['height_orig']<$dimension['height']){
					$dimension['width']=$dimension['width_orig'];
					$dimension['height']=$dimension['height_orig'];
				}
				else{
					$reqratio=$dimension['width']/$dimension['height'];
					$orgratio=(float) $dimension['width_orig']/$dimension['height_orig'];
					if($reqratio<=$orgratio){
						$aspect_ratio = (float)  $dimension['height_orig'] /$dimension['width_orig'];
						$dimension['height'] = round($dimension['width'] * $aspect_ratio);
					}
					else  $dimension['width'] = round($dimension['height'] * $orgratio);
				}
			}
		}
		catch(Exception $ex){ }					
		return $dimension;
	}

	private function getImageSize($string_data){
		try{
			if (!function_exists('getimagesizefromstring')) {
				$uri = 'data://application/octet-stream;base64,'  . base64_encode($string_data);
				return getimagesize($uri);
			}
			else{
				return getimagesizefromstring($string_data);
			}
		}
		catch(Exception $ex){ }					
    }

	private function resizeImage($imagestring,$dimension){
       try{
			/*** imagecreatefromstring will automatically detect the file type ***/
			$source = imagecreatefromstring($imagestring);

			/*** create the thumbnail canvas ***/ 
			$thumb = imagecreatetruecolor($dimension['width'], $dimension['height']);
						
			/****** png transparency saved ****************/
			if($dimension['type']==3){
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
				$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
				imagefilledrectangle($thumb,0,0,$dimension['width'], $dimension['height'],$transparent);
			}

			/*** map the image to the thumbnail ***/
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $dimension['width'], $dimension['height'], $dimension['width_orig'], $dimension['height_orig']);
					
			/*** destroy the source ***/
			imagedestroy($source);
		}
		catch(Exception $ex){ }					
        return $thumb;
	}

    private function saveImage($resizeddata,$newfilename,$image_type){
       try{
			/*** write thumbnail based on file type ***/
			switch ($image_type){
				case 1:
					$newfilename.='.gif';
					imagegif($resizeddata,$this->location.$newfilename);
					break;
				case 2:
					$newfilename.='.jpg';
					imagejpeg($resizeddata,$this->location.$newfilename,$this->quality);
					break;
				case 3:
					$newfilename.='.png';
					imagepng($resizeddata,$this->location.$newfilename);
					break;
				case 7:
					$newfilename.='.bmp';
					imagewbmp($resizeddata,$this->location.$newfilename);
					break;
			}
		}
		catch(Exception $ex){ }					
        return $newfilename;
	}

	public function resize($source_image,$newfilename,$reqwidth,$reqheight){
		$retval=false;
		try{
			$this->checkAndRotateImageIfNecessary($source_image);
			if(!file_exists($source_image)){ error_log('source image not found : ' . $source_image);  }
			else{
				$data=file_get_contents($source_image);
				$retval=$this->resizeFromData($data,$newfilename,$reqwidth,$reqheight);			
			}
		}
		catch(Exception $ex){ }	
		return $retval;
	}

	public function checkAndRotateImageIfNecessary($source){
		$retval=false;
		try{
			if(!file_exists($source)){ error_log('source image not found : ' . $source_image);  }
			else{
				$exif = exif_read_data($source);
				if (!empty($exif['Orientation']) && in_array($exif['Orientation'], [2, 3, 4, 5, 6, 7, 8])) {
					$image = imagecreatefromjpeg($source);
					if (in_array($exif['Orientation'], [3, 4])) {
						$image = imagerotate($image, 180, 0);
					}
					if (in_array($exif['Orientation'], [5, 6])) {
						$image = imagerotate($image, -90, 0);
					}
					if (in_array($exif['Orientation'], [7, 8])) {
						$image = imagerotate($image, 90, 0);
					}
					if (in_array($exif['Orientation'], [2, 5, 7, 4])) {
						imageflip($image, IMG_FLIP_HORIZONTAL);
					}
					imagejpeg($image, $source, 100);
				}
			}
		}
		catch(Exception $ex){ }
		return $retval;
	}

    public function resizeFromData($data,$newfilename,$reqwidth,$reqheight){
		$retval=false;
		try{
			$dimension=$this->getNewImageDimensions($data,$reqwidth,$reqheight);
			
			/** check for supported type ***/
			if(!in_array($dimension['type'], $this->supported_types)){ error_log('source image type not supproted : ' . $newfilename); }
 			else {
				$resizeddata=$this->resizeImage($data,$dimension);
				$newfilename=$this->saveImage($resizeddata,$newfilename,$dimension['type']);
				$retval=$newfilename;
			}
		}
		catch(Exception $ex){ }	
		return $retval;
	}

	public function compressJpegImageOnQuality($imgdata,$targetfile,$max_img_quality,$min_img_quality,$min_img_size){
		$count=0;
		try {
			clearstatcache();
			$img_size=filesize($targetfile)/1024;//in kb
			if($img_size>$min_img_size){
				$ratio=($img_size-$min_img_size)/($max_img_quality-$min_img_quality);
			}else{
				$ratio=1;
			}
			$quality=intval($this->getMeanValue($max_img_quality,$min_img_quality,$ratio));
			$status=imagejpeg($imgdata,$targetfile,$quality);
			if($quality>$min_img_quality){
				if($img_size>$min_img_size){
					clearstatcache();
					$img_size=filesize($targetfile)/1024;
					if($img_size>$min_img_size){
						$count=1+$this->compressJpegImageOnQuality($imgdata,$targetfile,$quality,$min_img_quality,$min_img_size);
						return $count;
					}else{
						$count=1+$this->compressJpegImageOnQuality($imgdata,$targetfile,$max_img_quality,$quality,$min_img_size);
						return $count;
					}
				}else{
					$count=1+$this->compressJpegImageOnQuality($imgdata,$targetfile,$max_img_quality,$quality,$min_img_size);
					return $count;
				}
			}else{
				return $count;
			}
		} catch (Exception $e) {}
	}

	private function getMeanValue($value1,$value2,$ratio=1){
		$retval=0;
		try {
			$retval=($value1+($ratio*$value2))/(1+$ratio);
		} catch (Exception $e) {}
		return $retval;
	}

	public function getImageData($source_image=''){
		$imgdata=false;
		try {
			if(!file_exists($source_image)){ error_log('fn : getImageData : source image not found : ' . $source_image);  }
			else{
				if($this->getImageExtension($source_image)){
					$data=file_get_contents($source_image);
					$imgdata=imagecreatefromstring($data);
				}
			}
		} catch (Exception $e) {}
		return $imgdata;
	}

	public function getImageExtension($source_image){
		try{
			if(filesize($source_image)>11){
				$image_type=exif_imagetype($source_image);
			}else{
				$image_type=false;
			}
			switch($image_type)
			{
				case 1:
					return '.gif';
					break;
				case 2:
					return '.jpg';
					break;
				case 3:
					return '.png';
					break;
				case 7:
					return '.png';
					break;
				default:
					return false;
		   }
		}
		catch(Exception $ex){ error_log("Exception in fn getImageExtension " . $e->getMessage()); }
	}
}

?>
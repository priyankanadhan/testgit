<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
require BASE_PATH.'/vendor/autoload.php';

class ImageuploadController extends \Phalcon\Mvc\Controller {
	CONST bucket = 'nidara-dev';
	
	/**
	 * Image upload to s3
	 */
	public function imageupload() {
		if (empty ( $_FILES )) {
			return $this->response->setJsonContent ( array (
					"status" => false,
					"message" => "Please upload the file" 
			) );
		}
		$target_dir = BASE_PATH . "/public/temp/";
		$target_file = $target_dir . basename ( $_FILES ["file"] ["name"] );
		if (move_uploaded_file ( $_FILES ["file"] ["tmp_name"], $target_file )) {
		}
		
		$profile = 'default';
		$path = APP_PATH . '/library/credentials.ini';
		
		$provider = CredentialProvider::ini ( $profile, $path );
		$provider = CredentialProvider::memoize ( $provider );
		
		// Instantiate an Amazon S3 client.
		$s3 = new S3Client ( [ 
				'version' => 'latest',
				'region' => 'us-east-1',
				'credentials' => $provider 
		] );
		$ext = $this->getExtension ( basename ( $_FILES ["file"] ["name"] ) );
		$actual_image_name = time () . ".".$ext;
		try {
			$result = $s3->putObject ( [ 
					'Bucket' => self::bucket,
					'Key' => 'dev-files/' . $actual_image_name, // file name. this could be a unique UUID name instead of meaningful name.
					'Body' => fopen ( $target_file, 'r' ), // instead of fopen function we can use file uploaded tmp path
					'ACL' => 'public-read' 
			] );
			$s3file = 'http://' . self::bucket . '.s3.amazonaws.com/dev-files/' . $actual_image_name;
			unlink ( $target_file );
			return $this->response->setJsonContent ( array (
					"status" => true,
					"photo" => $s3file,
					"message" => "Image uploaded successfully" 
			) );
		} catch ( Aws\S3\Exception\S3Exception $e ) {
			return $this->response->setJsonContent ( array (
					"status" => false,
					"message" => "There was an error uploading the file.\n" 
			) );
		}
	}
	
	/**
	 * 
	 * @param string $str
	 * @return string
	 */
	public function getExtension($str) {
		$i = strrpos ( $str, "." );
		if (! $i) {
			return "";
		}
		
		$l = strlen ( $str ) - $i;
		$ext = substr ( $str, $i + 1, $l );
		return $ext;
	}
}

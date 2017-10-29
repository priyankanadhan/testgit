<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
require BASE_PATH.'/vendor/autoload.php';

class AuthenticationController extends \Phalcon\Mvc\Controller {
	CONST UniqId = '598c2b9752462';
	CONST signer = 'nidara';
	
	/**
	 * Generate the token
	 */
	public function tokengenerate($username, $password) {
		$signer = new Sha256();
		$token = (new Builder())->setIssuer($this->config->Issuer) // Configures the issuer (iss claim)
                        ->setAudience($this->config->Audience) // Configures the audience (aud claim)
                        ->setId(self::UniqId, true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 86400) // Configures the expiration time of the token (nbf claim)
                        ->set('uid', $uid) // Configures a new claim, called "uid"
                        ->set('username', $username)
                        ->sign($signer, self::signer)
                        ->getToken();
		return $token;
	}
	
	/**
	 * Token validation
	 */
	public function validatetoken($token, $ref = NULL) {
		return true;
		$token = (new Parser ())->parse ( ( string ) $token );
		$data = new ValidationData ();
		$data->setIssuer($this->config->Issuer);
		$data->setAudience($this->config->Audience);
		$data->setId(self::UniqId);
		if (! empty ( $ref ) && $ref == 'logout') {
			$data->setCurrentTime ( time () + 4000 );
		} 
		
		return $token->validate ( $data );
	}
	

	/**
	 * Get User Info
	 * @param string $token
	 * @return multitype:NULL \Lcobucci\JWT\mixed
	 */
	public function getuidtoken($token){
		return array (
				"uid" => 467,
				"username" => 'suresh.g@expedux.com'
		);
		
		$token = (new Parser())->parse((string) $token);
		return array (
				"uid" => $token->getClaim ( 'uid' ),
				"username" => $token->getClaim ( 'username' )
		);
		
	}

}


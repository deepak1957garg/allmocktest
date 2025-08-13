<?php 
class SCCURL{
	protected $agents = array(
		'sc'=>'Mozilla/5.0 (compatible; sweetcouch/1.0; +http://www.sweetcouch.com)',
		'ie'=>'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)',
		'google'=>'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
	);
	protected $url, $ch, $scheme,$use_agent;
	protected $custom_headers;

	private $timeout = 25;
	private $cookieFile = '';
	private $usePersistentCookies = false;	// store cookies in file or in memory

	public function __construct($url,$agent=0){ 
		$this->use_agent = $agent;  
		$this->init($url); 
		$this->custom_headers = array(); 
	}

	public function __destruct(){
		if(!empty($this->ch))
			curl_close($this->ch);
	}	
	
	public function getHeaders(){
		$this->createCURLRequest(1);
		$response[] = $this->fetchRequestResponse();
		//var_dump($response);		
		$response['code'] = $this->fetchReturnCode();
		if($response['code'] == '405'){
            $this->createCURLRequest(-1);
            $this->fetchRequestResponse();
            $response['code'] = $this->fetchReturnCode();
        }
		$response['newtarget'] = $this->fetchRedirectUrl();
		return $response;
	}

	public function getInfo($opt=null){
		return $opt ? curl_getinfo($this->ch, $opt) : curl_getinfo($this->ch);
	}

	public function getBody(){
		$this->createCURLRequest(0);
		$response = $this->fetchRequestResponse();
		if($response) return $response; else return '';
	}
	
	public function deleteBody(){
		$this->createCURLRequest(0);
		$this->setDeleteOptions();
		$response = $this->fetchRequestResponse();
		if($response) return $response; else return '';
	}

	public function postBody($params){
		$this->createCURLRequest(0);
		$this->setPostParams($params);
		$response = $this->fetchRequestResponse();
		if($response) return $response; else return '';
	}

	public function fetchReturnCode(){ 
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);  
	}

	public function fetchError(){ 
		return curl_errno($this->ch).",".curl_error($this->ch); 
	}

	public function setProxy($proxy){ 
		curl_setopt($this->ch, CURLOPT_PROXY,$proxy); 
	}

	public function setBasicAuth($username,$pass){ 
		curl_setopt($this->ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
		curl_setopt($this->ch,CURLOPT_USERPWD,$username.":".$pass); 
	}

	public function setHeaders($http_header_arr){
		$this->custom_headers = array_merge($this->custom_headers, $http_header_arr);
	}

	public function addCustomHeader($param){
		array_push($this->custom_headers,$param);
	}

	// public function setBinaryTransfer(){
	// 	curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
	// }
	
	public function setTimeout($seconds){
		$this->timeout = $seconds;
	}

	public function usePersistentCookies(){
		$this->usePersistentCookies = true;
		$this->setCookieFile(parse_url($this->url));		
	}

	protected function init($url){
		$this->url = $url;
		$this->url = $this->removeHash($this->url);
		$parts = parse_url($this->url);
		
		if(!empty($parts['scheme'])) {
			$this->scheme = $parts['scheme'];
		}
		$this->ch = curl_init();
	}

	private function setCookieFile($urlParts){
		if(isset($urlParts['host'])){
			$domain = str_ireplace('www.', '', $urlParts['host']);
			$fileName = str_replace(array('-', '.'), array('_', '_'), $domain) . '_sccookies.txt';
		} else {
			$fileName = 'common_sccookies.txt';
		}
		$cookiePath = '/tmp/sccache/';
		$this->cookieFile = "$cookiePath$fileName";

		if(!file_exists($this->cookieFile)){
			if(!file_exists($cookiePath)){	// create directory 
				mkdir($cookiePath, 0777, true);
			}
			if(!touch($this->cookieFile)){	// create file
				error_log("Failed to create cookie file at ". $this->cookieFile);
			}
		}
	}

	protected function createCURLRequest($header=0){
		$agent = $this->agents['sc'];
		if($this->use_agent == 1) $agent = $this->agents['google'];
		else if($this->use_agent == 2) $agent = $this->agents['ie'];
		
		curl_setopt($this->ch, CURLOPT_URL,$this->url);
		curl_setopt($this->ch, CURLOPT_USERAGENT, $agent );
		curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true); 		/* try to follow redirects */
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
	  	curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
	    curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST,  false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($this->ch, CURLOPT_ENCODING , 'gzip');			/* Set content encoding */

		$this->setIPSettings();

		if($this->usePersistentCookies){
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);	// persistent in file
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		} else {
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');	// in memory 
		}

		if($header===1){
		   	curl_setopt($this->ch, CURLOPT_NOBODY, true);
		  	curl_setopt($this->ch, CURLOPT_HEADER, true);
		}else if($header === -1){
			curl_setopt($this->ch, CURLOPT_NOBODY, false);
			curl_setopt($this->ch, CURLOPT_HEADER, true);
		}

		if($this->scheme == 'https')
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);

		if(count($this->custom_headers) > 0)
			curl_setopt($this->ch,CURLOPT_HTTPHEADER, $this->custom_headers);
	}

	private function setPostParams($params){
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTREDIR, 3); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
	}
	private function setDeleteOptions(){
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	}

	protected function fetchRequestResponse(){ 
		$r = curl_exec($this->ch); 
		return $r; 
	}

	public function fetchRedirectUrl(){ 
		return curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);  
	}

	protected function removeHash($url){ 
		$x = explode('#',$url);  
		return $x[0]; 
	}

	private function setIPSettings(){
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){ 
			curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
	}

	public function setSafeUpload(){
		curl_setopt($this->ch, CURLOPT_SAFE_UPLOAD, false);
	}
	
	 public function setCURLSettings($var,$val){
		curl_setopt($this->ch,$var,$val);
	}
}

//$curl = new SCCURL('http://sweetcouch.com',1);
//$opt = $curl->getHeaders();
//$opt = $curl->getBody();
//$opt = $curl->postBody("xx=1");
//if($opt == '') var_dump($curl->fetchError());
//var_dump($opt);
//test
?>
<?php
define("LINE_API","https://notify-api.line.me/api/notify");
define("LINE_TOKEN","ใส่ Token ตรงนี้");

class Linenotify 
{
	private $curl = '';
	private $url = 'https://notify-api.line.me/api/notify';	
	private $token = '';
	private $header = '';
	private $post = '';
	private $result = '';

	public $message =  '-';
	public $stickerPackageId = '';
	public $stickerId = '';
	public $image = '';

	public function __construct($token=''){
		$this->token = $token;
		// echo $token;
		$this->curl = curl_init($this->url);
		$this->init();
	}

	private function init(){
		$this->headers = [ 'Content-type: application/x-www-form-urlencoded', 
						 'Authorization: Bearer '.$this->token, 
					   ]; 
		curl_setopt( $this->curl, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt( $this->curl, CURLOPT_POST, 1); 
		curl_setopt( $this->curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt( $this->curl, CURLOPT_HTTPHEADER, $this->headers); 
		curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, 1); 
	}

	private function queryurl($post=[]) {
		if($post){
			$this->post = rawurldecode(http_build_query($post));
		} else {
			$posts = [];
			if($this->message) {
				$posts['message'] = $this->message;
			}

			if($this->stickerPackageId && $this->stickerId ){
				$posts['stickerPackageId'] = $this->stickerPackageId;
				$posts['stickerId'] = $this->stickerId;
				if(empty($this->message)){ $this->message = '  ';}
			}

			if($this->image){
				$posts['imageFullsize'] = $this->image;
				$posts['imageThumbnail'] = $this->image;
				if(empty($this->message)){ $this->message = '  ';}
			}

			if($posts) {
				$this->post = rawurldecode(http_build_query($posts));
			}
		}
	}

	public function exec($post=''){

			if($this->token){
				$this->queryurl($post);
				curl_setopt( $this->curl, CURLOPT_POSTFIELDS,$this->post); 
				$this->result = curl_exec( $this->curl ); 
			} else {
				echo 'Error';
			}
	}

	public function __destruct(){
		if(curl_error($this->curl)) { 
			echo 'error:' . curl_error($this->curl); 
		}
		curl_close( $this->curl ); 
		exit();
	}


}

function notify_message(){
	if($_GET['token']) {
			$token = $_GET['token'];
            $ln = new Linenotify($token);
			$ln->message	        = (isset($_GET['message'])  ? $_GET['message']  : '' );
			$ln->image	            = (isset($_GET['image'])    ? $_GET['image']    : null );
			$ln->stickerPackageId	= (isset($_GET['stkpkgid']) ? $_GET['stkpkgid'] : null );
			$ln->stickerId	        = (isset($_GET['stkid'])    ? $_GET['stkid']    : null );
			$ln->exec();
		    return 'ok';
    } else {
	        return 'Error';
    }
}
$res = notify_message();
echo $res;

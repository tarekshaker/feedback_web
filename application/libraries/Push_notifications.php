<?PHP 
	class push_notifications{
		private $iosCertFilePath;
		private $iosPassphrase;
		private $appleServer;
		private $google_server;
		private $google_api_key;
		
		public function __constructor(){	
		}
		public function set_ios_certificate($cer_file_path,$passpharse,$appleServer){
			$this->iosCertFilePath=$cer_file_path;
			$this->iosPassphrase=$passpharse;
			$this->appleServer=$appleServer;
		}
		private function ios_body_generator($body_arr){
			$body=array();
			$body['aps']  = $body_arr;
			return $body;
		}
		private function start_apple_conection(){
			$ctx = stream_context_create();
			
			stream_context_set_option($ctx, 'ssl', 'local_cert', $this->iosCertFilePath);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $this->iosPassphrase);
			
			// Open a connection to the APNS server
			$fp = stream_socket_client(	$this->appleServer, $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			
			if (!$fp){
				
				exit("Failed to connect: $err $errstr" . PHP_EOL);
				
				return false;
			}else{
			
				return $fp;
				}
		}
		private function validate_body_array($body_arr){
			
		}
		
		public function ios_send_push_notification($body=array(),$devices=array()){
			$body =$this->ios_body_generator($body);
			$fp=$this->start_apple_conection();
			
			
			if($fp){
				$payload = json_encode($body);
				//echo $payload;

				foreach($devices As $device){
					if(trim($device)=='')
						continue;
					// Build the binary notification
					$msg = chr(0) . pack('n', 32) . pack('H*', $device) . pack('n', strlen($payload)) . $payload;
					
					// Send it to the server
						$result = fwrite($fp, $msg, strlen($msg));
				}
					// Close the connection to the server
				fclose($fp);
				return true;
					
			}else{
				return false;
			}
			
		}
		
		//************* Android Part ************************
			public function set_android_certificate($API_key,$URL){
				$this->google_server=$URL;
				$this->google_api_key=$API_key;
			}
			public function send_android_push_notifications($message,$devices){
			
					$url = $this->google_server;
					define("GOOGLE_API_KEY",$this->google_api_key);
					$headers = array(
						'Authorization: key=' . GOOGLE_API_KEY,
						'Content-Type: application/json'
					);
					// Open connection
					$ch = curl_init();
			 
					// Set the url, number of POST vars, POST data
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 
					// Disabling SSL Certificate support temporarly
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 
					foreach($devices As $user){
						//echo $user;
						$fields = array(
							'registration_ids' => array($user),
							'data' => $message,
						);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
						// Execute post
						$result = curl_exec($ch);
					
						if ($result === FALSE) {
							die('Curl failed: ' . curl_error($ch));
							//return false;
						}
					}
					// Close connection
					curl_close($ch);
					return true;
			
			}
	
	
	}

?>
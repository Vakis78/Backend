<?php

class FcmNotification{
	private $title;
	private $message;
	private $image_url;
	private $action="";
	private $action_destination="";
    private $data=FALSE;
    private $api_key;
    private $token;
    private $topic;
	
	function __construct(){
        $this->api_key=FcmNotification::getDefaultApiKey();
    }
    
    public static function getDefaultApiKey(){
        $apiKey="AAAAiitpUQg:APA91bHAaw-j9ZOJtyGsAubQhqWKgKJOlV2UrrM3Klb9sGSn6JiKqLUaV_4nfO-OPxjgnDZMo1m5P7Zygp56cskziP7eDu23fNygv5REFxc1pfiiRdhFRprCkVgTCN-E6EYFHlRiyMb0";
        return $apiKey;
    }

    public function setApiKey($ak){
        $this->api_key = $ak;
    }

    public function setToken($tk){
        $this->token=$tk;
        if ($this->token!="0"){
            $this->topic="0";
        }
    }

    public function setTopic($tp){
        $this->topic=$tp;
        if ($this->topic!="0"){
            $this->token="0";
        }
    }
 
	public function setTitle($title){
		$this->title = $title;
	}
 
	public function setMessage($message){
		$this->message = $message;
	}
 
	public function setImage($imageUrl){
		$this->image_url = $imageUrl;
	}
 
	public function setAction($action){
		$this->action = $action;
	}
 
	public function setActionDestination($actionDestination){
		$this->action_destination = $actionDestination;
	}
 
	public function setPayload($data){
		$this->data = $data;
	}
	
	public function getMessageData(){
		$notification = array();
		$notification['title'] = $this->title;
        $notification['message'] = $this->message;
		$notification['image'] = $this->image_url;
		$notification['action'] = $this->action;
        $notification['action_destination'] = $this->action_destination;
        $notification["data"]=$this->data;
		return $notification;
    }
    
    public function sendNotification(){
        $ntobj=array("title"=>$this->title,"body"=>$this->message,"image"=>$this->image_url);
        $fields=array();
        if ($this->token!="0"){
            $fields = array(
                'to' => $this->token,
                'notification' => $ntobj,
            );
        }else if ($this->topic!="0"){
            $fields = array(
                'to' => '/topics/' . $this->topic,
                'notification' => $ntobj,
            );
        }
        if ($this->data!==FALSE){
            $fields['data']=$this->data;
        }
        $this->sendOut($fields);
    }

    public function sendMessageNotification(){
        $requestData = $this->getMessageData();
        $fields=array();
        if ($this->token!="0"){
            $fields = array(
                'to' => $this->token,
                'data' => $requestData,
            );
        }else if ($this->topic!="0"){
            $fields = array(
                'to' => '/topics/' . $this->topic,
                'data' => $requestData,
            );
        }
        $this->sendOut($fields);
    }

    private function sendOut($fields){
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $this->api_key,
            'Content-Type: application/json'
        );
        
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarily
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        // Close connection
        curl_close($ch);
        // echo '<h2>Result</h2><hr/><h3>Request </h3><p><pre>';
        // echo json_encode($fields,JSON_PRETTY_PRINT);
        // echo '</pre></p><h3>Response </h3><p><pre>';
        // echo $result;
        // echo '</pre></p>';
        echo $result."<br />";
    }
}
?>
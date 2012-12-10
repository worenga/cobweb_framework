<?php 
	/**
	 * Todo: Attachment Support, Priority, Receipt
	 */

class CobWeb_Mail {
	
	private $fromAddress;
	private $fromName;
	private $to;
	private $cc;
	private $bcc;
	private $subject;
	private $message;
	private $messagetype;
	
	public function __construct(){
		$this->fromAddress = null;
		$this->fromName = null;
		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
		$this->subject = null;
		$this->message = null;
		$this->messagetype = 'plaintext';
	}
	
	public function from($name,$address){
		$this->fromName = $name;
		$this->fromAddress = $address;
	}
	
	public function to($name,$address){
		$this->to[$address] = $name;
	}
	
	public function cc($name,$address){
		$this->cc[$address] = $name;
	}
	
	public function bcc($name,$address){
		$this->bcc[$address] = $name;
	}
	
	public function subject($subject){
		$this->subject=$subject;
	}
	
	public function message($message){
		$this->message = $message;
	}
	
	
	private function generateHeaders(){
		
		if($this->fromName == null || $this->fromAddress == null){
			CobWeb::o('Console')->warning('No From Header in Mail');
			return false;
		}
		$headers  = 'From: '.$this->fromName.'<'.$this->fromAddress.'>'.cw_system_crlf;
		$headers .= 'Reply-To: '.$this->fromAddress.cw_system_crlf;
		$headers .= 'Return-Path: '.$this->fromAddress.cw_system_crlf;
		$headers .= 'MIME-Version: 1.0' . cw_system_crlf;
		
		if(empty($this->to)&&empty($this->cc)&&empty($this->bcc)){
			CobWeb::o('Console')->warning('No Recipients for Mail');
			return false;
		}
		//$this->to is handled by CobWeb_Mail::send()
		$cc ='';
		foreach ($this->cc as $address => $name){
			$cc .= $name .'<'.$address.'>, ';
		}
		if(!empty($this->cc))$headers .= 'Cc: '.$cc.' '.cw_system_crlf;
		$bcc =''; 
		foreach ($this->bcc as $address => $name){
			$bcc .= $name .'<'.$address.'>, ';
		}
		if(!empty($this->bcc))$headers .= 'Bcc: '.$bcc.' '.cw_system_crlf;
		
		return $headers;
		
	}
	
	public function send(){
		$headers = $this->generateHeaders();
		if($headers){
			$to = '';
			foreach ($this->to as $address => $name){
				$to .= $name .'<'.$address.'>, ';
			}
			$to = substr($to,0,strlen($to)-2);
			
			return mail($to,$this->subject,$this->message,$headers);
			
		}else{
			return false;
		}
	}
	
	
}


?>
<?php

class NotificationTemplates{

	public static $TEMPLATES = array(
									'ask_meeting'=>array("text"=>"{customerName} has asked for a meeting with you","users"=>"seller"),
									'meet_confirm_wait'=>array("text"=>"Your meeting with {sellerName} is awaiting for confirmation","users"=>"customer"),	
									'declined'=>array("text"=>"{sellerName} has declined meeting you at {place} on {availability}","users"=>"customer"),
									'expired'=>array("text"=>"Your meeting with {sellerName} has been expired","users"=>"customer"),
									'confirmed'=>array("text"=>"{sellerName} confirmed meeting you at {place} on {availability}","users"=>"customer"),
									'meeting'=>array("text"=>"You are meeting {customerName} at {place} on {availability}","users"=>"seller"),
									//'meeting_provider'=>array("text"=>"You are meeting this person at ","users"=>"creator"),
									'reminder'=>array("text"=>"Reminder: You are meeting {customerName} at {place} on {availability}","users"=>"customer"),
									'reminder2'=>array("text"=>"Reminder: You are meeting {sellerName} at {place} on {availability}","users"=>"seller"),
									'meeting_code'=>array("text"=>"Give this meeting code \"{customerCode}\" at the café to {sellerName} to confirm you’re meeting the right person.","users"=>"customer"),
									'meeting_code2'=>array("text"=>"Give this meeting code ({sellerCode}) at the café to {customerName} to confirm you’re meeting the right person.","users"=>"seller"),
									'ask_code'=>array("text"=>"Ask meeting code at cafe from {sellerName} & enter here","users"=>"customer"),
									'ask_code2'=>array("text"=>"Ask meeting code at cafe from {customerName} & enter here","users"=>"seller"),
									'completed'=>array("text"=>"Your meeting with {sellerName} has been completed","users"=>"customer"),
									'completed2'=>array("text"=>"Your meeting with {customerName} has been completed","users"=>"seller"),
									'chat'=>array("text"=>"","users"=>"")
								);
}
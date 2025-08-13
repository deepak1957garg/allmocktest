<?php
include_once dirname(__FILE__) . '/../dao/NotificationReadDao.php';
include_once dirname(__FILE__) . '/../dao/NotificationWriteDao.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';

class EmailNotifications{
	private $cread;
	private $cwrite;

	public function __construct(){
		$this->cread = new NotificationReadDao();
		$this->cwrite = new NotificationWriteDao();
	}

	public function sendVerifyEmailMail($uid,$email){
			$sub = "Things: Verify Your Email";

			$html = "Please Verify your email for login in Things App by clicking link below<br /><br />";
			$html.= "<a href=\"https://dl.thingsapp.co/email-verify/" . $uid . "\">Click here to verify</a><br /><br />";
			$html.= 'Best,<br />';
			$html.= 'Things App Team<br /><br />';

			Utils::sendCronMail($sub,$html,$email);
	}	

	public function sendNewSignupInfoMail($user){
		$notif = new EmailNotification();
		$type = "new_signup";
		$list = $this->cread->getList($notif,array('uid'=>$user->getValue('uid'),'type'=>$type));
		if(count($list)==0){
			$sub = "HumanTales.LIVE: New User SignUp " . $user->getValue('name');

			$html = "A new user has signed up on HumanTales<br /><br />";
			$html.= "Here are Details<br /><br />";
			$obj = $user->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);
		}
	}

	public function sendOpenRoomEmail($user){
		$notif = new EmailNotification();
		$type = "open_room";
		$sub = "HumanTales.LIVE: " . $user->getValue('name') . "'s room gone live";

			$html = $user->getValue('name') . "'s room gone live on HumanTales<br /><br />";
			$html.= "Here are Details<br /><br />";
			$obj = $user->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html,Constants::$ADMIN_MAIL_LIST);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);


	}

	public function sendCloseRoomEmail($user){
		$notif = new EmailNotification();
		$type = "open_room";
		$sub = "HumanTales.LIVE: " . $user->getValue('name') . "'s room is closed";

			$html = $user->getValue('name') . "'s room gone closed on HumanTales<br /><br />";
			$html.= "Here are Details<br /><br />";
			$obj = $user->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);


	}		

	public function sendJoinRoomEmail($user,$visitor){
		$notif = new EmailNotification();
		$type = "join_room";
		$sub = "HumanTales.LIVE: " . $visitor->getValue('name') . " joins " . $user->getValue('name') . "'s room";

			$html = $visitor->getValue('name') . " join " . $user->getValue('name') . "'s room";
			$html.= "Here are Details<br /><br />";
			$html.= "Host<br />";
			$obj = $user->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}
			$html.= "<br /><br />";

			$html.= "Visitor<br />";
			$obj = $visitor->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);


	}

	public function sendLeaveRoomEmail($user,$visitor){
		$notif = new EmailNotification();
		$type = "join_room";
		$sub = "HumanTales.LIVE: " . $visitor->getValue('name') . " leaves " . $user->getValue('name') . "'s room";

			$html = $visitor->getValue('name') . " leaves " . $user->getValue('name') . "'s room";
			$html.= "Here are Details<br /><br />";
			$html.= "Host<br />";
			$obj = $user->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}
			$html.= "<br /><br />";

			$html.= "Visitor<br />";
			$obj = $visitor->getObject();
			foreach($obj as $key=>$value){
				$html.= $key . " : " . $value . "<br />";
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);


	}

	public function sendReportAbuseEmailByHost($user,$visitor){
		$notif = new EmailNotification();
		$type = "report_abuse_by_host";
		$sub = "HumanTales.LIVE: Host " . $user->getValue('name') . " report abuse about visitor " . $visitor->getValue('name') . "";

			$html = "Host " . $user->getValue('name') . " report abuse about visitor " . $visitor->getValue('name') . "";
			$html.= "Here are Details<br /><br />";
			$html.= "Host<br />";
			$obj = $user->getShortInfo2();
			foreach($obj as $key=>$value){
				if($key!="pic")	$html.= $key . " : " . $value . "<br />";
				else $html.= $key . ' : <img src="' . $value . '" width="100" /><br />';
			}
			$html.= "<br /><br />";

			$html.= "Visitor<br />";
			$obj = $visitor->getShortInfo2();
			foreach($obj as $key=>$value){
				if($key!="pic")	$html.= $key . " : " . $value . "<br />";
				else $html.= $key . ' : <img src="' . $value . '" width="100" /><br />';
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$user->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);
	}

	public function sendReportAbuseEmailByVisitor($user,$visitor){
		$notif = new EmailNotification();
		$type = "report_abuse_by_visitor";
		$sub = "HumanTales.LIVE: Visitor " . $visitor->getValue('name') . " report abuse about host " . $user->getValue('name') . "";

			$html = "Visitor " . $visitor->getValue('name') . " report abuse about host " . $user->getValue('name') . "";
			$html.= "Here are Details<br /><br />";
			$html.= "Visitor<br />";
			$obj = $visitor->getShortInfo2();
			foreach($obj as $key=>$value){
				if($key!="pic")	$html.= $key . " : " . $value . "<br />";
				else $html.= $key . ' : <img src="' . $value . '" width="100" /><br />';
			}
			$html.= "<br /><br />";
			$html.= "Host<br />";
			$obj = $user->getShortInfo2();
			foreach($obj as $key=>$value){
				if($key!="pic")	$html.= $key . " : " . $value . "<br />";
				else $html.= $key . ' : <img src="' . $value . '" width="100" /><br />';
			}

			$html.= "<br />";

			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';

			Utils::sendCronMail2($sub,$html);
			$notif->setValue('uid',$visitor->getValue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);
	}

	public function sendCandidateSubscribeMail($name,$room_url,$email){
		$sub = "HumanTales.LIVE: Walk-In Interview Details";

		$html= 'Hey ' . $name . ',<br /><br />';
		$html.= 'You\'re all set for your walk-in interview! Here\'s the walk-in interview room link:<br /><br />';
		$html.= '<a href="' . $room_url . '">' . $room_url . '</a><br /><br />';
		// $html.= 'A few things to keep in mind:<br />';
		// $html.="<ul>";
		// $html.= '<li>This membership is valid for the next 30 days<br /></li>';
		// $html.= '<li>The company hosting the walk-in interview may end it earlier than scheduled, so please keep an eye on the time.<br /></li>';
		// $html.= '<li>The membership fee of ₹ 99/- is charged by HumanTales.LIVE for the services provided. The company hosting the walk-in interview does not charge any fees.<br /></li>';
		// $html.="</ul><br/>";
		$html.= 'You can help others find a job by sharing this walk-in interview on social media. Share the link with your friends if they\'re also looking for a change:<br /><br />';
		$html.= '<a href="' . $room_url . '">' . $room_url . '</a><br /><br />';
		$html.= 'Good luck and have a great career ahead!<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		Utils::sendCronMail($sub,$html,$email);


	}

	public function sendWorkEmailOtp($name,$email,$otp){
		$sub = "HumanTales.LIVE: Otp for Work Email Verification";

		$html= 'Hey ' . $name . ',<br /><br />';
		$html.= 'Your HumanTales Work Email verification code is ' . $otp . '.<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		Utils::sendCronMail($sub,$html,$email);
	}

	public function sendInvitationMail($user,$visitor){
		$sub = $user->getValue('name') . " is LIVE and inviting you to meet";

		$html= 'Hi ' . $visitor->getValue('name') . ',<br /><br />';
		$html.= $user->getValue('name') . ' is <a href="' . Config::$SERVER_URL . '/' . $user->getValue('uname') . '">LIVE</a> and inviting you to meet.';
		$html.='<br /><br />';
		$html.='<table>
			<tr>
			<td>
			<img src="' . $user->getUserPic() . '" style="width:50px; height:50px; border-radius:25px; object-fit:cover" />
			</td><td>' . $user->getValue('name') . ', ' . $user->getValue('designation') . '<br />' . $user->getValue('aboutme') . '</td>
			</tr>
		</table>';
		$html.='<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		//$user->setValue('email','deepak@thesnug.app');
		Utils::sendCronMail($sub,$html,$visitor->getValue('email'));
	}	

	public function sendNudgeMail($user,$visitor){
		$sub = $visitor->getValue('name') . " has nudged you to go LIVE";

		$html= 'Hi ' . $user->getValue('name') . ',<br /><br />';
		$html.= $visitor->getValue('name') . ' has nudged you to go <a href="' . Config::$SERVER_URL . '/' . $user->getValue('uname') . '">LIVE</a> on HumanTales.<br /><br />';
		$html.='<table>
			<tr>
			<td>
			<img src="' . $visitor->getUserPic() . '" style="width:50px; height:50px; border-radius:25px; object-fit:cover" />
			</td><td>' . $visitor->getValue('name') . ', ' . $visitor->getValue('designation') . '<br />' . $visitor->getValue('aboutme') . '</td>
			</tr>
		</table>';
		$html.='<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		//$user->setValue('email','deepak@thesnug.app');
		Utils::sendCronMail($sub,$html,$user->getValue('email'));
	}

	public function sendLiveNudgeMail($user,$visitor){
		$sub = "Your nudge worked. " . $user->getValue('name') . " is LIVE";

		$html= 'Hi ' . $visitor['name'] . ',<br /><br />';
		$html.= $user->getValue('name') . ' is LIVE. Rush to <a href="' . Config::$SERVER_URL . '">Human Tales</a> to meet ' . $user->getValue('name') . '<br /><br />';
		$html.='<table>
			<tr>
			<td>
			<img src="' . $user->getUserPic() . '" style="width:50px; height:50px; border-radius:25px; object-fit:cover" />
			</td><td>' . $user->getValue('name') . ', ' . $user->getValue('designation') . '<br />' . $user->getValue('aboutme') . '</td>
			</tr>
		</table>';
		$html.='<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		//$user->setValue('email','deepak@thesnug.app');
		Utils::sendCronMail($sub,$html,$visitor['email']);
	}

	public function sendRoomActivationMail($name,$host_url,$email){
		$sub = "Your Walk-In Room needs activation";

		$html= 'Hi ' . $name . ',<br /><br />';
		$html.= 'Your walk-in interview room is ready to go! But first, you need to activate it.<br /><br />';
		$html.= 'Just click the link below to get started: <br /> <a href="' . $host_url . '">' . $host_url . '</a><br /><br />';
		$html.= 'Once you activate your room, you\'ll be able to explore the controls and get a feel for how it works. And don\'t forget - once you activate your room, we\'ll feature it on the homepage to attract even more candidates.<br /><br />';
		$html.= 'Good luck and happy interviewing!<br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		//Utils::sendCronMail($sub,$html,$email);
	}

	public function sendRoomCreationMail($rid,$name,$url,$email){
		$notif = new EmailNotification();
		$type = "room_ready";
		$list = $this->cread->getList($notif,array('uid'=>$rid,'type'=>$type));
		if(count($list)==0){
			$sub = "Your Walk-In Room is ready";

			$html= 'Hi ' . $name . ',<br /><br />';
			$html.= 'Your walk-in interview room is ready! Here is the URL<br /><br />';
			$html.= '<a href="' . $url . '">' . $url . '</a><br /><br />';
			$html.= 'Share the link with your candidates.  We will gather their response and update you on the same page.<br /><br />';
			$html.= 'Good luck and happy interviewing!<br />';
			$html.= 'Best,<br />';
			$html.= 'HumanTales.LIVE Team<br /><br />';
			Utils::sendCronMail($sub,$html,$email);

			$notif->setValue('uid',$rid);
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);
		}

	}	

	public function sendRoomCreationSuccessMail($name,$email,$room_url,$host_url){
		$sub = "Your Walk-In Room is ready now to start LIVE interviews";

		$html= 'Hi ' .$name . ',<br /><br />';
		$html.= 'Things you must know and do:<br /><br />';

		$html.= '1. <strong>(Share this link with candidates) Candidate room URL - </strong> <a href="' . $room_url . '">' . $room_url . '</a><br />';
		$html.= 'For inviting candidates, you can share the above URL.<br />';
		$html.= 'You can announce on social media that you have opened the room for Walk-In interviews. This can attract the top talents to your room and also get visibility about your hiring efforts.<br />';
		$html.= 'Please share this freely with everyone, via email, social media, WhatsApp, etc<br /><br />';

		// $html.= '2. <strong>(PRIVATE LINK, can share with co-interviewers) Host room URL - </strong> <a href="' . $host_url . '">' . $host_url . '</a><br />';
		// $html.= 'Keep this link private. This is from where you will be able to control the room, call in candidates, review them, announce your messages to the queue, etc<br />';

		// $html.= 'NOTE: DO NOT SHARE THIS LINK WITH CANDIDATES OR ANYWHERE IN A PUBLIC SPACE.<br /><br />';

		$html.= '2. Keep the room open. Keep the camera ON and Smile.<br />';
		$html.= '3. Visitors on the website can see you LIVE and that attracts them to give the interview. It also sends a strong signal to the candidates that you are taking your hiring seriously.<br />';
		$html.= '4. When your room is not open, your potential candidates may walk into other interview rooms and you may lose out on many of them.<br /><br />';
		$html.= 'Reach out to us for any know-how, clarifications, questions or concerns.<br /><br />';
		$html.= 'Best,<br />';
		$html.= 'HumanTales.LIVE Team<br /><br />';

		//Utils::sendCronMail($sub,$html,$email);

		//Utils::sendCronMail($sub,$html,$email);
	}

	public function sendEmployerToJoinEmail($user,$email,$host_url,$title,$cname){
		$notif = new EmailNotification();
		$type = "employer_to_join";
		$list = $this->cread->getList($notif,array('uid'=>$user->getvalue('uid'),'type'=>$type));
		if(count($list)==0){
			$sub = "Conducting a walk-in? Welcome to HumanTales.LIVE to " . $user->getvalue('name') . " from " . $cname;
			if($email!='')	$sub = "Email - " . $sub;

			$html= trim('Hello ' . $user->getvalue('name')) .',<br /><br />';
			$html.= 'My name is Jayshree and I am the co-founder of <a href="https://www.HumanTales.LIVE/">HumanTales.LIVE</a>.<br /><br />';

			$html.= "Email : " . $email . '<br />';
			$html.= "Name : " . $user->getvalue('name') . '<br />';
			$html.= "Phone : " . $user->getvalue('mobile') . '<br /><br />';

			$html.= 'You recently posted about a walk-in interview. I have created a page specific for your walk in posting here: <br /><a href="' . $host_url . '">' . $title . '</a>.<br /><br />';

			$html.= 'You get a variety of tools for managing both on-site and online walk-in interviews, including scheduling, sending reminders, collecting RSVP, queue management, specialized rooms for online interviews, and the ability to take notes.<br /><br />';

			$html.= 'Employers are using our platform to conduct both on-site and online walk-in interviews.<br /><br />';

			$html.= 'Candidates are  discovering all the upcoming walk-in interviews in one place.<br /><br />';

			$html.= '<a href="https://www.HumanTales.LIVE/">HumanTales.LIVE</a> is easy and completely free to use and there are no hidden costs.<br /><br />';

			$html.= 'If you\'re interested in learning more, please feel free to request a call back or reply to this email and I\'ll be happy to assist you.<br /><br />';
			$html.= 'Thank you for your time<br /><br />';

			

			$html.= 'Best regards,<br />';
			$html.= 'Jayshree<br />';
			Utils::sendCronMail3('jayshree@HumanTales.LIVE',$sub,$html);

			$notif->setValue('uid',$user->getvalue('uid'));
			$notif->setValue('type',$type);
			$id = $this->cwrite->createObject($notif);
		}
	}	

}
?>
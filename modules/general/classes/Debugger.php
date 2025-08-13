<?php
class Debugger {
	private $log_arr = array();
	private $pagename;
	
	function __construct($pagename){ 
		$this->pagename=$pagename;
	}

	public function logStart($fn) {
		try{
			$log = array();
			$log['start']=microtime(true);
			$this->log_arr[$fn]=$log;
		}
		catch(Exception $ex){ }
    }
	
	public function logEnd($fn) {
		try{
			if(isset($this->log_arr[$fn])) $this->log_arr[$fn]['end']=microtime(true);
		}
		catch(Exception $ex){ }
    }

	public function logEndAndStart($fn,$startfn) {
		try{
			if(isset($this->log_arr[$fn])) $this->log_arr[$fn]['end']=microtime(true);
			$this->logStart($startfn);
		}
		catch(Exception $ex){ }
    }
	
	public function fetchLogHtml(){
		$ihtml='';
		try{			
			$ihtml="Page : " . $this->pagename . "\\n"; 
			foreach($this->log_arr as $key=>$log){
				$ihtml.= "" . $key . "\\n";
				$ihtml.= "time : " . round((($log['end']-$log['start'])*1000),4) . " msecs\\n";
			}
		}
		catch(Exception $ex){ }
		return $ihtml;
	}
	
	public function logInErrorConsole(){
		
		try{			
			error_log("Page : " . $this->pagename ); 
			foreach($this->log_arr as $key=>$log){
				error_log("" . $key );
				error_log("time : " . round((($log['end']-$log['start'])*1000),4) . " msecs");
			}
		}
		catch(Exception $ex){ }
	}
	public function fetchLogPlainText(){
		$ihtml='';
		try{			
			$ihtml="\nPage : " . $this->pagename . "\n\n"; 
			foreach($this->log_arr as $key=>$log){
				$ihtml.= "" . $key . "\n";
				$ihtml.= "time : " . round((($log['end']-$log['start'])*1000),4) . " msecs\n\n";
			}
		}
		catch(Exception $ex){ }
		return $ihtml;
	}
}
?>
<?php
class AuthBasic {
	
	// Generuje odcisk palca na podstawie użytkownika i adresu IP
	public function genFingerprint($algo){
		if($algo===null)
			$algo = 'sha512';
		$fp = hash_hmac($algo, $_SERVER['HTTP_USER_AGENT'], hash($algo, $_SERVER['REMOTE_ADDR'], true));
		return $fp;
	}

	// Tworzy losowy kod uwierzytelniający
	public function createCode(	$length=6, $min=1, $max=999999 ){
		$max = ($length>strlen($max)) ? str_pad($max,$length,9,STR_PAD_RIGHT) : substr($max,0,$length);
		return str_pad(mt_rand($min,$max),$length,'0',STR_PAD_LEFT);
	}

	// Tworzy token uwierzytelniający na podstawie adresu e-mail i ID użytkownika
	public function createAuthToken( $email, $id){
		$authCode = $this->createCode();
		$authDate = date("Y-m-d");
		$authHours = date("H:i:s");
		$addrIp = '127.0.0.1'; // TODO: Dodać kod do pobierania rzeczywistego adresu IP
		$opSys = 'Linux'; // TODO: Ustalić system operacyjny za pomocą whichBrowser
		$browser = 'FF'; // TODO: Ustalić przeglądarkę za pomocą whichBrowser
		$cont = array(
			'emlAuth'=>$email,'authCode'=>$authCode,
			'authDate'=>$authDate,'authHour'=>$authHours,
			'addrIp'=>$addrIp,'reqOs'=>$opSys,'reqBrw'=>$browser
		);
		
		// TODO: Zapisz dane do bazy danych za pomocą db->put()
		$tbl = 'cmsWebsiteAuth';
		$cols = 'session_id, usrId, addrIp, fingerprint, dateTime, content, email, authCode';
		$vals = '1234567890,$id,$addrIp,hash_hmac(sha512+USER_AGENT+hash()+TRUE),$dt,0,$eml,$code';
		$file = dirname(__FILE__).'/db.txt';
		file_put_contents($file,serialize($cont));
		$fData = file_get_contents($file);
		// var_dump(unserialize($fData));
		
		// Sprawdź, czy dane zostały zapisane poprawnie
		$tok = (unserialize($fData)==$cont) ? 0 : 'err:1045';
		$resp = ($tok===0) ? $cont : false;
		return $resp;
	}

	// Porównuje kod uwierzytelniający z danymi w bazie danych
	public function compAuthCode( $emlAuth, $idAuth, $authCode ){
		$tbl = 'cmsSessionAuth';
		$sql = 'dateTime';
		$opt['where'] = "email='{$emlAuth}' AND idZgl={$idAuth} AND authCode='{$authCode}'";
		
		// TODO: Pobierz dane z bazy danych i porównaj z danymi uwierzytelniającymi
		$res = $this->dbc->get(2,$sql,$tbl,$opt);
		if( is_array($res) )
			$res = true;
		Event::log('sql',(__METHOD__),null,$this->dbc->dbgInfo());	
		return $res;
	}
	
	public function doAuthByEmail( $person, $email ){}
	public function checkIfValidReqest( $person, $email ){}
	private function checkIfValidReqest2f( $emlAuth, $idAuth ){}
	public function verifyQuickRegCode($codeNo){}
}
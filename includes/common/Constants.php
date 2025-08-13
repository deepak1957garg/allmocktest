<?php 
class Constants {
	// servers
	public static $COOKIE_DOMAIN_PATH = '.sonder.app';

	//EMAIL
	public static $SERVER_ADMIN_NAME = 'Snug';
	public static $SERVER_ADMIN_EMAIL ='manish@thesnug.app';

	//PAYU
	public static $PAYU_DROP_PAY_MODES = "EMI,CASH,COD,AMEXZ";
	public static $PAYU_WEBSERVICE_URL = "https://info.payu.in/merchant/postservice.php?form=2";
	public static $TEST_PAYU_WEBSERVICE_URL = "https://test.payu.in/merchant/postservice.php?form=2";

	public static $GOOGEL_PLAY_STORE_URL = 'https://play.google.com/store/apps/details?id=app.things';
	public static $APPLE_APP_STORE_URL = 'https://apps.apple.com/app/id1497708613';

	public static $VIDEO_NON_CDN_PATH = 'https://firebasestorage.googleapis.com/v0/b/sonder-app-01.firebasestorage.app/o';
	public static $VIDEO_CDN_PATH = 'https://static.thingsapp.co'; //'https://d1aku7gsvt7x3p.cloudfront.net';


	public static $COOKIE_AUTH='jwauth';

	public static $FREE_TIPS_ON_SIGNUP=2;

	public static $DEFAULT_DB = 'jalwa';
	public static $TABLE_PREFIX = 'ht_';

}
?>
<?php
// include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
// include_once dirname(__FILE__) . '/../../../includes/common/CachingKeyTypes.php';
// include_once dirname(__FILE__) . '/../../../includes/db/MemcachedWrapper.php';
// include_once dirname(__FILE__) . '/../../../includes/common/CachingKeyManager.php';

class UserMapCachingDao {
	private $mwrapperobj;
	private $cachingkeymanager;

	function __construct($cluster='default'){
		// $this->mwrapperobj = new MemcachedWrapper();
		// $this->cachingkeymanager = new CachingKeyManager();
	}

}
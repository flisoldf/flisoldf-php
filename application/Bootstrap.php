<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initAutoLoader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
	}

	protected function _initCaches()
	{
	    $frontendOptions = array(
                         'lifetime'				   => (86400*7), /* 24 horas */
                         'automatic_serialization' => true
		);

		$backendOptions  = array(
		   					 'cache_dir'  => APPLICATION_PATH . '/../data/cache/'
		);
		$cache = Zend_Cache::factory(
				   'Core',
		       'File',
		       $frontendOptions,
		       $backendOptions
		);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	}
}


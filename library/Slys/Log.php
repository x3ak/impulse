<?php
/**
 * Slys log class. By default uses Firebug writter to log events in development enviroment
 * and Stream writter in production enviroment in APPLICATION_PATH/../data/logs/app.log file
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
class Slys_Log
{
	/**
	 * @var Zend_Log
	 */
	protected static $_log = null;

	/**
	 * Singelton pattern
	 */
	protected function __construct()
	{}

	/**
	 * Singelton approach
	 *
	 * @param Zend_Config $config
	 * @return Zend_Log
	 */
	public static function getInstance(Zend_Config $config = null)
	{
		if (is_null(self::$_log))
			self::_init($config);

		return self::$_log;
	}

	/**
	 * Log messages. Can be used without getInstance() call
	 *
	 * @param string $message message text
	 * @param int $messageType message priority
	 */
	public static function log($message, $messagePriority = Zend_Log::DEBUG)
	{
		if (is_null(self::$_log))
			self::_init();

		$trace = debug_backtrace();

    	self::$_log->log(
    		sprintf(
    			'Time: [%s] Microtime: [%f] File: [%s] Line: [%d] Function: [%s]',
    			date('H:i:s'),
    			microtime(),
    			$trace[0]["file"],
    			$trace[0]["line"],
    			$trace[1]["function"]
    		),
    		Zend_Log::INFO
    	);

		self::$_log->log($message, $messagePriority);
	}

	/**
	 * Initialization of the inner log variable
	 *
	 * @return void
	 */
	protected static function _init(Zend_Config $config = null)
	{
		self::$_log = new Zend_Log();

		if (!is_null($config) and $config instanceof Zend_Config and !empty($config->settings)) {
			self::$_log = Zend_Log::factory($config->settings);
		} else {
			if (!empty($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development')
				self::$_log->addWriter( new Zend_Log_Writer_Firebug() );
			else
				self::$_log->addWriter( new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/app.log') );
		}
	}
}
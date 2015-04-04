<?php

/**
 * This class allows to print log-messages into a file in a very simple
 * manner.
 * @author Tim Luginbühl (tynx)
 */
class Logger {
	/**
	 * The ressource of the file
	 */
	private static $logFile = null;

	/**
	 * How the date/time should be formated
	 */
	private static $dateFormat = '';

	/**
	 * Log errors?
	 */
	private static $logError = false;

	/**
	 * Log warnings?
	 */
	private static $logWarning = false;

	/**
	 * Log infos?
	 */
	private static $logInfo = false;

	/**
	 * Log debugs?
	 */
	private static $logDebug = false;

	/**
	 * The user of the current requests. It gets added to the line every
	 * time if set.
	 */
	private $user = null;

	/**
	 * The constructor does init the file and settings if it has not
	 * yet been configured.
	 * @param user if set it will be added to all the lines of this
	 * instance
	 */
	public function __construct($user = null) {
		if (Logger::$logFile === null) {
			$this->_init();
		}
		$this->user = $user;
	}

	/**
	 * Tries to close the file-ressource
	 */
	public function __destruct() {
		if (is_resource(Logger::$logFile)) {
			fclose(Logger::$logFile);
		}
	}

	/**
	 * This initializes/configures the class
	 */
	private function _init() {
		Logger::$dateFormat = Config::LOG_DATE_FORMAT;
		Logger::$logError = Config::LOG_ERROR;
		Logger::$logWarning = Config::LOG_WARNING;
		Logger::$logInfo = Config::LOG_INFO;
		Logger::$logDebug = Config::LOG_DEBUG;
		Logger::$logFile = fopen(Config::LOG_FILE, 'a');
	}

	/**
	 * This method writes the lines to the file. the level gets upper-
	 * cased.
	 * @param level the level of the current line
	 * @param msg the message/line to write
	 */
	private function _setMessage($level, $message) {
		$starters = array(date(Logger::$dateFormat), strtoupper($level));
		if ($this->user !== null) {
			$starters[] = $this->user;
		}
		$line = '[' . implode('][', $starters) . ']: ' . $message . "\n";
		fwrite(Logger::$logFile, $line);
	}

	/**
	 * This method logs an error. If deactivated in the config it (of
	 * course) does not.
	 * @param message the line to write
	 */
	public function error($message) {
		if (Logger::$logError) {
			$this->_setMessage('error', $message);
		}
	}

	/**
	 * This method logs an warning. If deactivated in the config it (of
	 * course) does not.
	 * @param message the line to write
	 */
	public function warning($message) {
		if (Logger::$logWarning) {
			$this->_setMessage('warning', $message);
		}
	}

	/**
	 * This method logs an info. If deactivated in the config it (of
	 * course) does not.
	 * @param message the line to write
	 */
	public function info($message) {
		if (Logger::$logInfo) {
			$this->_setMessage('info', $message);
		}
	}

	/**
	 * This method logs an debug. If deactivated in the config it (of
	 * course) does not.
	 * @param message the line to write
	 */
	public function debug($message) {
		if (Logger::$logDebug) {
			$this->_setMessage('debug', $message);
		}
	}
}

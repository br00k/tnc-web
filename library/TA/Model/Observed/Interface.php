<?php
interface TA_Model_Observed_Interface
{
	public static function attachStaticObserver(TA_Model_Observer_Interface $o);
	public static function detachStaticObserver(TA_Model_Observer_Interface $o);
	public function notifyObservers($method, $msg);

}
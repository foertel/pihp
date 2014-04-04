<?php
namespace PIHP\Driver;

require_once('WritingGpio.php');
require_once('ReadingGpio.php');

class HcSr04 {

	/**
	 * @var \PIHP\Base\WritingGPIO
	 */
	protected $trigger;

	/**
	 * @var \PIHP\Base\ReadingGPIO
	 */
	protected $echo;

	public function __construct($trigger, $echo) {
		$this->trigger = new \PIHP\Base\WritingGPIO($trigger);
		$this->echo = new \PIHP\Base\ReadingGPIO($echo);
	}

	public function getDistance() {
		// trigger sensor
		$this->trigger->on();
		usleep(5);
		$this->trigger->off();

		$timeout = (microtime(TRUE) + 0.1);

		// wait for the signal to appear
		do {
			$start = microtime(TRUE);
			if ($start > $timeout) return 0;
		} while (!$this->echo->read());

		$timeout = $timeout + 0.1;

		// signal is here, start measuring
		do {
			$end = microtime(TRUE);
			if ($end > $timeout) return 0;
		} while ($this->echo->read());

		// signal, ended.
		// sound will travel about 343 m/s
		// but the sound will travel to *and* from the object,
		// so divide time by 2 to get distance
		return (int)(($end - $start) * 34300 / 2);
	}
}

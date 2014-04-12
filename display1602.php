<?php
namespace PIHP\Driver;

require_once('WritingGpio.php');

class display1602 {

	const CHARACTER = TRUE;
	const COMMAND = FALSE;
	const WIDTH = 16;

	protected $delay = 100000;

	protected $lines = array(128, 192);

	protected $charIdenticator;

	protected $trigger;

	protected $data = array();

	public function __construct($rs, $e, $data) {
		$this->charIndicator = new \PIHP\Base\WritingGPIO($rs);
		$this->trigger = new \PIHP\Base\WritingGPIO($e);
		foreach ($data as $dataPin) {
			$this->data[] = new \PIHP\Base\WritingGPIO($dataPin);
		}
		
		foreach (array(51, 50, 40, 8, 15, 1, 6) as $initCommand) {
			$this->send($initCommand);
		}
		exit;

		$this->sendString('Hallo Finn, was geht ab?');
	}

	public function sendCommand($command) {
		$this->send($command);
	}

	public function sendString($string) {
		$this->charIdenticator->on();
		$this->delay();

		for ($line = 0; $line < min((strlen($string) / self::WIDTH), count($this->lines)); $line++) {
			$this->sendCommand($this->lines[$line]);
			for ($position = 0; $position < min(self::WIDTH, (strlen($string) - ($line * self::WIDTH))); $position++) {
				$this->send(ord($string[$position + ($line * self::WIDTH)]));
			}
		}
		
		$this->charIdenticator->off();
		$this->delay();
	}

	protected function send($decimal) {
		$binary = sprintf('%08d', decbin($decimal));

		/**
		 * We are using 4-bit mode to safe some pins. This means we have to
		 * send in two charges of 4 bit each.
		 */
		echo PHP_EOL;
		for ($split = 0; $split < 2; $split++) {
			for ($position = 0; $position <= 3; $position++) {
				if ($binary[$position + ($split * 4)]) {
					echo '1';
					$this->data[$position]->on();
				} else {
					echo '0';
					$this->data[$position]->off();
				}
			}
			$this->delay();

			$this->trigger->on();
			$this->delay();
			$this->trigger->off();
			$this->delay();
		}
	}

	protected function delay() {
		usleep($this->delay);
	}
}

declare(ticks = 1);
pcntl_signal(SIGTERM, function () {exit;});
pcntl_signal(SIGINT, function () {exit;});

$display = new display1602(20, 4, array(18, 27, 23, 25));


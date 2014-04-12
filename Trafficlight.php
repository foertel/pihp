<?php
namespace PIHP\Application;

require_once('WritingGPIO.php');

class Trafficlight {

	protected $red;

	protected $orange;

	protected $green;

	public function __construct($red, $orange, $green) {
		$this->red = new \PIHP\Base\WritingGPIO($red);
		$this->orange = new \PIHP\Base\WritingGPIO($orange);
		$this->green = new \PIHP\Base\WritingGPIO($green);
	}

	public function loop() {
		$iterator = 0;

		while (TRUE) {
			$this->distances[] = $this->distanceSensor->getDistance();
			$iterator++;

			if ($iterator >= 30) {
				$ledsToSwitch = floor($this->getAverageDistance($iterator) / 10);

				foreach (array_slice($this->leds, 0, (count($this->leds) - $ledsToSwitch)) as $led) {
					$led->on();
				}

				foreach (array_slice($this->leds, (count($this->leds) - $ledsToSwitch)) as $led) {
					$led->off();
				}

				$iterator = 0;
			}
		}
	}

	protected function getAverageDistance($numberOfDistances) {
		sort($this->distances);
		$this->distances = array_slice($this->distances, (int)($numberOfDistances * 0.25), (int)($numberOfDistances * 0.5));
		return (int)(array_sum($this->distances) / count($this->distances));
	}
}

declare(ticks = 1);
pcntl_signal(SIGTERM, function () {exit;});
pcntl_signal(SIGINT, function () {exit;});

$parkDistance = new ParkDistance(24, 25, array(4, 17, 18, 27, 22, 23));
$parkDistance->loop();

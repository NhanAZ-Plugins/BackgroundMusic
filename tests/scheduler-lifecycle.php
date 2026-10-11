<?php

declare(strict_types=1);

namespace pocketmine\event {
	interface Listener {}
}

namespace pocketmine\scheduler {
	final class ClosureTask {
		public function __construct(private \Closure $callback) {}

		public function run() : void {
			($this->callback)();
		}
	}

	final class TaskHandler {
		public bool $cancelled = false;

		public function __construct(public ClosureTask $task, public int $period) {}

		public function cancel() : void {
			$this->cancelled = true;
		}
	}

	final class TaskScheduler {
		/** @var list<TaskHandler> */
		public array $handlers = [];

		public function scheduleRepeatingTask(ClosureTask $task, int $period) : TaskHandler {
			return $this->handlers[] = new TaskHandler($task, $period);
		}

		public function tick() : void {
			foreach ($this->handlers as $handler) {
				if (!$handler->cancelled) {
					$handler->task->run();
				}
			}
		}
	}
}

namespace pocketmine\plugin {
	use pocketmine\scheduler\TaskScheduler;

	class PluginBase {
		private TaskScheduler $scheduler;

		public function __construct() {
			$this->scheduler = new TaskScheduler();
		}

		public function getScheduler() : TaskScheduler {
			return $this->scheduler;
		}
	}
}

namespace pocketmine\player {
	use pocketmine\network\mcpe\protocol\PlaySoundPacket;

	final class TestUuid {
		public function __construct(private string $value) {}

		public function toString() : string {
			return $this->value;
		}
	}

	final class TestPosition {
		public function __construct(public float $x, public float $y, public float $z) {}

		public function getX() : float { return $this->x; }
		public function getY() : float { return $this->y; }
		public function getZ() : float { return $this->z; }
	}

	final class TestSession {
		/** @var list<PlaySoundPacket> */
		public array $packets = [];

		public function sendDataPacket(PlaySoundPacket $packet) : void {
			$this->packets[] = $packet;
		}
	}

	final class Player {
		public TestPosition $position;
		public TestSession $session;
		public bool $online = true;

		public function __construct(private string $id) {
			$this->position = new TestPosition(1, 2, 3);
			$this->session = new TestSession();
		}

		public function getUniqueId() : TestUuid { return new TestUuid($this->id); }
		public function getPosition() : TestPosition { return $this->position; }
		public function getNetworkSession() : TestSession { return $this->session; }
		public function isOnline() : bool { return $this->online; }
	}
}

namespace pocketmine\event\player {
	use pocketmine\player\Player;

	final class PlayerJoinEvent {
		public function __construct(private Player $player) {}
		public function getPlayer() : Player { return $this->player; }
	}

	final class PlayerQuitEvent {
		public function __construct(private Player $player) {}
		public function getPlayer() : Player { return $this->player; }
	}
}

namespace pocketmine\network\mcpe\protocol {
	final class PlaySoundPacket {
		public string $soundName;
		public float $x;
		public float $y;
		public float $z;
		public float $volume;
		public float $pitch;
	}
}

namespace NhanAZ\libRegRsp {
	final class libRegRsp {
		public static int $unregisterCalls = 0;

		public static function unregister(object $plugin) : void {
			++self::$unregisterCalls;
		}
	}
}

namespace {
	use NhanAZ\BackgroundMusic\Main;
	use NhanAZ\libRegRsp\libRegRsp;
	use pocketmine\event\player\PlayerJoinEvent;
	use pocketmine\event\player\PlayerQuitEvent;
	use pocketmine\player\Player;

	require_once __DIR__ . '/../src/NhanAZ/BackgroundMusic/Main.php';

	$plugin = new class extends Main {
		public function shutdown() : void { $this->onDisable(); }
	};
	$first = new Player('same-player');
	$plugin->onJoin(new PlayerJoinEvent($first));
	$firstHandler = $plugin->getScheduler()->handlers[0];
	if ($firstHandler->period !== 4400) { throw new \RuntimeException('Wrong repeat interval'); }
	$first->position->x = 8;
	$plugin->getScheduler()->tick();
	if (count($first->session->packets) !== 1 || $first->session->packets[0]->x !== 8.0) {
		throw new \RuntimeException('Sound packet did not use the current position');
	}

	$second = new Player('same-player');
	$plugin->onJoin(new PlayerJoinEvent($second));
	$secondHandler = $plugin->getScheduler()->handlers[1];
	if (!$firstHandler->cancelled || $secondHandler->cancelled) {
		throw new \RuntimeException('Reconnect did not replace the old task');
	}
	$plugin->onQuit(new PlayerQuitEvent($second));
	if (!$secondHandler->cancelled) { throw new \RuntimeException('Quit did not cancel playback'); }

	$offline = new Player('offline-player');
	$plugin->onJoin(new PlayerJoinEvent($offline));
	$offline->online = false;
	$plugin->getScheduler()->tick();
	if (!$plugin->getScheduler()->handlers[2]->cancelled) {
		throw new \RuntimeException('Offline task did not cancel itself');
	}

	$active = new Player('active-player');
	$plugin->onJoin(new PlayerJoinEvent($active));
	$plugin->shutdown();
	if (!$plugin->getScheduler()->handlers[3]->cancelled || libRegRsp::$unregisterCalls !== 1) {
		throw new \RuntimeException('Disable did not cancel playback and unregister the pack');
	}

	echo "PASS: playback position, reconnect, quit, offline, and disable\n";
}

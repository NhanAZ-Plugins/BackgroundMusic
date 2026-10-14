<?php

declare(strict_types=1);

namespace NhanAZ\BackgroundMusic;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Main extends PluginBase implements Listener {
	private const REPEAT_INTERVAL_TICKS = 4400;

	/** @var array<string, \Closure(): void> */
	private array $cancelTasks = [];

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	protected function onDisable(): void {
		foreach ($this->cancelTasks as $cancel) {
			$cancel();
		}
		$this->cancelTasks = [];
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$id = $player->getUniqueId()->toString();
		$this->cancelTask($id);
		$handler = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player, $id): void {
			if (!$player->isOnline()) {
				$this->cancelTask($id);
				return;
			}
			$position = $player->getPosition();
			$packet = new PlaySoundPacket();
			$packet->soundName = "music.game";
			$packet->x = $position->getX();
			$packet->y = $position->getY();
			$packet->z = $position->getZ();
			$packet->volume = 1.0;
			$packet->pitch = 1.0;
			$player->getNetworkSession()->sendDataPacket($packet);
		}), self::REPEAT_INTERVAL_TICKS);
		$this->cancelTasks[$id] = static function () use ($handler): void {
			$handler->cancel();
		};
	}

	public function onQuit(PlayerQuitEvent $event): void {
		$this->cancelTask($event->getPlayer()->getUniqueId()->toString());
	}

	private function cancelTask(string $id): void {
		if (isset($this->cancelTasks[$id])) {
			$this->cancelTasks[$id]();
			unset($this->cancelTasks[$id]);
		}
	}
}

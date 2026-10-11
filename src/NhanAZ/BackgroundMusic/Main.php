<?php

declare(strict_types=1);

namespace NhanAZ\BackgroundMusic;

use NhanAZ\libRegRsp\libRegRsp;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Main extends PluginBase implements Listener {

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		libRegRsp::compileAndRegister($this);
	}

	protected function onDisable(): void {
		libRegRsp::unregister($this);
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$playerPos = $player->getPosition();
		$packet = PlaySoundPacket::create(
			soundName: "C418Sweden",
			x: $playerPos->getX(),
			y: $playerPos->getY(),
			z: $playerPos->getZ(),
			volume: 1.0,
			pitch: 1.0
		);
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player, $packet): void {
			if ($player->isOnline()) {
				$player->getNetworkSession()->sendDataPacket($packet);
			}
		}), 4400); /** 3m40s = 220s * 20 tick =  4400 tick */
	}
}

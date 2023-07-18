<?php

namespace glad\kit;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\item\Item;

class KitManager
{

    private array $kits = [];

    public function __construct(
        private Config $kitStorage
    ) {
        foreach ($this->kitStorage->getAll() as $name => $data) {
            $this->kits[$name] = new Kit($name, $data["whocreate"], $data["items"], $data["armor"]);
        }
    }

    public function getKitByName(string $name): ?kit
    {
        return $this->kits[$name] ?? null;
    }

    /**
     * @return kit[]
     */
    public function getKits(): array
    {
        return $this->kits;
    }

    public  function createKit(string $name, Player $player): void
    {
        $this->kits[$name] = new Kit($name, $player->getName(), Utils::cereal($player->getInventory()->getContents()), Utils::cereal($player->getArmorInventory()->getContents())); //mesmo com armor
    }

    public function deleteKit(string $name): void
    {
        unset($this->kits[$name]);
        $this->kitStorage->remove($name);
    }

    public function saveKits(): void
    {
        foreach ($this->kits as $kit) {
            $this->kitStorage->set($kit->getName(), $kit->toArray());
        }
        $this->kitStorage->save();
    }
}

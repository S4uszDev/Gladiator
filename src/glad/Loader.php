<?php

namespace glad;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\sound\XpLevelUpSound;
use glad\kit\Kit;
use glad\kit\KitManager;

class Loader extends \pocketmine\plugin\PluginBase
{

    use SingletonTrait;

    private KitManager $kitManager;

    protected function onLoad(): void {
        $this->kitManager = new KitManager(new Config($this->getDataFolder() . 'kits.yml'));
        self::$instance = $this;
    }

    protected function onDisable(): void
    {
        $this->kitManager->saveKits();
    }

    /**
     * @return KitManager
     */
    public function getKitManager(): KitManager
    {
        return $this->kitManager;
    }


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === 'createkit') {
            if ($sender instanceof Player) {
                if (empty($args)) {
                    $sender->sendMessage("§cUse: /createkit (nome)");
                    return true;
                }
                $this->kitManager->createKit($args[0], $sender);
                $sender->sendMessage("§aKit criado com sucesso!");
                $sender->getWorld()->addSound($sender->getPosition(), new XpLevelUpSound(30), [$sender]);
                return true;
            }
        }
        if ($command->getName() === 'listkit'){
            if($sender instanceof Player) {
                $messagelist = [
                    "§aLista de Kits Disponíveis",
                    ""
                ];
                foreach ($this->kitManager->getKits() as $kit){
                    $messagelist[] = "§r§7- §a" . $kit->getName();

                }
                $sender->sendMessage(implode("\n", $messagelist));
                return true;
            }
        }
        if ($command->getName() === 'givekit'){
            if ($sender instanceof Player) {
                if (count($args) < 2) {
                    $sender->sendMessage('§cUse: /givekit (kit) (mundo:servidor)');
                    return true;
                }
                $kit = $this->kitManager->getKitByName($args[0]);
                if (is_null($kit)){
                    $sender->sendMessage("§cO Kit não foi localizado!");
                    return true;
                }
                $players = $args[1] === 'mundo' ? $sender->getWorld()->getPlayers() : $this->getServer()->getOnlinePlayers();
                foreach ($players as $player){
                    $player->getInventory()->setContents($kit->getItems());
                    $player->getArmorInventory()->setContents($kit->getArmor());
                }
                $sender->getWorld()->addSound($sender->getPosition(), new XpLevelUpSound(30), [$sender]);
                $sender->sendMessage("§aSucesso!");
                $this->getServer()->broadcastMessage("§a[Gladiador] §7Todos jogadores receberam o kit!");
                return true;
            }
        }
        if ($command->getName() == 'removekit'){
            if ($sender instanceof Player){
                if (empty($args)){
                    $sender->sendMessage("§cVocê esqueceu de dizer o kit!");
                    return true;
                }
                $kit = $this->kitManager->getKitByName($args[0]);
                if (is_null($kit)){
                    $sender->sendMessage("§cO Kit não foi localizado!");
                    return true;
                }
                $this->kitManager->deleteKit($kit->getName());
                $sender->getWorld()->addSound($sender->getPosition(), new XpLevelUpSound(30), [$sender]);
                $sender->sendMessage("§aKit deletado com sucesso!");
                return true;
            }
        }

        if ($command->getName() == 'tpall'){
            if ($sender instanceof Player) {
                if (empty($args[0])){
                    $sender->sendMessage("§cUse: /tpall (mundo:servidor)");
                    return true;
                }
                $players = $args[0] === 'mundo' ? $sender->getWorld()->getPlayers() : $this->getServer()->getOnlinePlayers();
                foreach ($players as $player) {
                    $player->teleport($sender->getPosition());
                }
                $sender->getWorld()->addSound($sender->getPosition(), new XpLevelUpSound(30), [$sender]);
                $sender->sendMessage("§aSucesso!");
                $this->getServer()->broadcastMessage("§a[Gladiador] §7Todos jogadores foram puxados!");
            }
        }
     return true;
    }
}
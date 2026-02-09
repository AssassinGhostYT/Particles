<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\commands;

use AssassinGhost\Particles\forms\MainForm;
use AssassinGhost\Particles\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ParticleCommand extends Command {
    
    private Main $plugin;
    
    public function __construct(Main $plugin) {
        parent::__construct("p", "Abrir menú de partículas", "/p", ["particles"]);
        $this->setPermission("particles.command");
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede usarlo un jugador");
            return false;
        }
        
        if (!$this->testPermission($sender)) {
            return false;
        }
        
        $sender->sendForm(new MainForm($this->plugin));
        
        return true;
    }
}

<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\forms;

use AssassinGhost\Particles\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MainForm implements Form {
    
    private Main $plugin;
    private array $categories = [];
    
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }
    
    public function jsonSerialize(): array {
        $config = $this->plugin->getParticleConfig();
        $buttons = [];
        $this->categories = [];
        
        // Categoría Feet
        if ($config->getNested("categorias.feet.enabled", true)) {
            $buttons[] = ["text" => "§e§lPartículas de Pie\n§r§7Click para ver opciones"];
            $this->categories[] = "feet";
        }
        
        // Categoría Body
        if ($config->getNested("categorias.body.enabled", true)) {
            $buttons[] = ["text" => "§b§lPartículas de Cuerpo\n§r§7Click para ver opciones"];
            $this->categories[] = "body";
        }
        
        // Categoría Head
        if ($config->getNested("categorias.head.enabled", true)) {
            $buttons[] = ["text" => "§d§lPartículas de Cabeza\n§r§7Click para ver opciones"];
            $this->categories[] = "head";
        }
        
        return [
            "type" => "form",
            "title" => "§l§8MENÚ DE PARTÍCULAS",
            "content" => "§7Selecciona una categoría para gestionar tus partículas:\n\n§aVerde §7= Activado | §cRojo §7= Desactivado",
            "buttons" => $buttons
        ];
    }
    
    public function handleResponse(Player $player, $data): void {
        if ($data === null) return;
        
        if (isset($this->categories[$data])) {
            $category = $this->categories[$data];
            $player->sendForm(new CategoryForm($this->plugin, $category, $player->getName()));
        }
    }
}

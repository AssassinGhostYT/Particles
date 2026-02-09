<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\forms;

use AssassinGhost\Particles\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ParticleToggleForm implements Form {
    
    private Main $plugin;
    private string $category;
    private string $particleKey;
    private string $playerName;
    
    public function __construct(Main $plugin, string $category, string $particleKey, string $playerName) {
        $this->plugin = $plugin;
        $this->category = $category;
        $this->particleKey = $particleKey;
        $this->playerName = $playerName;
    }
    
    public function jsonSerialize(): array {
        $config = $this->plugin->getParticleConfig();
        $manager = $this->plugin->getParticleManager();
        
        $configKey = "particles_" . $this->category;
        $particleData = $config->getNested($configKey . "." . $this->particleKey, []);
        $particleName = $particleData['nombre'] ?? $this->particleKey;
        
        $isActive = $manager->hasParticleActive($this->playerName, $this->category, $this->particleKey);
        
        $buttons = [];
        
        if ($isActive) {
            $buttons[] = ["text" => "§c§lDESACTIVAR PARTÍCULA\n§r§7Click para apagar"];
        } else {
            $buttons[] = ["text" => "§a§lACTIVAR PARTÍCULA\n§r§7Click para encender"];
        }
        
        $buttons[] = ["text" => "§e§l← Volver a la Categoría"];
        $buttons[] = ["text" => "§c§l← Volver al Menú Principal"];
        
        $status = $isActive ? "§aACTIVADA" : "§cDESACTIVADA";
        
        return [
            "type" => "form",
            "title" => "§l§8GESTIONAR PARTÍCULA",
            "content" => "§7Partícula: §f" . $particleName . "\n" .
                        "§7Estado actual: " . $status . "\n" .
                        "§7Tipo: §f" . ($particleData['tipo'] ?? 'DESCONOCIDO') . "\n\n" .
                        "§7Selecciona una opción:",
            "buttons" => $buttons
        ];
    }
    
    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            $player->sendForm(new CategoryForm($this->plugin, $this->category, $player->getName()));
            return;
        }
        
        $manager = $this->plugin->getParticleManager();
        $isActive = $manager->hasParticleActive($player->getName(), $this->category, $this->particleKey);
        
        switch ($data) {
            case 0:
                // Toggle partícula
                if ($isActive) {
                    $manager->deactivateParticle($player->getName(), $this->category, $this->particleKey);
                    $player->sendMessage("§cPartícula desactivada correctamente");
                } else {
                    $success = $manager->activateParticle($player->getName(), $this->category, $this->particleKey);
                    
                    if ($success) {
                        $player->sendMessage("§aPartícula activada correctamente");
                    } else {
                        $player->sendMessage("§cHas alcanzado el límite máximo de partículas activas");
                    }
                }
                
                // Volver a la categoría
                $player->sendForm(new CategoryForm($this->plugin, $this->category, $player->getName()));
                break;
                
            case 1:
                // Volver a categoría
                $player->sendForm(new CategoryForm($this->plugin, $this->category, $player->getName()));
                break;
                
            case 2:
                // Volver al menú principal
                $player->sendForm(new MainForm($this->plugin));
                break;
        }
    }
}

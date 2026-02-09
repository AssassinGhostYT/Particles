<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\forms;

use AssassinGhost\Particles\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CategoryForm implements Form {
    
    private Main $plugin;
    private string $category;
    private array $particleKeys = [];
    private array $categoryNames = [
        "feet" => "§ePartículas de Pie",
        "body" => "§bPartículas de Cuerpo",
        "head" => "§dPartículas de Cabeza"
    ];
    private string $playerName;
    
    public function __construct(Main $plugin, string $category, string $playerName) {
        $this->plugin = $plugin;
        $this->category = $category;
        $this->playerName = $playerName;
    }
    
    public function jsonSerialize(): array {
        $config = $this->plugin->getParticleConfig();
        $manager = $this->plugin->getParticleManager();
        
        $buttons = [];
        $this->particleKeys = [];
        
        // Usar el mismo patrón que LobbyCore - getAll() y acceso directo
        $allConfig = $config->getAll();
        $configKey = "particles_" . $this->category;
        $particlesData = $allConfig[$configKey] ?? [];
        
        // Si la categoría tiene partículas dentro
        if (!empty($particlesData) && is_array($particlesData)) {
            foreach ($particlesData as $key => $data) {
                // Verificamos que sea un array y que la partícula esté habilitada
                if (!is_array($data)) continue;
                if (!($data["enabled"] ?? true)) continue;
                    
                // Chequeamos si el jugador la tiene puesta
                $isActive = $manager->hasParticleActive($this->playerName, $this->category, $key);
                
                $color = $isActive ? "§a" : "§c";
                $status = $isActive ? "§r§a[ACTIVADO]" : "§r§c[DESACTIVADO]";
                $nombreFinal = $data["nombre"] ?? $key;
                
                // Añadimos el botón a la lista
                $buttons[] = [
                    "text" => $color . "§l" . $nombreFinal . "\n" . $status
                ];
                
                // Guardamos la key para saber qué partícula tocó el jugador
                $this->particleKeys[] = $key;
            }
        }
        
        // El botón de volver SIEMPRE después del bucle
        $buttons[] = ["text" => "§c§l← Volver al Menú Principal"];
        
        $titulo = $this->categoryNames[$this->category] ?? "PARTÍCULAS";
        
        return [
            "type" => "form",
            "title" => "§l§8" . strtoupper($titulo),
            "content" => "§7Selecciona una partícula para activar o desactivar:",
            "buttons" => $buttons
        ];
    }
    
    public function handleResponse(Player $player, $data): void {
        if ($data === null) return;
        
        // Si el índice coincide con el tamaño de particleKeys, es el botón de Volver
        if ($data === count($this->particleKeys)) {
            $player->sendForm(new MainForm($this->plugin));
            return;
        }
        
        // Si es una partícula válida
        if (isset($this->particleKeys[$data])) {
            $particleKey = $this->particleKeys[$data];
            $player->sendForm(new ParticleToggleForm($this->plugin, $this->category, $particleKey, $this->playerName));
        }
    }
}

<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\particles;

use AssassinGhost\Particles\Main;
use pocketmine\utils\Config;

class ParticleManager {
    
    private Main $plugin;
    private Config $playerData;
    private array $activeParticles = [];
    
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->loadData();
    }
    
    public function loadData(): void {
        $this->playerData = new Config($this->plugin->getDataFolder() . "players.yml", Config::YAML);
        
        foreach ($this->playerData->getAll() as $playerName => $data) {
            $this->activeParticles[strtolower($playerName)] = $data['particles'] ?? [];
        }
    }
    
    public function saveAllData(): void {
        $data = [];
        foreach ($this->activeParticles as $playerName => $particles) {
            $data[$playerName] = ['particles' => $particles];
        }
        $this->playerData->setAll($data);
        $this->playerData->save();
    }
    
    public function activateParticle(string $playerName, string $category, string $particleKey): bool {
        $playerName = strtolower($playerName);
        $maxParticles = $this->plugin->getParticleConfig()->getNested("settings.max_particulas_por_jugador", 3);
        
        if (!isset($this->activeParticles[$playerName])) {
            $this->activeParticles[$playerName] = [];
        }
        
        // Contar partículas activas
        $activeCount = count($this->activeParticles[$playerName]);
        
        // Si ya está activa, no hacer nada
        if (isset($this->activeParticles[$playerName][$category . ":" . $particleKey])) {
            return true;
        }
        
        // Verificar límite
        if ($activeCount >= $maxParticles) {
            return false;
        }
        
        $this->activeParticles[$playerName][$category . ":" . $particleKey] = [
            'category' => $category,
            'particle' => $particleKey,
            'enabled_at' => time()
        ];
        
        return true;
    }
    
    public function deactivateParticle(string $playerName, string $category, string $particleKey): void {
        $playerName = strtolower($playerName);
        unset($this->activeParticles[$playerName][$category . ":" . $particleKey]);
    }
    
    public function hasParticleActive(string $playerName, string $category, string $particleKey): bool {
        $playerName = strtolower($playerName);
        return isset($this->activeParticles[$playerName][$category . ":" . $particleKey]);
    }
    
    public function getActiveParticles(string $playerName): array {
        $playerName = strtolower($playerName);
        return $this->activeParticles[$playerName] ?? [];
    }
    
    public function deactivateAllParticles(string $playerName): void {
        $playerName = strtolower($playerName);
        unset($this->activeParticles[$playerName]);
    }
}

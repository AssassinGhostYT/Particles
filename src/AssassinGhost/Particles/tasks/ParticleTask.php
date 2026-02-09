<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\tasks;

use AssassinGhost\Particles\Main;
use AssassinGhost\Particles\particles\categories\BodyParticles;
use AssassinGhost\Particles\particles\categories\FeetParticles;
use AssassinGhost\Particles\particles\categories\HeadParticles;
use pocketmine\scheduler\Task;

class ParticleTask extends Task {
    
    private Main $plugin;
    private FeetParticles $feetParticles;
    private BodyParticles $bodyParticles;
    private HeadParticles $headParticles;
    
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->feetParticles = new FeetParticles('FLAME', 3);
        $this->bodyParticles = new BodyParticles('ENCHANTMENT_TABLE', 5);
        $this->headParticles = new HeadParticles('VILLAGER_HAPPY', 3);
    }
    
    public function onRun(): void {
        $manager = $this->plugin->getParticleManager();
        $config = $this->plugin->getParticleConfig();
        
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $activeParticles = $manager->getActiveParticles($player->getName());
            
            foreach ($activeParticles as $key => $data) {
                $category = $data['category'];
                $particleKey = $data['particle'];
                
                // Verificar si la categoría está habilitada
                if (!$config->getNested("categorias.$category.enabled", true)) {
                    continue;
                }
                
                // Obtener configuración de la partícula
                $particleConfig = $config->getNested("particles_$category.$particleKey");
                
                if ($particleConfig === null || !($particleConfig['enabled'] ?? true)) {
                    continue;
                }
                
                // Spawn según categoría
                switch ($category) {
                    case 'feet':
                        $this->feetParticles->spawn($player, $particleConfig);
                        break;
                        
                    case 'body':
                        $this->bodyParticles->spawn($player, $particleConfig);
                        break;
                        
                    case 'head':
                        $this->headParticles->spawn($player, $particleConfig);
                        break;
                }
            }
        }
    }
}

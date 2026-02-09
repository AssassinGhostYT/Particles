<?php

declare(strict_types=1);

namespace AssassinGhost\Particles;

use AssassinGhost\Particles\commands\ParticleCommand;
use AssassinGhost\Particles\particles\ParticleManager;
use AssassinGhost\Particles\tasks\ParticleTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {
   
   private static self $instance;
   private ParticleManager $particleManager;
   
   public function onLoad(): void {
       self::$instance = $this;
   }
   
   public function onEnable(): void {
       // Esto copia resources/config.yml a plugin_data/Particles/config.yml si no existe
       $this->saveDefaultConfig();
       
       // Recargar para asegurar que tenemos los datos más recientes
       $this->reloadConfig();
       
       $this->particleManager = new ParticleManager($this);
       
       // Debug: verificar que las partículas se cargaron
       $particlesFeet = $this->getConfig()->get("particles_feet", []);
       $this->getLogger()->info("Partículas de pie cargadas: " . count($particlesFeet));
       
       // Registrar comando
       $this->getServer()->getCommandMap()->register("particles", new ParticleCommand($this));
       
       // Iniciar task de partículas
       $this->getScheduler()->scheduleRepeatingTask(
           new ParticleTask($this), 
           $this->getConfig()->getNested("settings.intervalo_actualizacion", 5)
       );
       
       $this->getLogger()->info("§5Particles §ev1.0.0 §aActivado");
   }
   
   public function onDisable(): void {
       $this->particleManager->saveAllData();
       $this->getLogger()->info("§5Particles §cDeshabilitado");
   }
   
   public static function getInstance(): self {
       return self::$instance;
   }
   
   // Usar getConfig() en lugar de una propiedad separada
   public function getParticleConfig(): Config {
       return $this->getConfig();
   }
   
   public function getParticleManager(): ParticleManager {
       return $this->particleManager;
   }
}

<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\particles;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\BubbleParticle;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\EnchantParticle;
use pocketmine\world\particle\EnchantmentTableParticle;
use pocketmine\world\particle\ExplodeParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\HappyVillagerParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\particle\InkParticle;
use pocketmine\world\particle\InstantEnchantParticle;
use pocketmine\world\particle\ItemBreakParticle;
use pocketmine\world\particle\LavaDripParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\RainSplashParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\particle\SnowballPoofParticle;
use pocketmine\world\particle\SplashParticle;
use pocketmine\world\particle\SporeParticle;
use pocketmine\world\particle\TerrainParticle;
use pocketmine\world\particle\WaterDripParticle;
use pocketmine\world\particle\WaterParticle;
use pocketmine\world\particle\Particle;
use pocketmine\color\Color;

abstract class BaseParticle {
    
    protected string $type;
    protected int $count;
    
    public function __construct(string $type, int $count = 3) {
        $this->type = $type;
        $this->count = $count;
    }
    
    abstract public function spawn(Player $player, array $config): void;
    
    protected function spawnParticle(Player $player, Vector3 $pos, string $particleType): void {
        $particle = $this->getParticleInstance($particleType);
        if ($particle === null) return;
        
        $world = $player->getWorld();
        
        // Mostrar al jugador y a otros cercanos
        $viewers = [$player];
        
        foreach ($world->getPlayers() as $p) {
            if ($p !== $player && $p->getPosition()->distance($player->getPosition()) <= 20) {
                $viewers[] = $p;
            }
        }
        
        $world->addParticle($pos, $particle, $viewers);
    }
    
    protected function getParticleInstance(string $type): ?Particle {
        return match($type) {
            'FLAME' => new FlameParticle(),
            'WATER_SPLASH' => new SplashParticle(),
            'HEART' => new HeartParticle(),
            // CORREGIDO: DustParticle acepta Color, RedstoneParticle acepta int (lifetime)
            'NOTE' => new DustParticle(new Color(255, 0, 0)),
            'PORTAL' => new PortalParticle(),
            'ENCHANTMENT_TABLE' => new EnchantmentTableParticle(),
            'CRIT' => new CriticalParticle(),
            'SMOKE' => new SmokeParticle(),
            'LAVA' => new LavaParticle(),
            'SPELL_WITCH' => new DustParticle(new Color(128, 0, 128)),
            'VILLAGER_HAPPY' => new HappyVillagerParticle(),
            'VILLAGER_ANGRY' => new AngryVillagerParticle(),
            'END_ROD' => new DustParticle(new Color(255, 255, 255)),
            'SOUL_FIRE_FLAME' => new FlameParticle(),
            'SNOWBALL_POOF' => new SnowballPoofParticle(),
            'TOTEM' => new InstantEnchantParticle(),
            'DRAGON_BREATH' => new DustParticle(new Color(170, 0, 170)),
            'NAUTILUS' => new WaterParticle(),
            'HAPPY_VILLAGER' => new HappyVillagerParticle(),
            'FLASH' => new HugeExplodeParticle(),
            'BUBBLE' => new BubbleParticle(),
            'EXPLODE' => new ExplodeParticle(),
            'HUGE_EXPLODE' => new HugeExplodeParticle(),
            'HUGE_EXPLODE_SEED' => new HugeExplodeSeedParticle(),
            'INK' => new InkParticle(),
            'LAVA_DRIP' => new LavaDripParticle(),
            'RAIN_SPLASH' => new RainSplashParticle(),
            'WATER_DRIP' => new WaterDripParticle(),
            'WATER' => new WaterParticle(),
            'SPORE' => new SporeParticle(),
            'TERRAIN' => new TerrainParticle(),
            'ITEM_BREAK' => new ItemBreakParticle(),
            // CORREGIDO: RedstoneParticle usa int (lifetime), no Color
            'REDSTONE' => new RedstoneParticle(10),
            default => new FlameParticle()
        };
    }
}

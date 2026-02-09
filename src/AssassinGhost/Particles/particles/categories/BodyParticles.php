<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\particles\categories;

use AssassinGhost\Particles\particles\BaseParticle;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BodyParticles extends BaseParticle {
    
    public function spawn(Player $player, array $config): void {
        $particleType = $config['tipo'] ?? 'ENCHANTMENT_TABLE';
        $count = $config['cantidad'] ?? 5;
        $radius = $config['radio'] ?? 0.8;
        
        $pos = $player->getPosition();
        
        for ($i = 0; $i < $count; $i++) {
            $angle = (mt_rand() / mt_getrandmax()) * 2 * M_PI;
            $offsetX = cos($angle) * $radius;
            $offsetZ = sin($angle) * $radius;
            $offsetY = mt_rand(8, 15) / 10;
            
            $particlePos = new Vector3(
                $pos->getX() + $offsetX,
                $pos->getY() + $offsetY,
                $pos->getZ() + $offsetZ
            );
            
            $this->spawnParticle($player, $particlePos, $particleType);
        }
    }
}

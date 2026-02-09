<?php

declare(strict_types=1);

namespace AssassinGhost\Particles\particles\categories;

use AssassinGhost\Particles\particles\BaseParticle;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FeetParticles extends BaseParticle {
    
    public function spawn(Player $player, array $config): void {
        $particleType = $config['tipo'] ?? 'FLAME';
        $count = $config['cantidad'] ?? 3;
        $height = $config['altura'] ?? 0.1;
        
        $pos = $player->getPosition();
        
        for ($i = 0; $i < $count; $i++) {
            $offsetX = (mt_rand() / mt_getrandmax() - 0.5) * 0.8;
            $offsetZ = (mt_rand() / mt_getrandmax() - 0.5) * 0.8;
            
            $particlePos = new Vector3(
                $pos->getX() + $offsetX,
                $pos->getY() + $height,
                $pos->getZ() + $offsetZ
            );
            
            $this->spawnParticle($player, $particlePos, $particleType);
        }
    }
}

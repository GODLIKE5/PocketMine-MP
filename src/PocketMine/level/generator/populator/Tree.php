<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\generator\object\Tree as ObjectTree;
use pocketmine\level\Level;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\utils\Random;

class Tree extends Populator{
	private $level;
	private $randomAmount;
	private $baseAmount;

	public function setRandomAmount($amount){
		$this->randomAmount = $amount;
	}

	public function setBaseAmount($amount){
		$this->baseAmount = $amount;
	}

	public function populate(Level $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y === -1){
				continue;
			}
			if($random->nextFloat() > 0.75){
				$meta = Sapling::BIRCH;
			}else{
				$meta = Sapling::OAK;
			}
			ObjectTree::growTree($this->level, new Vector3($x, $y, $z), $random, $meta);
		}
	}

	private function getHighestWorkableBlock($x, $z){
		for($y = 128; $y > 0; --$y){
			$b = $this->level->getBlockRaw(new Vector3($x, $y, $z));
			if($b->getID() !== Block::DIRT and $b->getID() !== Block::GRASS){
				if(--$y <= 0){
					return -1;
				}
			}else{
				break;
			}
		}

		return ++$y;
	}
}
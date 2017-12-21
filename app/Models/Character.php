<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facade\DB;
use App\Models\Armor;
use App\Models\Skill;
use App\Models\Background;
use App\Models\CharacterClass;
use App\Models\Race;
use App\Models\Subrace;
use App\User;
use Illuminate\Database\Eloquent\Collection;


class Character extends Model
{
    protected $table = "characters";
	
	public function user()
	{
		return $this->belongsTo(User::class);
	}
	
	public function skills()
	{
		return $this->belongsToMany(Skill::class, 'character_skills', 'character_id', 'skill_id');
	}
	
	public function speed()
	{
		return $this->race->speed;
	}
	
	public function class()
	{
		return $this->belongsTo(CharacterClass::class);
	}
	
	public function background()
	{
		return $this->belongsTo(Background::class);
	}
	
	public function race()
	{
		return $this->belongsTo(Race::class);
	}
	
	public function subrace()
	{
		return $this->belongsTo(Subrace::class, 'subrace_id');
	}
	
	public function prof_bonus()
	{
		if($this->level > 0 && $this->level < 5)
		{
			return 2;
		}
		
		if($this->level > 4 && $this->level < 9)
		{
			return 3;
		}
		
		if($this->level > 8 && $this->level < 13)
		{
			return 4;
		}
		
		if($this->level > 12 && $this->level < 17)
		{
			return 5;
		}
		
		if($this->level > 16 && $this->level < 21)
		{
			return 6;
		}
	}
	
	public function getAbilityModifier($stat)
	{
		if($stat === 1)
		{
			return -5;
		}
		
		if($stat === 2 || $stat === 3 )
		{
			return -4;
		}
		
		if($stat === 4 || $stat === 5 )
		{
			return -3;
		}
		
		if($stat === 6 || $stat === 7 )
		{
			return -2;
		}
		
		if($stat === 8 || $stat === 9 )
		{
			return -1;
		}
		
		if($stat === 10 || $stat === 11 )
		{
			return 0;
		}
		
		if($stat === 12 || $stat === 13 )
		{
			return 1;
		}
		
		if($stat === 14 || $stat === 15 )
		{
			return 2;
		}
		
		if($stat === 16 || $stat === 17 )
		{
			return 3;
		}
		
		if($stat === 18 || $stat === 19 )
		{
			return 4;
		}
		
		if($stat === 20 || $stat === 21 )
		{
			return 5;
		}
		
		if($stat === 22 || $stat === 23 )
		{
			return 6;
		}
		
		if($stat === 24 || $stat === 25 )
		{
			return 7;
		}
		
		if($stat === 26 || $stat === 27 )
		{
			return 8;
		}
		
		if($stat === 28 || $stat === 29 )
		{
			return 9;
		}
		
		if($stat === 30 )
		{
			return 10;
		}
	}

	public function getArmorClass()
	{
		if($this->getWornEquipment()->count() > 0)
		{
			$equipment = $this->getWornEquipment();
			
			$dexBonus = $this->getAbilityModifier($this->dexterity);
			$shieldAC = $equipment->shield->ac ?: 0;
			$baseAC = 10;
			
			if($this->class->name === "Barbarian")
			{
				$baseAC = 10 + $this->getAbilityModifier($this->constitution) + $dexBonus;
			}
			
			if($equipment->armor)
			{	
				$armorAC = $equipment->armor->ac;
				if($equipment->armor->max_dex_allowed AND $dexBonus >= $equipment->armor->max_dex_allowed) //2
				{
					$dexAC = $equipment->armor->max_dex_allowed;
				}
				else
				{
					$dexAC = $dexBonus;
				}
				return $armorAC + $dexAC + $shieldAC;
			}
			return $baseAC + $dexAC + $shieldAC;
		}
		
		return 10;
		
	}
	
	public function inventory()
	{
		return $this->hasMany(\App\Models\Inventory::class);
	}
	
	public function getWornEquipment()
	{
		$equipment = new Collection;
		$equipment->armor = '';
		$equipment->shield = '';
		$equipment->melee_primary = '';
		$equipment->melee_offhand = '';
		$equipment->ranged_weapon = '';
		
		
		$armorIDs = $this->inventory()->where('armor_id', '<>', null)
									  ->where('equipped', 1)
									  ->pluck('armor_id');
		foreach($armorIDs AS $id)
		{
			$armor = Armor::find($id);
			if(!is_null($armor))
			{
				if($armor->armor_type !== 'shield')
				{
					$equipment->armor = $armor;
				}
				
				if($armor->armor_type === 'shield')
				{
					$equipment->shield = $armor;
				}
			}
		}
		
		$weaponIDs = $this->inventory()->where('weapon_id', '<>', null)
									   ->where('equipped', 1)
									   ->pluck('weapon_id');
		foreach($weaponIDs AS $id)
		{
			$weapon = Weapon::find($id);
			if(!is_null($weapon))
			{
				if($weapon->properties->contains('ranged'))
				{
					$equipment->ranged_weapon = $weapon;
				}
			}
		}
		
		return $equipment;
	}
	
	public function passive_perception()
	{
		if(!is_null($this->getSkill('Perception')))
		{
			return 10 + $this->getSkill('Perception');
		}
		return 10;
	}
	
	public function getSkill($skill)
	{
		if($this->skills->count() > 0)
		{
			return $this->skills()->where('name', $skill)->first()->bonus;
		}
	}
}
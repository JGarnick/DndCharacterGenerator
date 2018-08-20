<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassProficiency;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class CharacterClass extends Model
{
    protected $table = "classes";
	
	public function general_info()
	{
		//return $this->hasMany(ClassProficiency::class, "class_id");
		$data = [];
		$data["hit_die"] = $this->hit_die;
		$data["skills_granted"] = DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "num_skills_granted")->pluck("num_skills_granted")->first();
		//Get weapon category proficiency, i.e. simple, martial, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "weapon")->whereNotNull('weapon_armor_type_id')->pluck("weapon_armor_type_id") AS $p){								
			$data["proficiencies"]["weapon_types"][] = $p;
		}
		//Get actual weapon proficiency, i.e. longsword, rapier, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "weapon")->whereNotNull('weapon_id')->pluck("weapon_id") AS $p){								
			$data["proficiencies"]["weapons"][] = $p;
		}
		//Get armor category proficiency, i.e. light, medium, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "armor")->whereNotNull('weapon_armor_type_id')->pluck("weapon_armor_type_id") AS $p){								
			$data["proficiencies"]["armor_types"][] = $p;
		}
		//Get actual armor proficiency, i.e. leather, breastplate, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "armor")->whereNotNull('armor_id')->pluck("armor_id") AS $p){								
			$data["proficiencies"]["armor"][] = $p;
		}
		//Get saving throw proficiency, i.e. Strength, Dexterity, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "saving throw")->whereNotNull('attribute_id')->pluck("attribute_id") AS $p){								
			$data["proficiencies"]["saves"][] = $p;
		}
		//Get skill proficiency, i.e. Acrobatics, Perception, etc.
		foreach(DB::table("class_proficiencies")->where("class_id", $this->id)->where("type", "skill")->whereNotNull('skill_id')->pluck("skill_id") AS $p){								
			$data["proficiencies"]["skills"][] = $p;
		}
		
		return $data;
	}
	
	public function saving_throws()
	{
		$attIDs = $this->proficiencies->where('type', 'saving throw')->pluck('attribute_id');
		$attArray = Attribute::whereIn('id', $attIDs)->pluck("name");
		return $attArray;
	}
}

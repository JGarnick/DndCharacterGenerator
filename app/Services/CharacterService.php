<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use \App\Models\Character;
use \App\Models\CharacterClass;
use \App\Models\Race;
use \App\Models\Skill;
use \App\Models\Subrace;
use \App\Models\Background;
use \App\Models\Proficiency;
use \App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterService
{
    // public function show($id)
	// {
	// 	$character 		= Character::find($id);
	// 	$races 			= Race::all();
	// 	$skills 		= Skill::all();
	// 	$classes 		= CharacterClass::all();
	// 	$backgrounds 	= Background::all();
	// 	$subraces 		= Subrace::all();

	// 	return [
	// 		"character" 	=> $character,
	// 		"races"			=> $races,
	// 		"classes"		=> $classes,
	// 		"backgrounds"	=> $backgrounds,
	// 		"subraces"		=> $subraces,
	// 		"skills"		=> $skills,
	// 	];
	// }

	public function create()
	{
		$races = $this->getRacesWithRacialBonuses();
		array_unshift($races, "deleteme");
		unset($races[0]);
		
		$attributes		= Attribute::all()->toArray();
		array_unshift($attributes, "deleteme");
		unset($attributes[0]);

		$skills			= Skill::all()->toArray();
		array_unshift($skills, "deleteme");
		unset($skills[0]);

		$classes 		= CharacterClass::all()->toArray();
		array_unshift($classes, "deleteme");
		unset($classes[0]);

		$backgrounds 	= Background::all()->toArray();
		array_unshift($backgrounds, "deleteme");
		unset($backgrounds[0]);

		$subraces 		= $this->getSubRacesWithRacialBonuses();
		array_unshift($subraces, "deleteme");
		unset($subraces[0]);

		$classData 		= $this->constructClassesInfo();

		return [
			"races"			=> $races,
			"classes"		=> $classes,
			"backgrounds"	=> $backgrounds,
			"subraces"		=> $subraces,
			"class_data"	=> $classData,
			"skills"		=> $skills,
			"attributes"	=> $attributes
		];
	}

	public function update(Request $request, $id)
	{
		$character 					= Character::find($id);
		$character->class 			= $request->input('class', 0);
		$character->background 		= $request->input('background', 0);
		$character->strength 		= $request->input('strength', 10);
		$character->dexterity 		= $request->input('dexterity', 10);
		$character->constitution 	= $request->input('constitution', 10);
		$character->wisdom 			= $request->input('wisdom', 10);
		$character->intelligence 	= $request->input('intelligence', 10);
		$character->charisma 		= $request->input('charisma', 10);

		//attach bonuses to skills
		if($request->has('skills'))
		{
			foreach($request->skills AS $skill)
			{
				$character->updateExistingPivot($skill->id, [
					'bonus'		 => $skill->bonus,
					'proficient' => $skill->proficient,
				]);
			}
		}

	}

	public function constructRacesInfo()
	{
		$races = Race::all();
		$race_data = [];

		//Process the races
		foreach($races AS $race){
			$race_data[$race->name]["id"] 			= $race->id;
			$race_data[$race->name]["has_subraces"] = $race->subraces->count() > 0;

			//Process the subraces
			if($race->subraces->isNotEmpty()){
				foreach($race->subraces AS $subrace){
					$race_data[$race->name]["subraces"][$subrace->name] = [
						"id" => $subrace->id,
						"parent_race_id" => $subrace->parent_race_id
					];
					$asi_index = 1;
					 //Process the subrace ASI
					foreach($subrace->asi AS $att){
						$key = $att->name;
						if($att->name === "Choice")
						{
							$key = $att->name . "_" . $asi_index;
							$asi_index++;
						}

						$race_data[$race->name]["subraces"][$subrace->name]["subrace_asi"][$key] = [
							"att_id" => $att->id,
							"amount" => $att->pivot->amount,
						];
					}
				}
			}

			foreach($race->asi AS $att){
				$race_data[$race->name]["race_asi"][$att->name] = [
					"att_id" => $att->id,
					"amount" => $att->pivot->amount,
				];
			}
		}

		return $race_data;
	}

	public function getRacesWithRacialBonuses(){
		$races = Race::all();
		foreach($races AS $r){
			$r["bonuses"] = [];
			foreach($r->asi AS $bonus){
				
				$toAdd = [
					"type" => "race",
					"val" => $bonus->pivot->amount,
					"key" => strtolower(Attribute::find($bonus->pivot->attribute_id)->abbr),
					"source" => strtolower($r->name)
				];
				$r->addBonus($toAdd);
			}
		}
		
		return $races->toArray();
	}
	public function getSubRacesWithRacialBonuses(){
		$subraces = Subrace::all();
		foreach($subraces AS $r){
			$r["bonuses"] = [];
			foreach($r->asi AS $bonus){
				
				$toAdd = [
					"type" => "race",
					"val" => $bonus->pivot->amount,
					"key" => strtolower(Attribute::find($bonus->pivot->attribute_id)->abbr),
					"source" => strtolower($r->name)
				];
				$r->addBonus($toAdd);
			}
		}
		
		return $subraces->toArray();
	}
	
	public function constructClassesInfo(){
		$classes = CharacterClass::all();
		$class_data = [];
		
		foreach($classes AS $class){
			$class_data[$class->name] = $class->general_info();
		}
		
		return $class_data;
	}
	
	// public function createAbilityScores($character){
	// 	$returnMe 	= [];
	// 	$atts	 	= Attribute::all();

	// 	foreach($atts AS $att)
	// 	{
	// 		$base					= 8;
	// 		$mod 					= $character->getAbilityModifier($base);

	// 		$returnMe[$att->name] = [
	// 			"abbr"				=> strtoupper($att->abbr),
	// 			"full_name"			=> strtolower($att->name),
	// 			"amount"			=> $base,
	// 			"mod"				=> $mod,
	// 			"id"				=> $att->id,
	// 			"points_purchased"  => 0,
	// 		];
	// 	}
		
	// 	return $returnMe;
	// }
	
	// public function constructCharSkills($allSkills, $ability_scores){
	// 	$returnMe = [];
	// 	foreach($allSkills AS $skill){
	// 		$returnMe[$skill->name] = [
	// 			'name' => $skill["name"],
	// 			'attribute_abbr' => $ability_scores[$skill["attribute"]]["abbr"],
	// 			'attribute' => $skill["attribute"],
	// 			'proficient' => 0,
	// 			'expertise' => 0,
	// 			'bonus' => $ability_scores[$skill["attribute"]]["mod"],
	// 			'skill_id' => $skill["id"]
	// 		];
	// 	}
		
	// 	return $returnMe;
	// }
	
	// public function constructSavingThrows(){
	// 	$returnMe = [];
		
	// }
	
}

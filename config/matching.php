<?php
// Matching configuration: weights and simple synonyms dictionary
return [
  'weights' => [
    'species_dog_for_active' => 30,
    'description_energetic'  => 20,
    'space_large_dog'        => 15,
    'space_small_pet'        => 15,
    'time_playful'           => 15,
    'time_independent'       => 15,
    'experience_young'       => 10,
    'experience_old'         => 10,
    'temperament_match'      => 10,
    'friendly_keyword'       => 5,
  ],

  // Simple synonyms / keywords for description matching
  'keywords' => [
    'energetic' => ['energic', 'activ', 'viu', 'vioi'],
    'playful' => ['jucăuș', 'jucaus', 'jucarie', 'jucausi', 'jucării'],
    'independent' => ['independent', 'autonom'],
    'friendly' => ['prieteno', 'afectuos', 'prietenos', 'dornic de afectiune']
  ]
];

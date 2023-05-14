<?php
$json_data = file_get_contents('data.json');
$data = json_decode($json_data);

$todayEnergy = $data->today->energy;
$todayGPL = $data->today->gpl;
$todayPetrol = $data->today->petrolium;

$trend = array();

foreach ($data->trend as $andamento) {
  $priceEnergy = $andamento->priceEnergy;
  $priceGPL = $andamento->priceGPL;
  $pricePetrol = $andamento->pricePetrolium;
  $month = $andamento->month;
  $trend[] = array('month' => $month, 'priceEnergy' => $priceEnergy, 'priceGPL' => $priceGPL, 'pricePetrolium' => $pricePetrol);
}

$startData = array(
  'today' => array(
    'energy' => $todayEnergy,
    'gpl' => $todayGPL,
    'petrolium' => $todayPetrol
  ),
  'trend' => $trend
);

$startData = json_encode($startData);

function getStartData()
{
  global $startData;
  return $startData;
}
?>
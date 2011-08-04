<?php
require_once('lib/roll.php');

$cash = 2000;
$start_bet = 10;
$multiplier = 2;
$spins = 10;
$bet = 'red';

$roll = new roll();
for ($i = 0; $i < $spins; $i++) {
  $curr_bet = $start_bet;
  $cash -= $bet;

  $result = $roll->roll();

  if ($result['color'] == $bet) {
    $cash += $curr_bet*2;
    $curr_bet = $start_bet;
  } else {
    $curr_bet = $curr_bet*$multiplier;
  }
}

var_dump($cash);

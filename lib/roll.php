<?php
class roll
{
  public function roll()
  {
    $wheel = $this->getAmericanWheel();
    return $wheel[array_rand($wheel)];
  }

  private function getAmericanWheel()
  {
    $wheel = array();
    $wheel[] = array('number' => '0', 'color' => 'green');
    $wheel[] = array('number' => '00', 'color' => 'green');
    $wheel[] = array('number' => '1', 'color' => 'red');
    $wheel[] = array('number' => '2', 'color' => 'black');
    $wheel[] = array('number' => '3', 'color' => 'red');
    $wheel[] = array('number' => '4', 'color' => 'black');
    $wheel[] = array('number' => '5', 'color' => 'red');
    $wheel[] = array('number' => '6', 'color' => 'black');
    $wheel[] = array('number' => '7', 'color' => 'red');
    $wheel[] = array('number' => '8', 'color' => 'black');
    $wheel[] = array('number' => '9', 'color' => 'red');
    $wheel[] = array('number' => '10', 'color' => 'black');
    $wheel[] = array('number' => '11', 'color' => 'black');
    $wheel[] = array('number' => '12', 'color' => 'red');
    $wheel[] = array('number' => '13', 'color' => 'black');
    $wheel[] = array('number' => '14', 'color' => 'red');
    $wheel[] = array('number' => '15', 'color' => 'black');
    $wheel[] = array('number' => '16', 'color' => 'red');
    $wheel[] = array('number' => '17', 'color' => 'black');
    $wheel[] = array('number' => '18', 'color' => 'red');
    $wheel[] = array('number' => '19', 'color' => 'red');
    $wheel[] = array('number' => '20', 'color' => 'black');
    $wheel[] = array('number' => '21', 'color' => 'red');
    $wheel[] = array('number' => '22', 'color' => 'black');
    $wheel[] = array('number' => '23', 'color' => 'red');
    $wheel[] = array('number' => '24', 'color' => 'black');
    $wheel[] = array('number' => '25', 'color' => 'red');
    $wheel[] = array('number' => '26', 'color' => 'black');
    $wheel[] = array('number' => '27', 'color' => 'red');
    $wheel[] = array('number' => '28', 'color' => 'black');
    $wheel[] = array('number' => '29', 'color' => 'black');
    $wheel[] = array('number' => '30', 'color' => 'red');
    $wheel[] = array('number' => '31', 'color' => 'black');
    $wheel[] = array('number' => '32', 'color' => 'red');
    $wheel[] = array('number' => '33', 'color' => 'black');
    $wheel[] = array('number' => '34', 'color' => 'red');
    $wheel[] = array('number' => '35', 'color' => 'black');
    $wheel[] = array('number' => '36', 'color' => 'red');

    return $wheel;
  }
}

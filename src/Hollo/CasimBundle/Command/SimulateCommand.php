<?php

namespace Hollo\CasimBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SimulateCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('casim:simulate')
      ->setDescription('Simulate Roulette');
      #->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
      #->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $cash = 2000;
    $start_bet = 10;
    $multiplier = 2;
    $spins = 10;
    $bet = 'red';

    $wheel = new \Hollo\CasimBundle\Entity\Wheel();

    for ($i = 0; $i < $spins; $i++) {
      $curr_bet = $start_bet;
      $cash -= $bet;

      $result = $wheel->getSpin();

      if ($result['color'] == $bet) {
        $cash += $curr_bet*2;
        $curr_bet = $start_bet;
      } else {
        $curr_bet = $curr_bet*$multiplier;
      }
    }

    var_dump($cash);

    $output->writeln('meh');
  }
}

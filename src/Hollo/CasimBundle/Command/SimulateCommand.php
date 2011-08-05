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
      ->setDescription('Simulate Roulette')
      ->addOption('spin', null, InputOption::VALUE_REQUIRED, 'How many spin',20000)
      ->addOption('cash', null, InputOption::VALUE_REQUIRED, 'How much cash to start with.',1000)
      ->addOption('start_bet', null, InputOption::VALUE_REQUIRED, 'How big a start bet.',25)
      ->addOption('max_bet', null, InputOption::VALUE_REQUIRED, 'How much is the biggest gamble.',50)
      ->addOption('max_sequence', null, InputOption::VALUE_REQUIRED, 'How many in sequence befor betting.',1)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->start_bet = $input->getOption('start_bet');
    $max_bet = $input->getOption('max_bet');
    $spin = $input->getOption('spin');
    $this->max_sequence = $input->getOption('max_sequence');
    $multiplier = 2;
    $this->cash = $input->getOption('cash');
    $stats = array(
      'max_cash' => 0,
      'min_cash' => $this->cash,
      'max_win' => 0
    );
    $win = 0;
    $this->history = array();
    $this->game_on = 0;

    $wheel = new \Hollo\CasimBundle\Entity\Wheel();
    $this->curr_bet = false;
    $this->bet = false;

    for ($i = 0; $i < $spin; $i++) {

      $output->writeln('');
      $output->writeln('<comment>Spin number: '.$i.'</comment>');
      $this->cash -= $this->curr_bet;

      $this->setBet($output);
      $result = $this->spin($output, $wheel);

      if ($this->bet == false) {
        $output->writeln('<comment>Skip turn.</comment>');
      } else {
        if ($result['color'] == $this->bet) {
          $win = $this->curr_bet*2;
          $output->writeln('<info>YOU WIN '.$win.'</info>');

          $this->cash += $win;
          $this->curr_bet = false;
          $this->game_on = 0;
        } else {
          $this->curr_bet = $this->curr_bet*$multiplier;
          $output->writeln('<comment>Did not win, raising bet to '.$this->curr_bet.' on '.$this->bet.'</comment>');
        }

        if ($this->curr_bet > $max_bet) {
          $output->writeln('<error>Bet too high, resetting...</error>');
          $this->curr_bet = $this->start_bet;
        }

        if ($this->cash > $stats['max_cash']) $stats['max_cash'] = $this->cash;
        if ($this->cash < $stats['min_cash']) $stats['min_cash'] = $this->cash;
        if ($win > $stats['max_win']) $stats['max_win'] = $win;

        if ($this->cash < 0) {
          $output->writeln('');
          $output->writeln('<error>You dont have enough cash :(</error>');
          break;
        }

        if ($this->curr_bet)
          $output->writeln('<comment>Bet: '.$this->curr_bet.' on '.$this->bet.'</comment>');
      }

      $output->writeln('<comment>Cash: '.$this->cash.'</comment>');
    }

    $output->writeln('');
    $output->writeln('<info>Spins: '.$i.'</info>');
    $output->writeln('<info>Highest win: '.$stats['max_win'].'</info>');
    $output->writeln('<info>Highest cash: '.$stats['max_cash'].'</info>');
    $output->writeln('<info>Lowest cash: '.$stats['min_cash'].'</info>');
    $output->writeln('<info>Current cash: '.$this->cash.'</info>');
    $output->writeln('');
    $output->writeln('<info>Time spent (40 seconds per spin): '.round(($i*40/60/60),2).' hours or '.round(($i*40/60/60/24),2).' days</info>');
    $profit = $this->cash - $input->getOption('cash');
    $profit_hour = $profit/($i*40/60/60);
    $output->writeln('<info>Profit: '.$profit.' ,- or '.round($profit_hour,2).'/h</info>');

  }

  private function setBet($output)
  {
    if ($this->game_on) return;

    $trigger = 0;

    if (count($this->history) < $this->max_sequence) {
      $output->writeln('<comment>Need history, not betting.</comment>');
      return;
    }

    foreach ($this->history as $hist) {
      if (!isset($color)) {
        $color = $hist['color'];
      } else {
        if ($color != $hist['color'])
          $trigger = 1;
      }
    }

    if ($trigger) {
      $output->writeln('<comment>Bad pattern in last rounds on roulette..</comment>');
      $this->bet = false;
      return;
    }

    $this->game_on = 1;
    if ($color == 'red') {
      $this->bet = 'black';
    } else {
      $this->bet = 'red';
    }

    $this->curr_bet = $this->start_bet;
    $output->writeln('<comment>Betting on '.$this->curr_bet.' on '.$this->bet.'</comment>');
  }

  private function addToHistory($result)
  {
    if (count($this->history) >= $this->max_sequence) {
      array_shift($this->history);
    }

    $this->history[] = $result;
  }

  private function spin($output, $wheel)
  {
    $result = $wheel->getSpin();
    $this->addToHistory($result);
    $output->writeln('<comment>Roulette: '.$result['color'].'/'.$result['number'].'</comment>');

    return $result;
  }
}

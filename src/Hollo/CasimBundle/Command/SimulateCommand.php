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
    $this->init($input);
    $this->dropBet();
    $wheel = new \Hollo\CasimBundle\Entity\Wheel();

    for ($this->spins = 0; $this->spins < $this->spin; $this->spins++) {

      $output->writeln('');
      $output->writeln('<comment>Spin number: '.$this->spins.'</comment>');

      $this->setBet($output);
      $result = $this->spin($output, $wheel);

      if ($this->bet == false) {
        $output->writeln('<comment>Skip turn.</comment>');
      } else {

        if ($result['color'] == $this->bet) {
          $this->winBet($output);
          $this->dropBet();

        } else {
          $this->raiseBet();
          $output->writeln('<comment>Did not win, raising bet to '.$this->curr_bet.' on '.$this->bet.'</comment>');
        }

        $this->validateBet($output);

        if ($this->cash < 0) {
          $output->writeln('');
          $output->writeln('<error>You dont have enough cash :(</error>');
          break;
        }
      }
      $this->writeSpinSummary($output);
    }

    $this->writeStats($input, $output);
  }

  private function init($input)
  {
    $this->start_bet = $input->getOption('start_bet');
    $this->max_bet = $input->getOption('max_bet');
    $this->spin = $input->getOption('spin');
    $this->max_sequence = $input->getOption('max_sequence');
    $this->cash = $input->getOption('cash');
    $this->multiplier = 2;
    $this->stats = array(
      'max_cash' => 0,
      'min_cash' => $this->cash,
      'max_win' => 0,
      'max_bet' => 0
    );
    $this->win = 0;
    $this->history = array();
  }

  private function setBet($output)
  {
    if (!$this->game_on) {
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
    }
    $output->writeln('<comment>Betting '.$this->curr_bet.' on '.$this->bet.'</comment>');
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
    $output->writeln('<comment>Cash is '.$this->cash.' we take '.$this->curr_bet.' for next spin, new cash is '.($this->cash-$this->curr_bet).'</comment>');
    $this->cash -= $this->curr_bet;

    if ($this->cash < $this->stats['min_cash']) $this->stats['min_cash'] = $this->cash;

    $result = $wheel->getSpin();
    $this->addToHistory($result);
    $output->writeln('<comment>Roulette: '.$result['color'].'/'.$result['number'].'</comment>');

    return $result;
  }

  private function dropBet()
  {
    $this->curr_bet = false;
    $this->bet = false;
    $this->game_on = 0;
  }

  private function winBet($output)
  {
    $this->cash += $this->curr_bet*2;
    if ($this->curr_bet*2 > $this->stats['max_win']) $this->stats['max_win'] = $this->curr_bet*2;
    if ($this->cash > $this->stats['max_cash']) $this->stats['max_cash'] = $this->cash;

    $output->writeln('<info>YOU WIN '.($this->curr_bet*2).'</info>');
  }

  private function setStats()
  {
    if ($this->curr_bet > $this->stats['max_bet']) $this->stats['max_bet'] = $this->curr_bet;
  }

  private function writeStats($input, $output)
  {
    $output->writeln('');
    $output->writeln('<info>Spins: '.$this->spins.'</info>');
    $output->writeln('<info>Highest win: '.$this->stats['max_win'].'</info>');
    $output->writeln('<info>Highest bet: '.$this->stats['max_bet'].'</info>');
    $output->writeln('<info>Highest cash: '.$this->stats['max_cash'].'</info>');
    $output->writeln('<info>Lowest cash: '.$this->stats['min_cash'].'</info>');
    $output->writeln('<info>Current cash: '.$this->cash.'</info>');
    $output->writeln('');
    $output->writeln('<info>Time spent (40 seconds per spin): '.round(($this->spins*40/60/60),2).' hours or '.round(($this->spins*40/60/60/24),2).' days</info>');
    $profit = $this->cash - $input->getOption('cash');
    $profit_hour = $profit/($this->spins*40/60/60);
    $output->writeln('<info>Profit: '.$profit.' ,- or '.round($profit_hour,2).'/h</info>');
  }

  private function raiseBet()
  {
    $this->curr_bet = $this->curr_bet*$this->multiplier;
  }

  private function validateBet($output)
  {
    if ($this->max_bet > 0 && $this->curr_bet > $this->max_bet) {
      $output->writeln('<error>Bet too high, resetting...</error>');
      $this->curr_bet = $this->start_bet;
    }

    $this->setStats();
  }

  private function writeSpinSummary($output)
  {
    $output->writeln('<comment>Cash: '.$this->cash.'</comment>');
  }
}

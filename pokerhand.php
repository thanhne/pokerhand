<?php
	/**
	 * Poker Hand
	 */
	class pokehand
	{
		public $cards;
		public $errors;
		public $output;
		public $counter;
		public $ranks;
		public $isOnePair, $isTwoPair, $isThreeCards, $isFourCards, $isFullHouse = false;
		public $suits_allow = ["S","H","D","C","s","h","d","c"];
		public $ranks_allow = ["2","3","4","5","6","7","8","9","10","J","Q","K","A","j","q","k","a"];

		function __construct($str){
			$this->ValidCardsFromString($str);
			if (!$this->errors) { //if not error 
				$this->Pair();
				$this->threeOrFourCards();
				$this->FullHouse();
			
				if ($this->isOnePair) {
					$this->output = '1P';
				}else if($this->isTwoPair) {
					$this->output = '2P';
				}else if($this->isThreeCards) {
					$this->output = '3C';
				}else if($this->isFourCards){
					$this->output = '4C';
				}else if($this->isFullHouse){
					$this->output = 'FH';
				}else if(count($this->cards) == 0){
					$this->output = '--';
				}
			}else {
				$this->output = $this->errors;
			}

			echo "Input: " . $str . "\nOutput: ".$this->output;
		}

		public function getSuitFromCard($card){
			return substr($card,0,1);
		}

		public function getRankFromCard($card){
			return substr($card, -1);
		}

		/**
		 * [ValidCardsFromString Validation 5 Card on your hand]
		 * @param [type] $str [input 10 chars have 5 cards example: D4C4C8D8S4]
		 */
		public function ValidCardsFromString($str){
			$cards = str_split($str,2);
			$total_card = count($cards);
			if($total_card != 5){
				$this->errors .= 'please check again input: Hold your hand 5 card <br />';
			}else {

				for ($i=0; $i < $total_card; $i++) { 
					$card = $cards[$i];
					try {
						$this->cardValid($card);
					} catch (Exception $e) {
						$this->errors .= 'Card '.($i+1).'('.$card.') is not valid: ' .$e->getMessage();
						$this->errors .= "<br />";
					}
					$this->cards[] = $card; //set cards array if valid
					$this->ranks[] = $this->getRankFromCard($card);
				}
			}
		}

		public function cardValid($card){
			if(empty($card)){
				throw new \Exception('Card is empty.');
			}

			$cardlen = strlen($card);
			if ($cardlen != 2) {
				throw new \Exception('This isn\'t a card');
			}

			$suit = $this->getSuitFromCard($card);
			if (!in_array($suit,$this->suits_allow)) {
				throw new \Exception('The charts that represent a Suit must be: S(=Spades♠), H(=Hearts♡), D(=Diamonds♢), C(=Clovers♣).');
			}

			$rank = $this->getRankFromCard($card);
			if (!in_array($rank,$this->ranks_allow)) {
				throw new \Exception('The strings that represent a Rank must be: 2, 3, 4, 5, 6, 7, 8, 9, 10, J, Q, K, A');
			}
			return true;
		}

		public function Pair(){
			$this->counter = [];
			foreach (array_count_values($this->ranks) as $value => $count) {
				if ($count == 2) { //if a pair must be 2 value the same in 5 card
					if (count($this->counter) < 2) 
						$this->counter[] = $value;
				}
			}

			if (count($this->counter) == 1)
				$this->isOnePair = true;

			if (count($this->counter) == 2) 
				$this->isTwoPair = true;
			
		}

		public function threeOrFourCards (){
			foreach (array_count_values($this->ranks) as $value => $count) {
				if ($count == 3) {
					$this->isThreeCards = true;
				}else if($count == 4){
					$this->isFourCards = true;
				}
			}
		}

		public function FullHouse(){
			if (isset($this->isOnePair) && isset($this->isThreeCards)) {
				$this->isFullHouse = true;
			}
		}
	}

	echo "Please inter card string (example: H2S2C2D2HJ, H2S2C3D3HQ, H3S3C3D2HA): ";
	$handle = fopen ("php://stdin","r");
	$cards = fgets($handle);
	if($cards){
	    new pokehand(trim($cards));
	    exit;
	}

//random
/*	$arr = [
		"S1H1S4CJS4", //error
		"S3H3S3CJS4", //three cards
		"S2H2S4C4S4", //full house
		"", //no input string
		"S1H1S4CJ", //not enough 5 cards
		'H2S2C2D2HJ', //four cards
		'H2S2C3D3HQ', //two pair
		'H3S3C3D2HA' //three cards
	];

	$key = array_rand($arr);
	$input = $arr[$key];
	new pokehand($input);*/
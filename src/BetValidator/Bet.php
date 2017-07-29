<?php
namespace BetValidator;

class Bet
{
    public $success = true;
    private $error_codes;
    public $bestslip;

    /**
     * Bet constructor.
     * @param $betslip
     */
    public function __construct($betslip)
    {
        $this->bestslip = $betslip;
        $this->setErrorCodes();
        $this->validator();
    }

    /**
     * Sets $error_codes variable.
     */
    private function setErrorCodes()
    {
        $this->error_codes = [
            0 => 'Unknown error',
            1 => 'Betslip structure mismatch',
            2 => 'Minimum stake amount is :min_amount',
            3 => 'Maximum stake amount is :max_amount',
            4 => 'Minimum number of selections is :min_selections',
            5 => 'Maximum number of selections is :max_selections',
            6 => 'Minimum odds are :min_odds',
            7 => 'Maximum odds are :max_odds',
            8 => 'Duplicate IDs are not allowed',
            9 => 'Maximum win amount is :max_win_amount',
            10 => 'Your previous action is not finished yet',
            11 => 'Insufficient balance',
        ];
    }

    /**
     * Validates $bestslip variable.
     */
    private function validator()
    {
        $structure = true;
        $betIds = [];
        $allOds = [];

        // Checking if array parameters is correct type.
        if (!is_int($this->bestslip['player_id']) || !is_float($this->bestslip['stake_amount']) || !is_array($this->bestslip['errors'])) {
            $structure = false;
        }

        if (isset($this->bestslip['selections']) && $structure === true) {

            foreach ($this->bestslip['selections'] as $index => $section) {

                if (!is_int($section['id'])) {
                    $structure = false;
                } else {
                    array_push($betIds, $section['id']);
                }

                // Validate odds: max/max. If passes add to array.
                if (!is_float($section['odds'])) {
                    $structure = false;
                } elseif (1 > $section['odds']) {
                    array_push($this->bestslip['selections'][$index]['errors'], $this->error_codes[6]);
                } elseif (10000 < $section['odds']) {
                    array_push($this->bestslip['selections'][$index]['errors'], $this->error_codes[7]);
                } else {
                    array_push($allOds, $section['odds']);
                }

                if (!empty($this->bestslip['selections'][$index]['errors'])) {
                    $this->success = false;
                }
            }
        }

        // Counting expected win amount and validating it.
        $expectedWinAmount = array_product($allOds) * $this->bestslip['stake_amount'];

        if ($expectedWinAmount > 20000) {
            array_push($this->bestslip['errors'], $this->error_codes[9]);
        }

        // Validating stake_amount: max/min.
        if (0.3 > $this->bestslip['stake_amount']) {
            array_push($this->bestslip['errors'], $this->error_codes[2]);
        } elseif (10000 < $this->bestslip['stake_amount']) {
            array_push($this->bestslip['errors'], $this->error_codes[3]);
        }

        // Validating sections: max/min
        if (1 > count($this->bestslip['selections'])) {
            array_push($this->bestslip['errors'], $this->error_codes[4]);
        } elseif (20 < count($this->bestslip['selections'])) {
            array_push($this->bestslip['errors'], $this->error_codes[5]);
        }

        if ($structure === false) {
            array_push($this->bestslip['errors'], $this->error_codes[1]);
        }

        if (count($betIds) !== count(array_unique($betIds))) {
            array_push($this->bestslip['errors'], $this->error_codes[8]);
        }

        if (!empty($this->bestslip['errors'])) {
            $this->success = false;
        }
    }

    /**
     * Returns global errors.
     * @return mixed
     */
    public function getGlobalErrors() {
        return $this->bestslip['errors'];
    }

    /**
     * Gets secstions
     * @return array
     */
    public function getSelectionsErrors() {
        $errorSelections = [];

        // Checking for sections with errors and adding to return array.
        foreach ($this->bestslip['selections'] as $selection) {
            if (!empty($selection['errors'])) {
                array_push($errorSelections, $selection);
            }
        }
        return $errorSelections;
    }
}
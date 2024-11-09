<?php

if (!function_exists('number2roman')) {

  /**
   * @param int $number
   * @return string
   */
  function number2roman(int $numb = 0): string
  {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnVal = '';

    while ($numb > 0) {
      foreach ($map as $roman => $int) {
        if ($numb >= $int) {
          $numb -= $int;
          $returnVal .= $roman;
          break;
        }
      }
    }
    return $returnVal;
  }
}



if (!function_exists('displayAlert')) {
  function displayAlert()
  {
    if (Session::has('message')) {
      list($type, $message) = explode('|', Session::get('message'));

      $type = $type == 'error' ?: 'danger';
      $type = $type == 'message' ?: 'info';

      return sprintf('<div class="alert alert-%s">%s</div>', $type, $message);
    }

    return '';
  }
}

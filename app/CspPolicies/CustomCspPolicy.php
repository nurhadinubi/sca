<?php

namespace App\CspPolicies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Policy;

class CustomCspPolicy extends Policy
{
  public function configure()
  {
    $this
      ->addDirective(Directive::IMG, [Keyword::SELF, 'data:'])
      ->addDirective(Directive::STYLE, [
        'self',
        'https://fonts.googleapis.com',
      ])
      ->addDirective(Directive::FONT, [
        'self',
        'https://fonts.gstatic.com',
      ]);
  }
}

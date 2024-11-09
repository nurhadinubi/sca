<?php

return [

    /*
     * A policy will determine which CSP headers will be set. A valid CSP policy is
     * any class that extends `Spatie\Csp\Policies\Policy`
     */
    'policy' => App\CspPolicies\CustomCspPolicy::class,
    // 'policy' => Spatie\Csp\Policies\Basic::class,

    /*
     * This policy which will be put in report only mode. This is great for testing out
     * a new policy or changes to existing csp policy without breaking anything.
     */
    'report_only_policy' => '',

    /*
     * All violations against the policy will be reported to this url.
     * A great service you could use for this is https://report-uri.com/
     *
     * You can override this setting by calling `reportTo` on your policy.
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => Spatie\Csp\Nonce\RandomString::class,

    'default-src' => ['self'],
    'style-src' => ['self', 'https://fonts.googleapis.com', 'https://cdnjs.cloudflare.com', 'https://maxcdn.bootstrapcdn.com'],
    'font-src' => ['self', 'https://fonts.gstatic.com', 'https://cdnjs.cloudflare.com', 'https://maxcdn.bootstrapcdn.com'],
    'script-src' => ['self', 'https://cdnjs.cloudflare.com'],
    'img-src' => ['self', 'data:', 'https://cdn.jsdelivr.net'],
];

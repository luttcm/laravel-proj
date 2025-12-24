<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it will be used to sign
    | your tokens. A helper command is provided for this:
    | `php artisan jwt:secret`
    |
    | Note: This will be used for Symmetric algorithms only (HS256, HS384 and HS512).
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour.
    |
    | You can also set this to null, to yield a never expiring token.
    | Some people may want this behaviour for e.g. a mobile app.
    | This is not recommended, for security reasons, so make
    | sure to have an endpoint in place to refresh the token.
    |
    */

    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.E. The user can refresh their token within a 2 week window of
    | the original token being created until they are required to log in again.
    | Defaults to 2 weeks.
    |
    | You can also set this to null, to yield an infinite refresh time.
    | Some may want this instead of never expiring tokens for e.g. a mobile app.
    | This is not recommended, for security reasons, so make sure to have an
    | endpoint in place to refresh the token.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Set the algorithm used to sign the tokens. See here
    | https://github.com/tymondesigns/jwt-auth/wiki/Configuration#hash-algorithm
    |
    */

    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify the required claims that must exist in any token.
    | A TokenInvalidException will be thrown if any claims are missing.
    |
    */

    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'jti',
        'sub',
        'prv',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    |
    | Specify the claim keys to be persisted when refreshing a token.
    | `note` and `sub` are wildcard persistence claims.
    | By default all instances of `note` and `sub` will remain after refresh.
    | Specify other claims for them to persist, otherwise they're discarded.
    |
    */

    'persistent_claims' => [
        // 'note',
        // 'sub',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    |
    | This will determine whether a `prv` claim is automatically added to
    | the token. The purpose of this is to ensure that the token is only
    | usable by the subject that it was requested by.
    | See here to learn more: https://tools.ietf.org/html/rfc7519#section-4.1.2
    |
    */

    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway in seconds
    |--------------------------------------------------------------------------
    |
    | This property gives the jwt timestamp claims some leeway when validating
    | tokens to account for clock skew.
    | Defaults to 0 seconds.
    |
    */

    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Loggable Claims
    |--------------------------------------------------------------------------
    |
    | Set the claim keys to be loggable. This will result in these claims
    | being logged when a token is failed to be decoded.
    |
    */

    'loggable_claims' => [
        // 'exp',
        // 'nbf',
        // 'iat',
        // 'jti',
    ],
];

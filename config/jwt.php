<?php

return [
    'algorithm'        => env('JWT_ALGORITHM', 'RS256'),
    'private_key_path' => base_path(env('JWT_PRIVATE_KEY_PATH', 'storage/keys/jwt_private.pem')),
    'public_key_path'  => base_path(env('JWT_PUBLIC_KEY_PATH', 'storage/keys/jwt_public.pem')),
    'expiry_seconds'   => (int) env('JWT_EXPIRY_SECONDS', 1200),
    'issuer'           => env('JWT_ISSUER', 'policy-auth'),
    'audience'         => env('JWT_AUDIENCE', 'policy-data'),
];
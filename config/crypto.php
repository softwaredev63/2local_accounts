<?php

return [

  'simplex' => [
      'publicKey' => env('SIMPLEX_PUBLIC_KEY', 'pk_test_0c3e2ecd-1546-4068-ae01-d49382e1266a'),
      'scriptSrc' => env('SIMPLEX_SCRIPT_SRC', 'https://cdn.test-simplexcc.com/sdk/v1/js/sdk.js'),
      'formScriptSrc' => env('SIMPLEX_FORM_SCRIPT_SRC', 'https://iframe.sandbox.test-simplexcc.com/form-sdk.js')
  ]

];

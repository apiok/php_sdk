# Odnoklassniki PHP SDK

## Example:

Include SDK file and create new SDK object with config:

```
require_once('okSdk.php');

$sdk_config = array(
  'app_id'         => 'YOUR_APP_ID',
  'app_public_key' => 'YOUR_PUBCIC_APP_KEY',
  'app_secret_key' => 'YOUR_SECRET_APP_KEY',
  'redirect_url'   => 'YOUR_APP_REDIRECT_URL',
);

$ok_sdk = new OkSDK($sdk_config);
```

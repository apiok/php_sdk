# Odnoklassniki PHP SDK

## Example:

Redirect user on URL 
https://connect.ok.ru/oauth/authorize?client_id={clientId}&scope={scope}&response_type=code&redirect_uri={redirectUri}

In callback your redirectUri, include SDK file and create new SDK object with config:

```
require_once('okSdk.php');

$sdk_config = array(
  'app_id'         => 'YOUR_APP_ID',
  'app_public_key' => 'YOUR_PUBCIC_APP_KEY',
  'app_secret_key' => 'YOUR_SECRET_APP_KEY',
  'redirect_url'   => 'YOUR_APP_REDIRECT_URL',
);

$ok_sdk = new OkSDK($sdk_config);

if ($ok_sdk->getCode()){

    if($ok_sdk->changeCodeToToken($ok_sdk->getCode())){
    
        // your code...
        
    }
}
```

Example request:

```
$group = $ok_sdk->makeRequest('group.getInfo', array('uids' => YOUR_GROUP_ID, 'fields' => 'uid,name'));
```

Russian example: [https://apiok.ru/wiki/pages/viewpage.action?pageId=80052390](https://apiok.ru/wiki/pages/viewpage.action?pageId=80052390)

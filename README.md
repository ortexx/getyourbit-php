# [GetYourBit.com](https://getyourbit.com) client 

This library allows you to make requests easily.

## Examples

```php
require 'vendor/autoload.php';    

use GetYourBit\Api;

// create an instance
$api = new Api('https://ip.getyourbit.com/');

// login
$api->auth('login', 'password');

// request without scrolling
$body = $api->request("/lookup/8.8.8.8", array('locale': 'en-US'));
var_dump($body['data']);

// request with scrolling
$result = $api->scroll("/find/", array(
    'size': 500,
    'query' => array(
        'country' => 'nepal'
    )
));
echo count($result);

//logout
$api->logout();
```

## Api
### .auth($login, $password, options=array())
Login to the API. You can get __$login__ and __$password__ [on the site](https://getyourbit.com) after a subscription.  
__options__ is [requests](https://github.com/rmccue/Requests) module options. 
You can also pass __headers__ as option  
Free services don't require authorization.

### .logout()
Logout from the API. It gives an error without authorization before.

### .request($url, $data=array(), options=array())
Request to the API without scrolling to get data.     
It returns all response body as object.

### .scroll($url, $data=array(), $options=array(), $callback=null)
### .scroll($url, $data=array(), $callback=null)
### .scroll($url, $callback=null)
Request to the API with scrolling to get data. You can pass callback to control every chunk. You will get three arguments:

* __$body__ - chunk response body
* __$chunkData__ - chunk data
* __$fullData__ - full data by the current chunk  

It returns the full data at the end


 

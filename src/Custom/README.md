Put your custom functionality here...

Use `Custom` namespace to add new payment gateways, API, etc.

For example:

`./src/Custom/Payment/MyCustomGateway.php`:

```php
namespace Custom\Payment;

use Mirspay\Payment\Common\AbstractGateway;

final class MyCustomGateway extends AbstractGateway 
{
// implementation of your custom payment gateway
}
```

Don't forget to tag your custom payment gateway with `app.payment.gateway`.

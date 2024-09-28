_The project still in progress..._

## Payment Proxy
Payment Proxy service provides an easy api for order payments
via various gateways. Please, take a look at the picture below to get an idea how it works:

![Service scheme](./scheme.png)

## Implemented payment gateways
* [LiqPay](https://www.liqpay.ua/doc)
* _TBD: PrivatBank_
* _TBD: Monobank_

## Requirements
* PHP 8.3
* MySQL

## Install

### Docker
Close the repository:
```shell
git clone https://github.com/skoro/mirspay.git
```

Build and up the image:
```shell
docker compose build
docker compose run --rm app composer install
docker compose up -d
docker compose exec app php bin/console doctrine:migrations:migrate
```

### From source code

Assume PHP, [Symfony CLI](https://symfony.com/download) and MySQL are locally installed.
1. Clone the repository:
    ```shell
    git clone https://github.com/skoro/mirspay.git
    ```
2. Install the project dependencies:
    ```shell
    composer install
    ```
3. Create `.env.local` file and fill in the database dsn:
    ```dotenv
    DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
   ```
4. Apply migrations:
    ```shell
    symfony console doctrine:migrations:migrate
    ```
5. Start the server.
    ```shell
    symfony serve -d
    ```
   
### Payment Gateways configuration

1. LiqPay public and private keys:
   ```dotenv
   LIQPAY_PUBLIC_KEY=
   LIQPAY_PRIVATE_KEY=
   ```
   
## API documentation
Two end-points are available for getting the API documentation:
 - `/api/doc` swagger ui. 
 - `/api/doc.json`
    as above but in Json format
    for consuming by [Postman](https://www.postman.com/product/what-is-postman/), for example.

## Order status notifications
A subscription allows you to get notifications when the order status has been changed.

### Channels and messages
A notification sends via channel. By default, `http` channel is available.
`http` does a POST/PUT/PATCH request to a predefined url with  json payload.

A message is used to represent data in the channel. There is `simple` message type, which
will turn an order and payment gateway response into a plain array.

To get a list of available channels and messages, use `subscribe:channels` console command.

If you want to develop your own channel and/or message, add tag `app.subscriber.channel`
for your custom channel and `app.subscriber.message` for a custom message (take a look
at `services.yml`).

### Subscribe example
This is how to subscribe your external service when order status is changed to `payment_received`.
```shell
console subscriber:add-http --order-status payment_received --channel-message simple https://backend.my-service.com/api/order-payment
```

When the order gets `payment_received` status due to payment gateway response,
a POST http request will be sent to https://backend.my-service.com/api/order-payment end point.
The request will contain json data like this:
```json
{
  "order_num": "1234567890",
  "order_status": "payment_received",
  "success": true,
  "transaction_id": "1234567890",
  "response": {
    // original response from the payment gateway.
  }
}
```

## How to debug payment gateway callbacks

In order to debug payment gateway callback responses, [expose](https://expose.dev/) tool can be used to create a tunnel
to your local development environment.
If you use docker, start `expose` with the following command:
```shell
expose share http://localhost
```

You will see external _Public HTTP_ and _Public HTTPS_ urls in output.
Use one of those urls and send Create Order request like this:
```
curl --location 'https://okdfskdfj126722jsnxz.sharedwithexpose.com/api/v1/order' \
--header 'Content-Type: application/json' \
--data '{
    "order_num": "00001",
    "payment_gateway": "liqpay",
    "description": "Order #001",
    "return_url": "https://super-site.com/thank-you",
    "products": [
        {
            "sku": "A01001",
            "price": 1900,
            "qty": 1,
            "name": "Beer"
        }
    ]
}'
```
Make sure `status_check` in the response has an external url,
that means your host is shared and the payment gateway will send a callback via the external url. 

If you still have localhost, check `trusted_proxies` settings in `config/packages/framework.yaml`.

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
_TBD: make docker installation_

1. Clone the repository:
    ```shell
    git clone https://github.com/skoro/mirspay.git
    ```
2. Install the project dependencies:
    ```shell
    composer install
    ```
3. Edit the `.env` file and fill in the necessary variables and payment gateway credentials:
    ```dotenv
    LIQPAY_PUBLIC_KEY=
    LIQPAY_PRIVATE_KEY=
    ```
4. Start the server.
    ```shell
    symfony serve -d
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
  "response": {
    // original response from the payment gateway.
  }
}
```

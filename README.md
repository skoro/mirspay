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
   

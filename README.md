# Anonymous Cucumber

School project - Senior Year (2022)

Simple web app to purchase cucumbers anonymously with Monero (XMR)

## Development
To start the app locally, run the following commands:
```bash
cd public
php -S 127.0.0.1:8000
```

## Database
The app uses a MySQL database to handle orders

It follows the format:
| Field         | Type        | Null | Key | Default | Comment                                |
|---------------|-------------|------|-----|---------|----------------------------------------|
| id            | varchar(6)  | NO   | PRI | NULL    | Random unique hex string               |
| address       | varchar(95) | YES  |     | NULL    | Monero address generated for order     |
| message       | text        | YES  |     | NULL    | Shipping instructions provided by user |
| time          | int         | YES  |     | NULL    | Order timetsamp                        |
| price         | varchar(16) | YES  |     | NULL    | Price in Monero (at the time of order) |
| address_index | int         | YES  |     | NULL    | Monero address index                   |
| completed     | tinyint(1)  | YES  |     | 0       | Whether the order has been completed   |

## config.php
The app stores secrets in a config file named `config.php` at the root level (outside of the public folder)

It should contain:
```php
<?php
    // Database
    $db_host="localhost";
    $db_user="<username>";
    $db_pass="<password>";
    $db_name="<database_name>";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Something went wrong!");
    }

    // Monero RPC
    $rpc_host = "127.0.0.1";
    $rpc_port = "38083";
?>
```

## Monero Wallet RPC
To start the Monero Wallet RPC (required to interact with the wallet), run the following command:
```shell
 ./monero-wallet-rpc --stagenet --rpc-bind-port 38083 --wallet-file wallets/main --password "<wallet_password>" --disable-rpc-login --daemon-address http://stagenet.melo.tools:38081 --trusted_daemon
```

## Screenshots
![homepage](https://user-images.githubusercontent.com/62302815/162515879-42b81a42-3fc3-458c-a805-50a5e5e321e7.png)
![form](https://user-images.githubusercontent.com/62302815/162515884-05f1b731-1c49-4cc8-a74e-ac8d92f18a0f.png)
![order](https://user-images.githubusercontent.com/62302815/162515886-8c4b733c-fe6e-4f1d-914d-d5eba8474837.png)
![order_complete](https://user-images.githubusercontent.com/62302815/162515888-3be9c0ea-3ac5-4a35-85aa-e16dfa634749.png)
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

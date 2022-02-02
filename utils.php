<?php
    include 'config.php';

    function fetchExchangeRate() {
        $url = "https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=XMR";
        $json = file_get_contents(__DIR__ . "/cached_exchange_rate.json");
        $data = json_decode($json, true);

        if (time() - $data["last_fetch_time"] > 3600) {
            $fetchData = file_get_contents($url);
            $json = json_decode($fetchData, true);
            $newExchangeRate = $json["XMR"];

            $data["usd_to_xmr"] = $newExchangeRate;
            $data["last_fetch_time"] = time();

            $json = json_encode($data);
            file_put_contents(__DIR__ . "/cached_exchange_rate.json", $json);

            return $data["usd_to_xmr"];
        }

        return $data["usd_to_xmr"];
    }

    function getRandomHex($num_bytes=3) {
        return bin2hex(openssl_random_pseudo_bytes($num_bytes));
    }

    function fetchOrder($id) {
        global $conn;

        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        return $order;
    }

    function createOrder($id, $address, $address_index, $message, $price) {
        global $conn;

        $sql = "INSERT INTO orders (id, address, address_index, message, price, time) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissi", $id, $address, $address_index, $message, $price, time());
        $stmt->execute();

        return [
            "id" => $id,
            "address" => $address,
            "address_index" => $address_index,
            "message" => $message,
            "price" => $price,
            "time" => time()
        ];
    }

    function completeOrder($id) {
        global $conn;

        $sql = "UPDATE orders SET completed = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
    }

    function getPublicPGPKey() {
        return file_get_contents(__DIR__ . "/public_pgp_key.asc");
    }

    function moneroRpcCall($method, $params="") {
        global $rpc_host, $rpc_port;
        $url = "http://" . $rpc_host . ":" . $rpc_port . "/json_rpc";

        $data = [
            "jsonrpc" => "2.0",
            "id" => "0",
            "method" => $method,
            "params" => $params
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => "POST",
                'content' => json_encode($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if (!$result) {
            die("Something went wrong!");
        }

        return json_decode($result, true);
    }

    function generateMoneroAddress() {
        $response = moneroRpcCall("create_address", ["account_index" => 0]);
        return $response["result"];
    }

    function fetchIncomingTransactions($index) {
        $response = moneroRpcCall("get_transfers", ["in" => true, "pending" => true, "subaddr_indices" => [$index]]);
        return $response["result"]["in"];
    }
?>
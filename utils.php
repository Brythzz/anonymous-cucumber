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

    function createOrder($id, $address, $message) {
        global $conn;

        $sql = "INSERT INTO orders (id, address, message, time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $id, $address, $message, time());
        $stmt->execute();

        return [
            "id" => $id,
            "address" => $address,
            "message" => $message,
            "time" => time()
        ];
    }

    function generateMoneroAddress() {
        return '84sVt19zqDyjN28bYhc4EajcVqFH5cJLAW9uQ7kasG944Tgq9og1R3gbpYfCea5zk9AGU35M4SQPmSM7Z983Jp3A1rLhrF4';
    }

    function getPublicPGPKey() {
        return file_get_contents(__DIR__ . "/public_pgp_key.asc");
    }
?>
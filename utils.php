<?php
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
?>
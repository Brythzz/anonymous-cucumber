<?php
    include '../utils.php';
    include '../qrcode.php';

    $urlId = basename($_SERVER['REQUEST_URI']);
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : getRandomHex();
    $orderId = $urlId != 'order.php' ? $urlId : $orderId;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anonymous Cucumber</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <main>
        <h1>Order 0x<?php echo htmlspecialchars($orderId) ?></h1>
        <date class="mono"><?php echo date("d/m/Y") ?></date>

        <?php if (basename($_SERVER['REQUEST_URI']) == 'order.php' && !isset($_POST['orderId'])): ?>

        <form action="/order.php/<?php echo $orderId ?>" method="post">
            <textarea name="message" id="message" cols="90" rows="10" required placeholder="Encrypted message to seller containing your shipping address and any other information required to complete the sale. Make sure it is valid, you will not be able to change it."></textarea>
            <p>We highly recommend you encrypt your shipping address with the PGP key below</p>
            <textarea cols="90" rows="6" readonly><?php echo getPublicPGPKey() ?></textarea>
            <input type="hidden" name="orderId" value="<?php echo $orderId ?>">
            <input type="submit" value="Continue →">
        </form>


        <?php
            else:
            $price = fetchExchangeRate() * 10;

            if (!empty($_POST)) {
                if (!isset($_POST['orderId']) || !isset($_POST['message'])) {
                    die("Something went wrong!");
                }

                $alreadyExists = fetchOrder($_POST['orderId']);
                if ($alreadyExists) {
                    die("<p>Order with this id already exists!</p>");
                }

                $res = generateMoneroAddress();
                $address = $res['address'];
                $address_index = $res['address_index'];

                $message = $_POST['message'];
                $order = createOrder($orderId, $address, $address_index, $message, $price, time());
                
                $URI = $_SERVER['REQUEST_URI'];
                header("location:$URI");
            }
            else {
                $order = fetchOrder($orderId);

                $address = $order['address'];
                $address_index = $order['address_index'];
                $price = $order['price'];
            }
        ?>

            <?php if ($order): ?>

                <?php if ($order['completed']): ?>
                    <p>Order completed! ✨<br/>Your item should be sent within 72hrs 🥒</p>
                
                <?php else: ?>

                    <div class="container">
                        <div>
                            <h2>Price</h2>
                            <img id="monero" src="/assets/monero.png" alt="monero">
                            <span class="mono"> <?php echo $price ?> XMR</span>
                            <h2>Your Monero (XMR) address is:</h2>
                            <span class="mono address"><?php echo $address ?></span>
                        </div>
                        <?php 
                            $link = 'monero:' . $address . '?tx_amount=' . $price;
                            $qr = QRCode::getMinimumQRCode($link, QR_ERROR_CORRECT_LEVEL_M);
                            $qr->printHTML();
                        ?>
                    </div>

                    <p class="info">Do NOT send the payment more than once. Wait 5-10 minutes and refresh the page, if the coins were received, they'll appear here. The item will only be shipped once 10 confirmations are completed within the Monero network (usually takes 20-30 minutes).</p>

                    <?php
                        $transactions = fetchIncomingTransactions($address_index);

                        if ($transactions) {
                            echo '<h2>Incoming transactions</h2>';
                        }

                        $confirmedTotal = 0;

                        foreach ($transactions as $transaction) {
                            $amount = $transaction['amount'] / 10**12;
                            $txid = $transaction['txid'];
                            $confirmations = $transaction['confirmations'];

                            echo '<div class="transaction">'
                                . '<span class="mono">Amount: ' . $amount . ' XMR</span>'
                                . '<span class="mono">Tx ID: ' . $txid . '</span>'
                                . '<span class="mono">Confirmations: ' . $confirmations . '</span>'
                                . '</div>';

                            if ($confirmations >= 10) {
                                $confirmedTotal += $amount;
                                if ($confirmedTotal >= $price) {
                                    completeOrder($orderId);
                                }
                            }
                        }
                    ?>

                <?php endif ?>

            <?php else: ?>

                <p class="error">The order was not found. Please try again later.</p>

            <?php endif; ?>

        <?php endif; ?>

    </main>
</body>
</html>
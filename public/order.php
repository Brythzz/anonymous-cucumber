<?php
    include '../utils.php';
    include '../qrcode.php';

    $urlId = basename($_SERVER['REQUEST_URI']);
    $orderId = $_POST['orderId'] ?: getRandomHex();
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
            <input type="hidden" name="orderId" value="<?php echo $orderId ?>">
            <input type="submit" value="Continue →">
        </form>


        <?php
            else:
            $price = fetchExchangeRate() * 10;
        ?>

            <div class="container">
                <div>
                    <h2>Price</h2>
                    <img id="monero" src="/assets/monero.png" alt="monero">
                    <span class="mono"> <?php echo $price ?> XMR</span>
                    <h2>Your Monero (XMR) address is:</h2>
                    <?php $address = '84sVt19zqDyjN28bYhc4EajcVqFH5cJLAW9uQ7kasG944Tgq9og1R3gbpYfCea5zk9AGU35M4SQPmSM7Z983Jp3A1rLhrF4' ?>
                    <span class="mono address"><?php echo $address ?></span>
                </div>
                <?php 
                    $link = 'monero:' . $address . '?tx_amount=' . $price;
                    $qr = QRCode::getMinimumQRCode($link, QR_ERROR_CORRECT_LEVEL_M);
                    $qr->printHTML();
                ?>
            </div>

            <p class="info">Do NOT send the payment more than once. Wait 5-10 minutes and refresh the page, if the coins were received, they'll appear here. The item will only be shipped as soon as 10 confirmations are completed within the Monero network (usually 20-30 minutes).</p>

        <?php endif; ?>

    </main>
</body>
</html>
<?php
    include '../utils.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anonymous Cucumber</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <main>
        <h1>Anonymous Cucumber</h1>
        <p>The best way to have cucumbers delivered to your home anonymously!</p>

        <div class="product">
            <div id="cucumber">
                <img src="/assets/cucumber.png" alt="cucumber">
            </div>
            <div class="details">
                <h3>Price</h3>
                <span class="mono">10.00 USD</span>
                <br/>
                <img id="monero" src="/assets/monero.png" alt="monero">
                <span class="mono"> <?php echo fetchExchangeRate() * 10; ?> XMR</span>

                <a class="button" href="/order.php">Purchase →</a>
            </div>
        </div>
    </main>
</body>
</html>
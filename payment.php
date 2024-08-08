<?php
session_start();
include 'db_connect.php';

// Fetch product details
$product_id = $_GET['product_id'];
$product_query = "SELECT * FROM products WHERE id = $product_id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

// Handle payment confirmation submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    $payment_type = $_POST['payment_type'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $receipt_number = $_POST['receipt_number'];

    // Insert payment confirmation details into the database
    $query = "INSERT INTO payment_confirmations (product_id, payment_type, payment_date, amount, receipt_number) VALUES ('$product_id', '$payment_type', '$payment_date', '$amount', '$receipt_number')";
    if (mysqli_query($conn, $query)) {
        // Insert notification for admin
        $notification_query = "INSERT INTO notifications (user_id, message, type, status, created_at) VALUES (1, 'Payment confirmation submitted for product ID: $product_id', 'payment_confirmation', 'unread', NOW())";
        mysqli_query($conn, $notification_query);
        
        $confirmation_message = "Payment confirmation submitted successfully!";
    } else {
        $confirmation_message = "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Methods</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function showPaymentDetails(paymentMethod) {
            document.getElementById('credit_card_details').style.display = 'none';
            document.getElementById('mobile_phone_details').style.display = 'none';
            document.getElementById('paypal_details').style.display = 'none';

            if (paymentMethod === 'credit_card') {
                document.getElementById('credit_card_details').style.display = 'block';
            } else if (paymentMethod === 'mobile_phone') {
                document.getElementById('mobile_phone_details').style.display = 'block';
            } else if (paymentMethod === 'paypal') {
                document.getElementById('paypal_details').style.display = 'block';
            }
        }
    </script>
</head>
<body>

    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Payment Methods</h1>
        <p>You have won the auction for: <?php echo htmlspecialchars($product['name']); ?></p>
        <p>Winning Bid: <?php echo htmlspecialchars($product['max_bid_amount']); ?></p>

        <h2>Select Payment Method</h2>
        <form method="post" action="payment.php?product_id=<?php echo $product_id; ?>">
            <div>
                <input type="radio" id="credit_card" name="payment_method" value="credit_card" onclick="showPaymentDetails('credit_card')" required>
                <label for="credit_card">Credit Card</label>
            </div>
            <div id="credit_card_details" style="display: none;">
                <p>Credit Card Number: 1234 5678 9012 3456</p>
                <p>Name on Card: John Doe</p>
                <p>Expiry Date: 12/25</p>
                <p>CVC: 123</p>
            </div>

            <div>
                <input type="radio" id="mobile_phone" name="payment_method" value="mobile_phone" onclick="showPaymentDetails('mobile_phone')">
                <label for="mobile_phone">Mobile Phone</label>
            </div>
            <div id="mobile_phone_details" style="display: none;">
                <p>Mobile Network: MTN</p>
                <p>Mobile Number: +123 456 7890</p>
                <p>Name: John Doe</p>
            </div>

            <div>
                <input type="radio" id="paypal" name="payment_method" value="paypal" onclick="showPaymentDetails('paypal')">
                <label for="paypal">PayPal</label>
            </div>
            <div id="paypal_details" style="display: none;">
                <p>PayPal Email: johndoe@example.com</p>
                <p>Name: John Doe</p>
            </div>
        </form>

        <h2>Confirm Your Payment</h2>
        <?php if (isset($confirmation_message)) echo "<p>$confirmation_message</p>"; ?>
        <form method="post" action="payment.php?product_id=<?php echo $product_id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div>
                <label for="payment_type">Type of Payment:</label>
                <input type="text" id="payment_type" name="payment_type" required>
            </div>
            <div>
                <label for="payment_date">Date of Payment:</label>
                <input type="date" id="payment_date" name="payment_date" required>
            </div>
            <div>
                <label for="amount">Amount Paid:</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div>
                <label for="receipt_number">Receipt Number:</label>
                <input type="text" id="receipt_number" name="receipt_number" required>
            </div>
            <button type="submit" name="confirm_payment">Submit Payment Confirmation</button>
        </form>
    </div>
</body>
</html>

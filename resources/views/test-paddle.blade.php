<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Creativ Stock - Paddle Test</title>

</head>

<body>

    <h1>Creativ Stock</h1>

    <h2>Test Purchase</h2>

    <p>Test Plan — $10.00 USD</p>

    <button id="buy-button">
        Buy Now
    </button>
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>

    <script>
        // Tell Paddle.js we're using the Sandbox environment.
        Paddle.Environment.set("sandbox");

        // Initialize Paddle with the Sandbox client-side token.
        Paddle.Initialize({
            token: @json(config('paddle.client_token')),
        });

        // Open checkout when the button is clicked.
        document.getElementById('buy-button').addEventListener('click', function () {

            Paddle.Checkout.open({
                items: [
                    {
                        priceId: @json(config('paddle.test_price_id')),
                        quantity: 1
                    }
                ]
            });

        });
    </script>

</body>
</html>
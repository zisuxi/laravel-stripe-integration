<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .payment-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        #card-element {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        #submit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        #submit:hover {
            background-color: #0056b3;
        }

        #payment-response {
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <h2 class="text-center">Stripe Payment</h2>
        <form id="payment-form">
            <div id="card-element"></div>
            <button id="submit">Pay</button>
            <div id="payment-response"></div>
        </form>
    </div>

    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const {
                token,
                error
            } = await stripe.createToken(card);
            if (error) {
                document.getElementById('payment-response').innerText = error.message;
            } else {
                const response = await fetch('{{ route('payment.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        stripeToken: token.id
                    })
                });

                const data = await response.json();
                document.getElementById('payment-response').innerText = data.success ? data.success : data
                .error;
            }
        });
    </script>
</body>

</html>

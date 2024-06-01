<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<div class="max-w-md mx-auto mt-10 bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h5 class="text-2xl font-bold mb-6">Subscription Form</h5>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('subscribe') }}" method="POST" id="payment-form">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" class="form-control w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="plan" class="block text-gray-700 font-bold mb-2">Plan</label>
                <select name="plan" id="plan" class="form-control w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="price_1PMpQiIjvJe8MwyWXTCe1M4s">Monthly Plan - INR 49</option>
                    <option value="price_1PMpTAIjvJe8MwyWYQayQ1Ma">Annual Plan - INR 249</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="card-element" class="block text-gray-700 font-bold mb-2">Credit or debit card</label>
                <div id="card-element" class="form-control w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            </div>
            <button type="submit" class="btn btn-primary w-full p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">Subscribe</button>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                alert(result.error.message);
            } else {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', result.token.id);
                form.appendChild(hiddenInput);

                form.submit();
            }
        });
    });
</script>

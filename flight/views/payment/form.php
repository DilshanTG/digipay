<div class="container">
    <div class="header">
        <h1>DigiMart Solutions</h1>
        <p>Secure Payment Gateway</p>
    </div>

    <div class="card">
        <h2>Make a Payment</h2>
        <form method="POST" action="/pay/process">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="whatsapp">WhatsApp Number</label>
            <div style="display: flex; gap: 10px;">
                <select name="country_code" style="width: 30%;">
                    <option value="94">+94 (LK)</option>
                    <option value="1">+1 (US)</option>
                    <option value="44">+44 (UK)</option>
                </select>
                <input type="tel" id="whatsapp" name="whatsapp" placeholder="771234567" required style="width: 70%;">
            </div>

            <label for="amount">Amount (LKR)</label>
            <input type="number" id="amount" name="amount" min="1" step="0.01" required>

            <label for="note">Note (Optional)</label>
            <textarea id="note" name="note" rows="3" placeholder="Payment description..."></textarea>

            <button type="submit" class="btn-success">Proceed to Payment</button>
        </form>
    </div>
</div>

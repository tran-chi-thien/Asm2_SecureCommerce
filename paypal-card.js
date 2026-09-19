/**
 * Renders a PayPal "Debit or Credit Card" button so shoppers can pay with
 * Visa / MasterCard inline, without leaving the page (like a Stripe card
 * element), while PayPal handles the actual card processing.
 */
function renderPayPalCardButton() {
    var container = document.getElementById('paypal-card-button-container');
    if (!container || container.childElementCount > 0 || typeof paypal === 'undefined' || !paypal.Buttons) {
        return;
    }

    var buttons = paypal.Buttons({
        fundingSource: paypal.FUNDING.CARD,
        style: { shape: 'rect', color: 'black', label: 'pay', height: 45 },

        createOrder: function () {
            return fetch('create-order.php', { method: 'POST' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.error) { throw new Error(data.error); }
                    return data.id;
                });
        },

        onApprove: function (data) {
            return fetch('capture-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'orderID=' + encodeURIComponent(data.orderID)
            })
                .then(function (res) { return res.json(); })
                .then(function (result) {
                    if (result.error) { throw new Error(result.error); }
                    var paymentMethodField = document.getElementById('payment-method');
                    var params = new URLSearchParams({
                        status: 'success',
                        tx: result.transactionId,
                        amt: result.amount,
                        cc: result.currency,
                        payment_method: paymentMethodField ? paymentMethodField.value : 'Card'
                    });
                    window.location.href = 'success.php?' + params.toString();
                })
                .catch(function (err) {
                    console.error(err);
                    alert('Card payment could not be completed. Please try again.');
                });
        },

        onError: function (err) {
            console.error(err);
            alert('Card payment could not be completed. Please check your card details and try again.');
        }
    });

    if (buttons.isEligible()) {
        buttons.render('#paypal-card-button-container');
    } else {
        container.innerHTML = '<p>Card payment is not available right now.</p>';
    }
}

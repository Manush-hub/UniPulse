let currentStep = 1;
const paymentGatewayConfig = window.paymentGatewayConfig || {};
let ticketPrice = Number(paymentGatewayConfig.ticketPrice || 0);
const processPaymentUrl = paymentGatewayConfig.processPaymentUrl || '/unipulse/public/user/paymentgateway/processPayment';
let selectedPaymentMethod = 'card';
let hasUploadedSlip = false;
let currentOrderId = '';

function updateProgress() {
    document.querySelector('.payment-progress-bar').setAttribute('data-step', currentStep);

    const steps = document.querySelectorAll('.payment-step');
    steps.forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 === currentStep) {
            step.classList.add('active');
        } else if (index + 1 < currentStep) {
            step.classList.add('completed');
        }
    });
}

function showStep(step) {
    document.querySelectorAll('.payment-form-step').forEach(s => s.classList.remove('active'));
    document.querySelector(`.payment-form-step[data-step="${step}"]`).classList.add('active');
}

function validateStep(step) {
    const currentStepEl = document.querySelector(`.payment-form-step[data-step="${step}"]`);
    const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
    let valid = true;

    inputs.forEach(input => {
        input.classList.remove('payment-error');
        const errorMsg = input.parentElement.querySelector('.payment-error-message');
        if (errorMsg) errorMsg.remove();

        if (!input.value.trim()) {
            valid = false;
            input.classList.add('payment-error');
            const error = document.createElement('div');
            error.className = 'payment-error-message';
            error.textContent = 'This field is required';
            input.parentElement.appendChild(error);
        }

        if (input.type === 'email' && input.value && !input.value.includes('@')) {
            valid = false;
            input.classList.add('payment-error');
            const error = document.createElement('div');
            error.className = 'payment-error-message';
            error.textContent = 'Please enter a valid email';
            input.parentElement.appendChild(error);
        }
    });

    // Additional validation for step 2 based on payment method
    if (step === 2) {
        if (selectedPaymentMethod === 'slip' && !hasUploadedSlip) {
            valid = false;
            document.getElementById('slipUpload').classList.add('payment-error');
            const error = document.createElement('div');
            error.className = 'payment-error-message';
            error.textContent = 'Please upload a payment slip';
            document.getElementById('slipUpload').parentElement.appendChild(error);
        }
    }

    return valid;
}

function nextStep(step) {
    if (validateStep(step)) {
        // Update order summary for step 2
        if (step === 1) {
            updateOrderSummary();
        }

        currentStep++;
        updateProgress();
        showStep(currentStep);
    }
}

function prevStep(step) {
    currentStep--;
    updateProgress();
    showStep(currentStep);
}

function selectPayment(element, method) {
    document.querySelectorAll('.payment-method-option').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    selectedPaymentMethod = method;

    // Show/hide appropriate payment forms
    if (method === 'slip') {
        document.getElementById('card-payment-form').style.display = 'none';
        document.getElementById('slip-payment-form').style.display = 'block';
        document.getElementById('paymentButton').textContent = 'Upload Payment Slip';
    } else {
        document.getElementById('card-payment-form').style.display = 'block';
        document.getElementById('slip-payment-form').style.display = 'none';
        document.getElementById('paymentButton').textContent = 'Complete Payment';
    }
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    let quantity = parseInt(quantityInput.value, 10);
    if (quantity < 10) {
        quantityInput.value = quantity + 1;
        updateTicketTotal();
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    let quantity = parseInt(quantityInput.value, 10);
    if (quantity > 1) {
        quantityInput.value = quantity - 1;
        updateTicketTotal();
    }
}

function updateTicketTotal() {
    const quantity = parseInt(document.getElementById('quantity').value, 10);
    const displayQuantity = document.getElementById('displayQuantity');
    const totalPrice = document.getElementById('totalPrice');

    displayQuantity.textContent = quantity;
    const total = ticketPrice * quantity;
    totalPrice.textContent = total.toFixed(2);
}

function updateOrderSummary() {
    const quantity = document.getElementById('quantity').value;

    document.getElementById('summaryQuantity').textContent = quantity;
    document.getElementById('summaryPrice').textContent = ticketPrice.toFixed(2);
    document.getElementById('summaryTotal').textContent = (ticketPrice * quantity).toFixed(2);
}

function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        document.getElementById('fileName').textContent = fileName;
        document.getElementById('slipUpload').classList.add('has-file');
        hasUploadedSlip = true;

        // Remove any existing error messages
        const errorMsg = document.getElementById('slipUpload').parentElement.querySelector('.payment-error-message');
        if (errorMsg) errorMsg.remove();
        document.getElementById('slipUpload').classList.remove('payment-error');
    }
}

function generateBarcode(orderId) {
    // Generate barcode using JsBarcode library
    JsBarcode('#barcode', orderId, {
        format: 'CODE128',
        width: 2,
        height: 100,
        displayValue: true,
        fontSize: 16,
        background: '#ffffff',
        lineColor: '#333333'
    });
}

function downloadBarcode() {
    // Get the SVG element
    const svg = document.getElementById('barcode');

    // Create a canvas to convert SVG to image
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    // Get SVG data
    const svgData = new XMLSerializer().serializeToString(svg);
    const img = new Image();

    // Create a blob from the SVG data
    const svgBlob = new Blob([svgData], {
        type: 'image/svg+xml;charset=utf-8'
    });
    const url = URL.createObjectURL(svgBlob);

    img.onload = function () {
        // Set canvas dimensions
        canvas.width = svg.clientWidth;
        canvas.height = svg.clientHeight;

        // Draw the image on canvas
        ctx.drawImage(img, 0, 0);

        // Create download link
        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.href = pngUrl;
        downloadLink.download = `ticket-barcode-${currentOrderId}.png`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

        // Clean up
        URL.revokeObjectURL(url);
    };

    img.src = url;
}

function processPayment() {
    if (!validateStep(2)) return;

    const paymentButton = document.getElementById('paymentButton');
    paymentButton.disabled = true;
    paymentButton.textContent = 'Processing...';

    const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
    const totalAmount = (ticketPrice * quantity).toFixed(2);
    const eventId = document.getElementById('eventId').value;

    // Build form data to send to the server
    const formData = new FormData();
    formData.append('event_id', eventId);
    formData.append('payment_method', selectedPaymentMethod);
    formData.append('amount', totalAmount);
    formData.append('quantity', quantity);

    // If payment slip, attach the file
    if (selectedPaymentMethod === 'slip') {
        const slipFile = document.getElementById('slipFile').files[0];
        if (slipFile) {
            formData.append('payment_slip', slipFile);
        }
    }

    // Send AJAX request to backend
    console.log('Sending payment request:', {
        event_id: eventId,
        payment_method: selectedPaymentMethod,
        amount: totalAmount,
        quantity: quantity
    });

    fetch(processPaymentUrl, {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment response:', data);
            if (data.success) {
                currentOrderId = data.transaction_id || Math.floor(100000 + Math.random() * 900000).toString();

                if (selectedPaymentMethod === 'slip') {
                    document.getElementById('confirmationTitle').textContent = 'Payment Slip Uploaded!';
                    document.getElementById('confirmationMessage').innerHTML =
                        'Your payment slip has been uploaded successfully.<br>' +
                        'Your tickets are pending confirmation and will be activated after payment verification.<br>' +
                        'This usually takes 1-2 business days.';
                }

                currentStep = 3;
                updateProgress();
                showStep(3);

                // Set order ID and generate barcode
                document.getElementById('orderId').textContent = currentOrderId;
                document.getElementById('barcodeOrderId').textContent = currentOrderId;
                generateBarcode(currentOrderId);
            } else {
                alert('Payment failed: ' + (data.error || 'Unknown error. Please try again.'));
                paymentButton.disabled = false;
                paymentButton.textContent = selectedPaymentMethod === 'slip' ? 'Upload Payment Slip' : 'Complete Payment';
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            alert('Payment processing failed. Please try again.');
            paymentButton.disabled = false;
            paymentButton.textContent = selectedPaymentMethod === 'slip' ? 'Upload Payment Slip' : 'Complete Payment';
        });
}

function resetForm() {
    currentStep = 1;
    updateProgress();
    showStep(1);
    document.querySelectorAll('input').forEach(input => input.value = '');
    document.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    document.getElementById('quantity').value = 1;
    updateTicketTotal();
    selectPayment(document.querySelector('.payment-method-option'), 'card');
    hasUploadedSlip = false;
    document.getElementById('fileName').textContent = '';
    document.getElementById('slipUpload').classList.remove('has-file');
    document.getElementById('confirmationTitle').textContent = 'Payment Successful!';
    document.getElementById('confirmationMessage').textContent = 'A confirmation email has been sent to your email address.';
    currentOrderId = '';
}

// Format card number input
document.getElementById('cardnumber').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date input
document.getElementById('expiry').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// Only allow numbers in CVV
document.getElementById('cvv').addEventListener('input', function (e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Initialize ticket total
updateTicketTotal();

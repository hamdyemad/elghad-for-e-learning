<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a2e; color: #eee; font-family: monospace; padding: 30px; }
        .card { background: #16213e; border: 1px solid #0f3460; }
        .form-control { background: #0f3460; color: #eee; border: 1px solid #533483; }
        .form-control:focus { background: #0f3460; color: #eee; border-color: #e94560; box-shadow: none; }
        pre { background: #0f3460; padding: 15px; border-radius: 8px; white-space: pre-wrap; word-break: break-all; max-height: 500px; overflow-y: auto; }
        .step { border: 2px solid #533483; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .step.active { border-color: #e94560; }
        .step.done { border-color: #00b894; }
        .token-box { background: #0f3460; padding: 10px; border-radius: 5px; word-break: break-all; font-size: 0.8rem; max-height: 80px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Payment API Tester</h2>

        <div class="row">
            <div class="col-md-6">

                <!-- Step 1: Signin -->
                <div class="step" id="step1">
                    <h5>Step 1: Signin</h5>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-control" id="signinUrl" value="http://160.19.103.122:40120/YusorOnline/api/OnlinePaymentServices/Signin">
                    </div>
                    <div class="form-group">
                        <label>Body (JSON)</label>
                        <textarea class="form-control" id="signinBody" rows="6">{
    "userId": 100589,
    "pin": "U3f@Zh",
    "providerId": 7070,
    "authUserType": 0
}</textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-block" onclick="doSignin()">
                        Send Signin Request
                    </button>
                    <div id="step1Loading" class="text-center d-none mt-2">
                        <div class="spinner-border spinner-border-sm text-danger"></div> Sending...
                    </div>
                    <pre id="step1Response" class="mt-2 d-none"></pre>
                </div>

                <!-- Step 2: OpenSession -->
                <div class="step" id="step2">
                    <h5>Step 2: OpenSession (Payment)</h5>
                    <div class="alert alert-info small mb-3">
                        Token from Step 1 will be used automatically as Bearer token.
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-control" id="paymentUrl" value="http://160.19.103.122:40120/YusorOnline/api/OnlinePaymentServices/OpenSession">
                    </div>
                    <div class="form-group">
                        <label>IdentityCard</label>
                        <input type="text" class="form-control" id="identityCard" value="333005123">
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="amount" value="10">
                    </div>
                    <div class="form-group">
                        <label>TransactionID</label>
                        <input type="number" class="form-control" id="transactionId" value="1">
                    </div>
                    <div class="form-group">
                        <label>OnlineOperation (1=Pay, 2=Retrieve)</label>
                        <select class="form-control" id="onlineOperation">
                            <option value="1">1 - Pay</option>
                            <option value="2">2 - Retrieve</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-success btn-block" onclick="doPayment()" id="payBtn" disabled>
                        Send Payment Request
                    </button>
                    <div id="step2Loading" class="text-center d-none mt-2">
                        <div class="spinner-border spinner-border-sm text-success"></div> Sending...
                    </div>
                    <pre id="step2Response" class="mt-2 d-none"></pre>
                </div>

            </div>

            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h5>Token Status</h5>
                    <div id="tokenStatus" class="mb-3">
                        <span class="badge badge-secondary">No token yet</span>
                    </div>
                    <div id="tokenBox" class="token-box d-none"></div>

                    <hr>
                    <h5>Full Log</h5>
                    <pre id="fullLog">Waiting for requests...</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
    let jwtToken = null;
    let log = '';

    function appendLog(text) {
        log += text + '\n';
        document.getElementById('fullLog').textContent = log;
    }

    async function doSignin() {
        const btn = document.querySelector('#step1 .btn');
        const loading = document.getElementById('step1Loading');
        const responseEl = document.getElementById('step1Response');
        const step = document.getElementById('step1');

        btn.disabled = true;
        loading.classList.remove('d-none');
        responseEl.classList.add('d-none');
        step.className = 'step active';

        appendLog('--- Step 1: Signin ---');
        appendLog('POST ' + document.getElementById('signinUrl').value);

        try {
            const res = await fetch('/test-api', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    url: document.getElementById('signinUrl').value,
                    body: document.getElementById('signinBody').value
                })
            });
            const data = await res.json();

            responseEl.classList.remove('d-none');
            responseEl.textContent = JSON.stringify(data, null, 2);

            appendLog('HTTP ' + data.http_code);

            if (data.response && data.response.content && data.response.content.value) {
                jwtToken = data.response.content.value;
                document.getElementById('tokenBox').textContent = jwtToken;
                document.getElementById('tokenBox').classList.remove('d-none');
                document.getElementById('tokenStatus').innerHTML = '<span class="badge badge-success">Token OK</span>';
                document.getElementById('payBtn').disabled = false;
                step.className = 'step done';
                appendLog('Token: ' + jwtToken.substring(0, 50) + '...');
                appendLog('Step 1 DONE\n');
            } else {
                document.getElementById('tokenStatus').innerHTML = '<span class="badge badge-danger">No token</span>';
                step.className = 'step';
                appendLog('ERROR: No token in response\n');
            }
        } catch (err) {
            responseEl.classList.remove('d-none');
            responseEl.textContent = 'Error: ' + err.message;
            appendLog('Error: ' + err.message);
            step.className = 'step';
        } finally {
            btn.disabled = false;
            loading.classList.add('d-none');
        }
    }

    async function doPayment() {
        if (!jwtToken) {
            alert('Please complete Step 1 first');
            return;
        }

        const btn = document.getElementById('payBtn');
        const loading = document.getElementById('step2Loading');
        const responseEl = document.getElementById('step2Response');
        const step = document.getElementById('step2');

        btn.disabled = true;
        loading.classList.remove('d-none');
        responseEl.classList.add('d-none');
        step.className = 'step active';

        const body = {
            "IdentityCard": document.getElementById('identityCard').value,
            "Amount": parseFloat(document.getElementById('amount').value),
            "TransactionID": parseInt(document.getElementById('transactionId').value),
            "OnlineOperation": parseInt(document.getElementById('onlineOperation').value)
        };

        appendLog('--- Step 2: OpenSession ---');
        appendLog('POST ' + document.getElementById('paymentUrl').value);
        appendLog('Body: ' + JSON.stringify(body));

        try {
            const res = await fetch('/test-api-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    url: document.getElementById('paymentUrl').value,
                    body: JSON.stringify(body),
                    token: jwtToken
                })
            });
            const data = await res.json();

            responseEl.classList.remove('d-none');
            responseEl.textContent = JSON.stringify(data, null, 2);

            appendLog('HTTP ' + data.http_code);
            appendLog('Response: ' + JSON.stringify(data.response));
            step.className = data.http_code >= 200 && data.http_code < 300 ? 'step done' : 'step';
        } catch (err) {
            responseEl.classList.remove('d-none');
            responseEl.textContent = 'Error: ' + err.message;
            appendLog('Error: ' + err.message);
            step.className = 'step';
        } finally {
            btn.disabled = false;
            loading.classList.add('d-none');
        }
    }
    </script>
</body>
</html>

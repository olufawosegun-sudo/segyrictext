<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Confirmed</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .container {
            max-width: 560px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0B132B 0%, #1C2541 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .content {
            padding: 32px 28px;
        }
        .badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            display: inline-block;
            padding: 6px 14px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .amount-box {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .amount-val {
            font-size: 32px;
            font-weight: 900;
            color: #7C3AED;
            font-family: -apple-system, BlinkMacSystemFont, monospace;
        }
        .table-details {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        .table-details td {
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-details td.label {
            color: #64748b;
            font-weight: 500;
        }
        .table-details td.value {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verixa</h1>
            <p style="margin: 6px 0 0; font-size: 13px; color: #94a3b8;">Wallet Funding Notification</p>
        </div>

        <div class="content">
            <div class="badge">Payment Successful</div>

            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6;">
                Hello <strong>{{ $user->name ?? 'Valued Customer' }}</strong>,
            </p>
            <p style="margin: 0 0 20px; font-size: 14.5px; line-height: 1.6; color: #475569;">
                Your wallet deposit has been verified and processed successfully.
            </p>

            <div class="amount-box">
                <div class="amount-title">Amount Credited</div>
                <div class="amount-val">+{{ $wallet->currency?->symbol ?? '₦' }}{{ number_format($transaction->amount, 2) }}</div>
            </div>

            <table class="table-details">
                <tr>
                    <td class="label">Reference</td>
                    <td class="value" style="font-family: monospace; font-size: 12.5px;">{{ $transaction->reference }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Channel</td>
                    <td class="value">Online Checkout (Stripe)</td>
                </tr>
                <tr>
                    <td class="label">Date & Time</td>
                    <td class="value">{{ now()->format('M d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <td class="label">New Available Balance</td>
                    <td class="value" style="color: #7C3AED; font-size: 15px;">
                        {{ $wallet->currency?->symbol ?? '₦' }}{{ number_format($wallet->available_balance, 2) }}
                    </td>
                </tr>
            </table>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                You can now use your funded balance to purchase numbers and complete SMS verifications instantly.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Verixa. All rights reserved.<br>
            If you did not authorize this transaction, please contact support immediately.
        </div>
    </div>
</body>
</html>

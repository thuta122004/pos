<?php require_once 'dbconnect.php'; 
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();

if (!$transaction) die("Transaction not found.");

$items_stmt = $conn->prepare("SELECT ti.*, p.name FROM transaction_items ti JOIN products p ON ti.product_id = p.product_id WHERE ti.transaction_id = ?");
$items_stmt->bind_param("i", $id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Receipt #<?= str_pad($id, 6, "0", STR_PAD_LEFT) ?></title>
    <style>
        @page { margin: 0; }
        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .receipt-card { 
                border: none !important; 
                box-shadow: none !important; 
                width: 100% !important; 
                max-width: 80mm !important;
                padding: 2mm !important; 
                margin: 0 !important;
            }
            .print-hidden { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-6 flex flex-col items-center justify-center min-h-screen font-sans antialiased">
    
    <div class="receipt-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 w-full max-w-sm bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px]">
        <div class="text-center border-b border-dashed pb-4 mb-4">
            <div class="flex justify-center text-pink-500 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold uppercase tracking-wider text-slate-800 mt-1">Sales Receipt</h2>
            <p class="text-xs text-slate-400 mt-0.5">Thank you for your purchase!</p>
        </div>
        
        <div class="text-xs text-slate-500 space-y-1 mb-4 font-mono">
            <div class="flex justify-between"><span>Receipt No:</span><span class="font-bold text-slate-700">#<?= str_pad($id, 6, "0", STR_PAD_LEFT) ?></span></div>
            <div class="flex justify-between"><span>Date/Time:</span><span><?= $transaction['transaction_date'] ?></span></div>
            <?php if (!empty($transaction['customer_name'])): ?>
            <div class="flex justify-between"><span>Customer:</span><span class="font-bold text-slate-700"><?= htmlspecialchars($transaction['customer_name']) ?></span></div>
            <?php endif; ?>
        </div>

        <table class="w-full text-sm font-mono border-b border-dashed pb-4 mb-4">
            <thead class="text-xs text-slate-400 uppercase tracking-tight border-b border-slate-100">
                <tr>
                    <th class="text-left pb-2 font-normal">Item</th>
                    <th class="text-center pb-2 font-normal">Qty</th>
                    <th class="text-center pb-2 font-normal">Price</th> <th class="text-right pb-2 font-normal">Total</th>
                </tr>
            </thead>

            <tbody class="text-slate-700">
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td class="py-1.5 truncate max-w-[100px]"><?= htmlspecialchars($item['name']) ?></td>
                    <td class="py-1.5 text-center"><?= $item['quantity'] ?></td>
                    <td class="py-1.5 text-center"><?= number_format($item['price']) ?></td> <td class="py-1.5 text-right"><?= number_format($item['quantity'] * $item['price']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="font-mono text-right space-y-1 text-sm">
            <div class="flex justify-between font-bold text-base text-slate-900 border-t pt-2">
                <span>TOTAL AMOUNT:</span>
                <span class="text-pink-600"><?= number_format($transaction['total_amount']) ?> MMK</span>
            </div>
        </div>

        <div class="mt-8 flex gap-3 print-hidden">
            <button onclick="window.print()" class="flex-1 bg-slate-800 text-white font-bold py-2 rounded-xl text-sm hover:bg-slate-900 transition">Print Receipt</button>
            <a href="history.php" class="flex-1 bg-slate-200 text-slate-700 font-bold py-2 rounded-xl text-center text-sm hover:bg-slate-300 transition">Back</a>
        </div>
    </div>
</body>
</html>
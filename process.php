<?php
require_once 'dbconnect.php';

date_default_timezone_set('Asia/Yangon');

if (isset($_POST['checkout']) && !empty($_POST['items'])) {
    $posted_items = $_POST['items'];
    
    $conn->begin_transaction();

    try {
        $total_amount = 0;
        $items_to_save = [];

        foreach ($posted_items as $id => $data) {
            $product_id = (int)$data['product_id'];
            $qty = (int)$data['quantity'];

            if ($qty <= 0) continue;

            $stmt = $conn->prepare("SELECT name, price, stock_quantity FROM products WHERE product_id = ? AND is_active = 1");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if (!$product || $product['stock_quantity'] < $qty) {
                throw new Exception("Error: item '{$product['name']}' is out of stock or unavailable.");
            }

            $line_total = $product['price'] * $qty;
            $total_amount += $line_total;

            $items_to_save[] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $qty,
                'line_total' => $line_total
            ];
        }

        if (empty($items_to_save)) {
            throw new Exception("Your cart is empty.");
        }

        $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';

        $stmt = $conn->prepare("INSERT INTO transactions (total_amount, customer_name) VALUES (?, ?)");
        $stmt->bind_param("ds", $total_amount, $customer_name);
        $stmt->execute();
        $transaction_id = $conn->insert_id;

        foreach ($items_to_save as $item) {
            $item_stmt = $conn->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $item_stmt->bind_param("iiid", $transaction_id, $item['product_id'], $item['quantity'], $item['price']);
            $item_stmt->execute();

            $update_stmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $update_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $update_stmt->execute();
        }

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        die("<div style='color:red; font-family:sans-serif; padding:20px;'><h3>Transaction Failed</h3>" . htmlspecialchars($e->getMessage()) . "<br><br><a href='index.php'>Return to Store</a></div>");
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <title>Receipt #<?= str_pad($transaction_id, 6, "0", STR_PAD_LEFT) ?></title>
        <style>
            @page {
                margin: 0;
            }

            @media print {
                body { 
                    background: #ffffff !important; 
                    padding: 0 !important; 
                    margin: 0 !important;
                }
                
                .receipt-card { 
                    border: none !important; 
                    box-shadow: none !important; 
                    max-w: 100% !important; 
                    width: 100% !important; 
                    padding: 6mm !important; 
                    margin: 0 auto !important;
                    background-image: none !important;
                }
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
                <h2 class="text-xl font-bold uppercase tracking-wider text-slate-800 mt-1">Sales Receipt</h2> <p class="text-xs text-slate-400 mt-0.5">Thank you for your purchase!</p>
            </div>
            
            <div class="text-xs text-slate-500 space-y-1 mb-4 font-mono">
                <div class="flex justify-between"><span>Receipt No:</span><span class="font-bold text-slate-700">#<?= str_pad($transaction_id, 6, "0", STR_PAD_LEFT) ?></span></div>
                <div class="flex justify-between"><span>Date/Time:</span><span><?= date("Y-m-d H:i:s") ?></span></div>
                <?php if (!empty($customer_name)): ?>
                <div class="flex justify-between"><span>Customer:</span><span class="font-bold text-slate-700"><?= htmlspecialchars($customer_name) ?></span></div>
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
                    <?php foreach ($items_to_save as $item): ?>
                    <tr>
                        <td class="py-1.5 truncate max-w-[100px]"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="py-1.5 text-center"><?= $item['quantity'] ?></td>
                        <td class="py-1.5 text-center"><?= number_format($item['price']) ?></td> <td class="py-1.5 text-right"><?= number_format($item['line_total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="font-mono text-right space-y-1 text-sm">
                <div class="flex justify-between font-bold text-base text-slate-900 border-t pt-2">
                    <span>TOTAL AMOUNT:</span>
                    <span class="text-pink-600"><?= number_format($total_amount) ?> MMK</span>
                </div>
            </div>

            <div class="mt-8 flex gap-3 print:hidden">
                <button onclick="window.print()" class="flex-1 bg-slate-800 text-white font-bold py-2 rounded-xl text-sm hover:bg-slate-900 transition">Print Receipt</button>
                <a href="index.php" class="flex-1 bg-pink-500 text-white font-bold py-2 rounded-xl text-center text-sm hover:bg-pink-600 transition">New Sale</a>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    header("Location: index.php");
    exit();
}
?>
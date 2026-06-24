<?php
require_once 'dbconnect.php';

date_default_timezone_set('Asia/Yangon');

$rev_stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM transactions");
$rev_stmt->execute();
$revenue_res = $rev_stmt->get_result()->fetch_assoc();
$total_revenue = $revenue_res['total'] ?? 0;

$order_stmt = $conn->prepare("SELECT COUNT(transaction_id) as total FROM transactions");
$order_stmt->execute();
$orders_res = $order_stmt->get_result()->fetch_assoc();
$total_orders = $orders_res['total'] ?? 0;

$item_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM transaction_items");
$item_stmt->execute();
$item_res = $item_stmt->get_result()->fetch_assoc();
$total_items_sold = $item_res['total'] ?? 0;


$trend_stmt = $conn->prepare("SELECT DATE(transaction_date) as sales_date, SUM(total_amount) as day_total 
                              FROM transactions 
                              GROUP BY DATE(transaction_date) 
                              ORDER BY DATE(transaction_date) ASC");
$trend_stmt->execute();
$trend_result = $trend_stmt->get_result();

$timeline_labels = [];
$timeline_data = [];
while ($row = $trend_result->fetch_assoc()) {
    $timeline_labels[] = $row['sales_date'];
    $timeline_data[] = (float)$row['day_total'];
}

$product_stmt = $conn->prepare("SELECT p.name, SUM(ti.quantity) as total_qty 
                                FROM transaction_items ti 
                                JOIN products p ON ti.product_id = p.product_id 
                                GROUP BY ti.product_id 
                                ORDER BY total_qty DESC LIMIT 5");
$product_stmt->execute();
$product_result = $product_stmt->get_result();

$product_labels = [];
$product_data = [];
while ($row = $product_result->fetch_assoc()) {
    $product_labels[] = $row['name'];
    $product_data[] = (int)$row['total_qty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>POS | Performance Reports</title>
</head>
<body class="bg-slate-50 text-slate-800 antialiased p-6 md:p-12">

    <div class="max-w-6xl mx-auto">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10 pb-6 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-pink-500 tracking-tight">Business Reports</h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">Analyze total lifetime sales performance and trends</p>
            </div>
            <div class="flex gap-2">
                <a href="products.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 font-bold px-4 py-2.5 rounded-xl text-xs transition-all tracking-wide border border-slate-200/40 hover:border-pink-200/60 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Inventory
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    
            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Revenue</p>
                    <h3 class="text-2xl font-black text-slate-800 font-mono mt-1"><?= number_format($total_revenue) ?> <span class="text-xs font-sans font-bold text-slate-400">MMK</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9h.01M18 15h.01" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Orders Handled</p>
                    <h3 class="text-2xl font-black text-slate-800 font-mono mt-1"><?= number_format($total_orders) ?> <span class="text-xs font-sans font-bold text-slate-400">sales</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Items Sold</p>
                    <h3 class="text-2xl font-black text-slate-800 font-mono mt-1"><?= number_format($total_items_sold) ?> <span class="text-xs font-sans font-bold text-slate-400">units</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider text-slate-400">All-Time Revenue Timeline</h3>
                <div class="relative h-72 w-full">
                    <?php if(empty($timeline_labels)): ?>
                        <div class="absolute inset-0 flex items-center justify-center text-xs font-medium text-slate-400 bg-slate-50/50 rounded-xl border border-dashed">No historical transaction logs found in database.</div>
                    <?php endif; ?>
                    <canvas id="revenueTimelineChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider text-slate-400">All-Time Top 5 products</h3>
                <div class="relative h-72 w-full">
                    <?php if(empty($product_labels)): ?>
                        <div class="absolute inset-0 flex items-center justify-center text-xs font-medium text-slate-400 bg-slate-50/50 rounded-xl border border-dashed">No product metrics recorded yet.</div>
                    <?php endif; ?>
                    <canvas id="productShareChart"></canvas>
                </div>
            </div>

        </div>

    </div>

    <script>
        const timelineLabels = <?= json_encode($timeline_labels) ?>;
        const timelineData = <?= json_encode($timeline_data) ?>;
        
        const productLabels = <?= json_encode($product_labels) ?>;
        const productData = <?= json_encode($product_data) ?>;

        if (timelineLabels.length > 0) {
            const ctxTimeline = document.getElementById('revenueTimelineChart').getContext('2d');
            new Chart(ctxTimeline, {
                type: 'line',
                data: {
                    labels: timelineLabels,
                    datasets: [{
                        label: 'Daily Earnings (MMK)',
                        data: timelineData,
                        borderColor: '#ec4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#ec4899',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'monospace', size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { family: 'monospace', size: 11 } } }
                    }
                }
            });
        }

        if (productLabels.length > 0) {
            const ctxproducts = document.getElementById('productShareChart').getContext('2d');
            new Chart(ctxproducts, {
                type: 'bar',
                data: {
                    labels: productLabels,
                    datasets: [{
                        data: productData,
                        backgroundColor: '#1e293b',
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { family: 'monospace', size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' } } }
                    }
                }
            });
        }
    </script>
</body>
</html>
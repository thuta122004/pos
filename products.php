<?php require_once 'dbconnect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>POS | Inventory Management</title>
</head>
<body class="bg-slate-50 text-slate-800 antialiased p-6 md:p-12 relative overflow-x-hidden">
    
    <div class="max-w-6xl mx-auto">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10 pb-6 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-pink-500 tracking-tight">Inventory Management</h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">Configure products and stock levels</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="index.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 font-bold px-4 py-2.5 rounded-xl text-xs transition-all tracking-wide border border-slate-200/40 hover:border-pink-200/60 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Product Catalog
                </a>
                <a href="history.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 font-bold px-4 py-2.5 rounded-xl text-xs transition-all tracking-wide border border-slate-200/40 hover:border-pink-200/60 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History
                </a>
                <a href="reports.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 font-bold px-4 py-2.5 rounded-xl text-xs transition-all tracking-wide border border-slate-200/40 hover:border-pink-200/60 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                    View Reports
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 overflow-x-auto bg-white rounded-2xl border border-slate-200/60 shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/70 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4 font-bold w-20">Image</th>
                            <th class="p-4 font-bold">Item Details</th>
                            <th class="p-4 font-bold w-28 text-right">Price</th>
                            <th class="p-4 font-bold w-40 text-center">Stock Control</th>
                            <th class="p-4 font-bold w-24 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        $result = $conn->query("SELECT * FROM products WHERE is_active = 1");
                        
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <form action="process_product.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $row['product_id'] ?>">
                                    
                                    <td class="p-4 align-middle">
                                        <?php if (!empty($row['image_path'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="w-12 h-12 object-cover rounded-xl shadow-sm border border-slate-100">
                                        <?php else: ?>
                                            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-[10px] text-slate-400 font-bold border border-slate-100 tracking-wide uppercase">No Img</div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="p-4 align-middle">
                                        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="bg-transparent text-sm font-bold text-slate-800 outline-none focus:border-b focus:border-pink-500 w-full mb-1.5 transition" required>
                                        <input type="file" name="edit_image" accept="image/*" class="block text-[11px] text-slate-400 file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-pink-50 hover:file:text-pink-600 cursor-pointer w-full max-w-xs transition">
                                    </td>

                                    <td class="p-4 align-middle text-right">
                                        <div class="flex items-center justify-end gap-1.5 text-sm font-mono font-bold text-slate-800">
                                            <input type="number" name="price" value="<?= round($row['price']) ?>" step="100" min="100" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="bg-transparent text-right outline-none border-b border-transparent focus:border-pink-500 w-20 transition" required>
                                            <span class="text-xs text-slate-400 font-sans">MMK</span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center justify-center gap-2.5">
                                            <span class="bg-pink-50 text-pink-700 font-mono font-bold px-2.5 py-1 rounded-lg text-xs min-w-[32px] text-center border border-pink-100/60 shadow-sm">
                                                <?= $row['stock_quantity'] ?>
                                            </span>
                                            <input type="number" name="add_stock" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="+Qty" class="w-20 bg-slate-50 border border-slate-200/60 rounded-xl px-2 py-1 text-xs text-center font-mono font-bold outline-pink-500 transition">
                                        </div>
                                    </td>

                                    <td class="p-4 align-middle text-right whitespace-nowrap text-xs">
                                        <button type="submit" name="update" class="bg-slate-800 text-white font-bold px-3 py-1.5 rounded-xl hover:bg-slate-900 shadow-sm transition mr-1.5">Save</button>
                                        <button type="button" onclick="confirmDelete(<?= $row['product_id'] ?>)" class="text-slate-300 hover:text-red-400 font-semibold text-lg transition px-1">×</button>
                                    </td>
                                </form>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="5" class="p-10 text-center text-slate-400 text-xs font-medium italic">
                                    No active products found. Add a new item using the form on the right.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 h-fit sticky top-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Add Product</h2>
                
                <form action="process_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Flavor Name</label>
                        <input type="text" name="name" placeholder="Candle" class="w-full text-xs p-3 border border-slate-200/80 rounded-xl focus:outline-pink-500 bg-slate-50/50" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Price (MMK)</label>
                            <input type="number" step="100" min="100" name="price" placeholder="1500" class="w-full text-xs p-3 border border-slate-200/80 rounded-xl focus:outline-pink-500 font-mono bg-slate-50/50" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Initial Stock</label>
                            <input type="number" min="1" name="stock" placeholder="50" class="w-full text-xs p-3 border border-slate-200/80 rounded-xl focus:outline-pink-500 font-mono bg-slate-50/50" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Product Display Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full p-2 border border-slate-200/80 border-dashed rounded-xl focus:outline-pink-500 text-xs text-slate-400 bg-slate-50/50 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 cursor-pointer transition">
                    </div>

                    <button type="submit" name="add" class="mt-2 w-full bg-pink-500 text-white font-bold py-3 rounded-xl hover:bg-pink-600 transition text-xs uppercase tracking-wider shadow-sm">
                        Save
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition-all">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full border border-slate-100 shadow-xl">
            <h3 class="text-base font-bold text-slate-800 mb-1.5 tracking-tight">Delete Product?</h3>
            <p class="text-xs font-medium text-slate-400 mb-6 leading-relaxed">Are you absolutely sure? This removes the product catalog record entirely from active retail inventory pipelines.</p>
            <div class="flex gap-3 text-xs font-bold">
                <button onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition">Cancel</button>
                <a id="confirmBtn" href="#" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl text-center hover:bg-red-600 shadow-sm transition">Yes, Delete</a>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmBtn');
        
        function confirmDelete(id) {
            confirmBtn.href = 'process_product.php?delete=' + id;
            modal.classList.remove('hidden');
        }
        function closeModal() { 
            modal.classList.add('hidden'); 
        }
    </script>
</body>
</html>
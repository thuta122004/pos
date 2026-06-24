<?php require_once 'dbconnect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>POS | Order</title>
</head>
<body class="bg-slate-50 text-slate-800 antialiased p-6 md:p-12 relative overflow-x-hidden">
    
    <div id="toastNotification" class="fixed top-6 right-6 z-50 transform translate-x-full opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-white border border-slate-200/80 shadow-lg rounded-2xl p-4 flex items-center gap-3 max-w-sm">
            <div class="w-8 h-8 rounded-xl bg-pink-50 flex items-center justify-center text-pink-500 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-800">Inventory Limit</h4>
                <p id="toastMessage" class="text-[11px] text-slate-400 font-medium mt-0.5"></p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        
        <header class="flex justify-between items-center mb-10 pb-6 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-pink-500 tracking-tight">Product Catalog</h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">Select items to add to your order</p>
            </div>
            <div>
                <a href="products.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 font-bold px-4 py-2.5 rounded-xl text-xs transition-all tracking-wide border border-slate-200/40 hover:border-pink-200/60 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    Inventory Management
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php
                    $result = $conn->query("SELECT * FROM products WHERE is_active = 1 AND stock_quantity > 0");

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): 
                    ?>
                        <div class="bg-white rounded-2xl border border-slate-200/60 p-5 flex flex-col justify-between transition hover:shadow-md hover:border-slate-300/80">
                            <div>
                                <div class="w-full h-48 bg-slate-50 rounded-xl overflow-hidden mb-4 border border-slate-100 flex items-center justify-center">
                                    <?php if (!empty($row['image_path'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="w-full h-full object-contain">
                                    <?php else: ?>
                                        <div class="text-xs text-slate-400 font-medium tracking-wide uppercase">No Image</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex justify-between items-start gap-2">
                                    <h3 class="text-base font-bold text-slate-800 tracking-tight leading-tight"><?= htmlspecialchars($row['name']) ?></h3>
                                    <span class="bg-pink-50 text-pink-700 font-mono font-bold px-2 py-0.5 rounded-md text-xs whitespace-nowrap">
                                        <?= number_format($row['price']) ?> MMK
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-400">
                                    Available: <span class="font-bold text-slate-600 font-mono"><?= $row['stock_quantity'] ?></span>
                                </span>
                                <button type="button" 
                                        onclick="addToCart(<?= $row['product_id'] ?>, '<?= htmlspecialchars($row['name']) ?>', <?= $row['price'] ?>, <?= $row['stock_quantity'] ?>)"
                                        class="bg-pink-500 text-white font-bold px-4 py-2 rounded-xl text-xs hover:bg-pink-600 active:scale-95 transition-all tracking-wide shadow-sm hover:shadow">
                                    Add to Order
                                </button>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <div class="col-span-1 sm:col-span-2 bg-white rounded-2xl border border-slate-200/60 p-12 flex flex-col items-center justify-center text-center shadow-sm">
                            <div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center text-pink-500 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5h6.75" />
                                </svg>
                            </div>
                            <h3 class="text-slate-800 font-bold text-sm">No Inventory Items Found</h3>
                            <p class="text-slate-400 text-xs mt-1 max-w-xs">There are no products currently available. Please manage your inventory to add new items.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 h-fit sticky top-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Checkout Summary</h2>
                
                <form action="process.php" method="POST">
                    <div class="mb-4">
                        <input type="text" name="customer_name" placeholder="Customer Name (Optional)" 
                            class="w-full bg-slate-50 border border-slate-200/60 text-slate-800 text-xs font-medium px-3 py-2.5 rounded-xl focus:outline-none focus:border-pink-400 focus:ring-1 focus:ring-pink-400 transition placeholder:text-slate-400">
                    </div>
                    <div id="cart-items" class="space-y-3 max-h-80 overflow-y-auto mb-6 pr-1 divide-y divide-slate-100">
                        <p id="empty-cart-msg" class="text-xs text-slate-400 text-center py-10 font-medium">No items added to cart.</p>
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-2.5">
                        <div class="flex justify-between text-xs text-slate-400 font-medium">
                            <span>Items Total</span>
                            <span id="summary-qty" class="font-mono">0 items</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-slate-900 pt-2.5 border-t border-dashed border-slate-200">
                            <span>Total Amount</span>
                            <span class="text-pink-600"><span id="summary-total" class="font-mono">0</span> MMK</span>
                        </div>
                    </div>

                    <button type="submit" name="checkout" id="checkout-btn" disabled
                            class="mt-6 w-full bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 transition disabled:bg-slate-100 disabled:text-slate-300 disabled:cursor-not-allowed text-sm tracking-wide shadow-sm">
                        Confirm Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = {};
        let toastTimeout = null;

        function showCustomToast(message) {
            const toast = document.getElementById('toastNotification');
            const msgSpan = document.getElementById('toastMessage');
            
            msgSpan.innerText = message;
            
            if (toastTimeout) clearTimeout(toastTimeout);
            
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
            
            toastTimeout = setTimeout(() => {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-full', 'opacity-0');
            }, 3000);
        }

        function addToCart(id, name, price, maxStock) {
            if (cart[id]) {
                if (cart[id].quantity < maxStock) {
                    cart[id].quantity++;
                } else {
                    showCustomToast(`Maximum stock of ${maxStock} reached.`);
                    return;
                }
            } else {
                cart[id] = { name: name, price: price, quantity: 1, maxStock: maxStock };
            }
            renderCart();
        }

        function updateQty(id, newQty) {
            const qty = parseInt(newQty);
            if (qty <= 0 || isNaN(qty)) {
                delete cart[id];
            } else if (qty > cart[id].maxStock) {
                showCustomToast(`Cannot exceed ${cart[id].maxStock} stock units.`);
                cart[id].quantity = cart[id].maxStock;
            } else {
                cart[id].quantity = qty;
            }
            renderCart();
        }

        function changeQtyByAmount(id, delta) {
            if (!cart[id]) return;
            const currentQty = cart[id].quantity;
            updateQty(id, currentQty + delta);
        }

        function removeItem(id) {
            delete cart[id];
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            container.innerHTML = '';
            
            const keys = Object.keys(cart);
            
            if (keys.length === 0) {
                container.innerHTML = `<p id="empty-cart-msg" class="text-xs text-slate-400 text-center py-10 font-medium">No items added to cart.</p>`;
                
                document.getElementById('summary-qty').innerText = "0 items";
                document.getElementById('summary-total').innerText = "0";
                checkoutBtn.disabled = true;
                return;
            }

            checkoutBtn.disabled = false;
            let totalAmount = 0;
            let totalQty = 0;

            keys.forEach(id => {
                const item = cart[id];
                const itemTotal = item.price * item.quantity;
                totalAmount += itemTotal;
                totalQty += item.quantity;

                const itemRow = document.createElement('div');
                itemRow.className = "flex justify-between items-center pt-3 first:pt-0";
                itemRow.innerHTML = `
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-slate-700 truncate tracking-tight">${item.name}</h4>
                        <span class="text-[11px] text-slate-400 font-mono font-medium">${item.price.toLocaleString()} MMK</span>
                        <input type="hidden" name="items[${id}][product_id]" value="${id}">
                        <input type="hidden" name="items[${id}][quantity]" value="${item.quantity}">
                    </div>
                    
                    <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg border border-slate-200/40">
                        <button type="button" onclick="changeQtyByAmount(${id}, -1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-white rounded-md transition select-none">&minus;</button>
                        <span class="w-8 text-center text-xs font-bold font-mono text-slate-800">${item.quantity}</span>
                        <button type="button" onclick="changeQtyByAmount(${id}, 1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-white rounded-md transition select-none">&plus;</button>
                    </div>

                    <button type="button" onclick="removeItem(${id})" class="text-slate-300 hover:text-red-400 text-sm font-semibold pl-2 transition">×</button>
                `;
                container.appendChild(itemRow);
            });

            document.getElementById('summary-qty').innerText = `${totalQty} item(s)`;
            document.getElementById('summary-total').innerText = totalAmount.toLocaleString();
        }
    </script>
</body>
</html>
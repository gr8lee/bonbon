@extends('layouts.app')

@section('content')
<div 
    x-data="{
        cart: JSON.parse(localStorage.getItem('store_cart') || '[]'),
        promoInput: '',
        appliedDiscount: null,
        isCheckingOut: false,
        checkoutForm: {
            customerName: @js(auth()->user()?->name ?? ''),
            customerEmail: @js(auth()->user()?->email ?? ''),
            phone: '',
            deliveryAddress: ''
        },

        saveCart() {
            localStorage.setItem('store_cart', JSON.stringify(this.cart));
            window.dispatchEvent(new Event('cart-updated'));
        },

        updateQty(productId, delta) {
            const item = this.cart.find(i => i.productId === productId);
            if (!item) return;
            item.quantity += delta;
            if (item.quantity <= 0) {
                this.cart = this.cart.filter(i => i.productId !== productId);
            }
            this.saveCart();
        },

        removeItem(productId) {
            this.cart = this.cart.filter(i => i.productId !== productId);
            this.saveCart();
        },

        get subtotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        get discountAmount() {
            if (!this.appliedDiscount) return 0;
            return this.appliedDiscount.type === 'percentage'
                ? Math.round((this.subtotal * this.appliedDiscount.value) / 100)
                : Math.min(this.appliedDiscount.value, this.subtotal);
        },

        get total() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },

        async applyPromo() {
            if (!this.promoInput.trim()) return;
            const res = await window.convex.query('orders:validateDiscount', { code: this.promoInput });
            if (!res) {
                alert('Invalid or inactive promo code.');
                this.appliedDiscount = null;
            } else {
                this.appliedDiscount = res;
            }
        },

        async placeOrder() {
            if (this.cart.length === 0) return;
            this.isCheckingOut = true;
            try {
                await window.convex.mutation('orders:createOrder', {
                    customerName: this.checkoutForm.customerName,
                    customerEmail: this.checkoutForm.customerEmail,
                    phone: this.checkoutForm.phone,
                    deliveryAddress: this.checkoutForm.deliveryAddress,
                    items: this.cart.map(({ productId, name, price, quantity }) => ({
                        productId, name, price, quantity
                    })),
                    ...(this.appliedDiscount ? { discountCode: this.appliedDiscount.code } : {})
                });
                this.cart = [];
                this.appliedDiscount = null;
                this.promoInput = '';
                this.saveCart();
                alert('Order placed successfully!');
            } catch (err) {
                alert('Checkout failed: ' + (err.message || err));
            } finally {
                this.isCheckingOut = false;
            }
        }
    }"
    class="max-w-5xl mx-auto py-12 px-6"
>
    <h1 class="text-3xl font-black uppercase text-gray-900 mb-8">Your Shopping Cart</h1>

    <template x-if="cart.length === 0">
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <p class="text-gray-500 mb-4">Your cart is currently empty.</p>
            <a href="{{ url('/store') }}" class="inline-block bg-cyan-600 hover:bg-cyan-700 text-white font-bold px-6 py-3 rounded-full uppercase text-xs tracking-wider transition">
                Browse Store
            </a>
        </div>
    </template>

    <div x-show="cart.length > 0" style="display: none;" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Itemized Cart List -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
            <template x-for="item in cart" :key="item.productId">
                <div class="flex items-center justify-between border-b pb-4 last:border-b-0 last:pb-0">
                    <div class="flex items-center space-x-4">
                        <img :src="item.image" :alt="item.name" class="w-16 h-16 rounded-xl object-contain bg-gray-50 p-1">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm" x-text="item.name"></h3>
                            <p class="text-xs text-cyan-600 font-bold" x-text="'KSh ' + item.price.toFixed(2) + ' each'"></p>
                            <button type="button" @click="removeItem(item.productId)" class="text-[11px] text-red-500 hover:underline mt-1">Remove</button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="updateQty(item.productId, -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 font-bold">-</button>
                            <span class="text-sm font-bold w-6 text-center" x-text="item.quantity"></span>
                            <button type="button" @click="updateQty(item.productId, 1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 font-bold">+</button>
                        </div>
                        <span class="font-black text-gray-900 text-sm w-28 text-right" x-text="'KSh ' + (item.price * item.quantity).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Right: Order Summary & Checkout -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5 h-fit">
            <h2 class="text-lg font-black uppercase text-gray-900">Order Summary</h2>

            <!-- Promo Code -->
            <div class="flex space-x-2">
                <input type="text" x-model="promoInput" placeholder="Promo code" class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs uppercase">
                <button type="button" @click="applyPromo()" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase">Apply</button>
            </div>

            <!-- Totals Breakdown -->
            <div class="space-y-2 text-sm text-gray-600 border-t pt-4">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-bold text-gray-900" x-text="'KSh ' + subtotal.toFixed(2)"></span>
                </div>
                <div x-show="discountAmount > 0" class="flex justify-between text-green-600 font-bold">
                    <span>Discount</span>
                    <span x-text="'- KSh ' + discountAmount.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-lg font-black text-gray-900 border-t pt-3">
                    <span>Total</span>
                    <span class="text-cyan-600" x-text="'KSh ' + total.toFixed(2)"></span>
                </div>
            </div>

            @guest
                <a href="{{ route('login') }}" class="block text-center w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-full uppercase text-xs tracking-wider transition">
                    Sign In to Checkout
                </a>
            @endguest

            @auth
                <form @submit.prevent="placeOrder()" class="space-y-3 border-t pt-4">
                    <input type="text" x-model="checkoutForm.customerName" placeholder="Full Name" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
                    <input type="email" x-model="checkoutForm.customerEmail" placeholder="Email Address" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
                    <input type="text" x-model="checkoutForm.phone" placeholder="M-Pesa Phone (e.g. 0712...)" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
                    <input type="text" x-model="checkoutForm.deliveryAddress" placeholder="Delivery Address" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">

                    <button type="submit" :disabled="isCheckingOut" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-full uppercase text-xs tracking-wider transition">
                        <span x-show="!isCheckingOut">Complete Order</span>
                        <span x-show="isCheckingOut" style="display: none;">Processing...</span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>
@endsection
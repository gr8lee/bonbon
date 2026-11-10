<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav 
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="scrolled ? 'bg-white shadow-md py-3' : 'bg-transparent py-5'"
    class="fixed top-0 left-0 w-full z-50 transition-all duration-300 px-6 md:px-12 flex items-center justify-between"
>
    <!-- Brand Logo -->
    <a href="/" class="text-xl font-black text-gray-900 uppercase tracking-wider">
        Great Shopping
    </a>

    <!-- Desktop Menu Links -->
    <ul class="hidden md:flex items-center space-x-8 font-bold text-sm text-gray-700">
        <li><a href="/" class="hover:text-cyan-600 transition">Home</a></li>
        <li><a href="/about" class="hover:text-cyan-600 transition">About</a></li>
        <li><a href="/contact" class="hover:text-cyan-600 transition">Contact</a></li>
    </ul>

    <!-- Mobile Hamburger Button -->
    <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"/>
        </svg>
    </button>

    <!-- Mobile Navigation Drawer -->
    <div 
        x-show="mobileOpen" 
        x-transition 
        class="fixed inset-0 top-[60px] bg-white z-40 p-6 md:hidden flex flex-col space-y-4 font-bold text-lg"
        style="display: none;"
    >
        <a href="/" @click="mobileOpen = false">Home</a>
        <a href="/about" @click="mobileOpen = false">About</a>
        <a href="/contact" @click="mobileOpen = false">Contact</a>
    </div>
</nav>
    <main>
       <!-- Hero Section -->
    <section class="bg-gradient-to-r from-cyan-600 to-blue-700 text-white py-20 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-tight mb-4">
                GREAT SHOPPING
            </h1>
            <p class="text-lg md:text-xl font-light opacity-90 mb-8">
                Brighten your life with our great shopping experience!
            </p>
            <a href="#catalog" class="inline-block bg-white text-cyan-700 font-bold px-8 py-3 rounded-full shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
                Explore Collection
            </a>
        </div>
    </section>

    <!-- Interactive Product Catalog -->
    <section 
        id="catalog"
        x-data="{ 
            activeCategory: 'all',
            activeModal: null,
            products: [
                { id: 1, name: 'Premium Leather Bag', category: 'fashion', price: '$89.00', desc: 'Crafted from full-grain leather with durable stitching.' },
                { id: 2, name: 'Wireless Headphones', category: 'electronics', price: '$129.00', desc: 'Active noise cancellation with 30-hour battery life.' },
                { id: 3, name: 'Minimalist Watch', category: 'fashion', price: '$150.00', desc: 'Stainless steel mesh strap with scratch-resistant glass.' }
            ]
        }"
        class="max-w-7xl mx-auto py-16 px-6"
    >
        <h2 class="text-3xl font-extrabold text-center uppercase mb-8">Featured Items</h2>

        <!-- Filter Buttons -->
        <div class="flex justify-center space-x-4 mb-10">
            <button 
                @click="activeCategory = 'all'" 
                :class="activeCategory === 'all' ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-5 py-2 rounded-full font-bold text-sm transition"
            >
                All
            </button>
            <button 
                @click="activeCategory = 'fashion'" 
                :class="activeCategory === 'fashion' ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-5 py-2 rounded-full font-bold text-sm transition"
            >
                Fashion
            </button>
            <button 
                @click="activeCategory = 'electronics'" 
                :class="activeCategory === 'electronics' ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-5 py-2 rounded-full font-bold text-sm transition"
            >
                Electronics
            </button>
        </div>

        <!-- Dynamic Product Cards -->
        <div class="grid md:grid-cols-3 gap-8">
            <template x-for="item in products" :key="item.id">
                <div 
                    x-show="activeCategory === 'all' || activeCategory === item.category"
                    x-transition
                    @click="activeModal = item"
                    class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition cursor-pointer"
                >
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-lg text-gray-900" x-text="item.name"></h3>
                        <span class="font-black text-cyan-600" x-text="item.price"></span>
                    </div>
                    <p class="text-gray-500 text-sm mb-4" x-text="item.desc"></p>
                    <span class="text-xs font-bold text-cyan-600 uppercase">View details &rarr;</span>
                </div>
            </template>
        </div>

        <!-- Quick View Modal -->
        <div 
            x-show="activeModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;"
        >
            <div @click.away="activeModal = null" class="bg-white p-8 rounded-2xl max-w-md w-full relative">
                <button @click="activeModal = null" class="absolute top-4 right-4 text-gray-400 hover:text-black font-bold">&times;</button>
                <template x-if="activeModal">
                    <div>
                        <h3 class="text-2xl font-bold mb-2" x-text="activeModal.name"></h3>
                        <p class="text-gray-600 text-sm mb-6" x-text="activeModal.desc"></p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-black text-cyan-600" x-text="activeModal.price"></span>
                            <button class="bg-cyan-600 text-white font-bold px-5 py-2 rounded-full hover:bg-cyan-700 transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
    </main>
</body>
</html>
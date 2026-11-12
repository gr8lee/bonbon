@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="hero bg-gradient-to-r from-cyan-600 to-blue-700 text-white py-20 px-6 text-center">
        <img class="hero-image" src="{{ asset('images/basket.png') }}" alt="">
        <div class="hero-content">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-tight mb-4">
                    GREAT SHOPPING
                </h1>
                <p class="text-lg md:text-xl font-light opacity-90 mb-8">
                    Brighten your life with our great shopping experience!
                </p>
                <a href="/store" class="inline-block bg-white text-cyan-700 font-bold px-8 py-3 rounded-full shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Explore Full Store
                </a>
            </div>
        </div>
    </section>

    <!-- Landing Page: Dynamic Featured Items Showcase -->
    <section 
        id="catalog"
        x-data="{ 
            featuredProducts: [],
            activeModal: null,

            init() {
                // Real-time subscription for featured items on the landing page
                window.convex.onUpdate('products:getFeaturedProducts', {}, (data) => {
                    this.featuredProducts = data || [];
                });
            }
        }"
        class="max-w-7xl mx-auto py-16 px-6"
    >
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-3xl font-black uppercase text-gray-900 tracking-tight">Featured Items</h2>
                <p class="text-gray-500 text-sm mt-1">Discover our most popular picks across all categories.</p>
            </div>
            
        </div>

        <!-- Responsive Grid of Dynamic Featured Items -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <template x-for="item in featuredProducts" :key="item._id">
                <div 
                    @click="activeModal = item"
                    class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition cursor-pointer flex flex-col justify-between"
                >
                    <div>
                        <div class="h-48 w-full rounded-xl overflow-hidden bg-gray-50 mb-4 flex items-center justify-center">
                            <img :src="item.image" :alt="item.name" class="h-full w-full object-contain p-2">
                        </div>
                        <span class="text-[10px] font-extrabold uppercase bg-cyan-50 text-cyan-700 px-2.5 py-1 rounded-md tracking-wider" x-text="item.category"></span>
                        <h3 class="font-bold text-gray-900 text-base mt-2" x-text="item.name"></h3>
                        <p class="text-gray-500 text-xs mt-1 line-clamp-2" x-text="item.description"></p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="font-black text-cyan-600 text-lg" x-text="'KSh ' + (item.price).toFixed(2)"></span>
                        <span class="text-xs font-bold text-cyan-600 uppercase">View &rarr;</span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Quick View Modal for Featured Items -->
        <div 
            x-show="activeModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;"
        >
            <div @click.away="activeModal = null" class="bg-white p-6 rounded-2xl max-w-md w-full relative shadow-2xl">
                <button @click="activeModal = null" class="absolute top-4 right-4 text-gray-400 hover:text-black font-bold text-2xl">&times;</button>
                <template x-if="activeModal">
                    <div>
                        <div class="h-48 w-full rounded-xl overflow-hidden bg-gray-100 mb-4">
                            <img :src="activeModal.image" :alt="activeModal.name" class="w-full h-full object-contain p-2">
                        </div>
                        <span class="text-[10px] font-extrabold uppercase bg-cyan-50 text-cyan-700 px-2.5 py-1 rounded-md tracking-wider" x-text="activeModal.category"></span>
                        <h3 class="text-2xl font-bold mt-1 mb-2 text-gray-900" x-text="activeModal.name"></h3>
                        <p class="text-gray-600 text-sm mb-6" x-text="activeModal.description"></p>
                        <div class="flex justify-between items-center border-t pt-4">
                            <span class="text-2xl font-black text-cyan-600" x-text="'KSh ' + (activeModal.price).toFixed(2)"></span>
                            <a href="/store" class="bg-cyan-600 text-white font-bold px-6 py-2 rounded-full hover:bg-cyan-700 transition uppercase text-xs">
                                Shop All Items
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
@endsection
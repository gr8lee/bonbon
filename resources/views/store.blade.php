@extends('layouts.app')

@section('content')
    <div 
        x-data="{ 
            storeData: {},
            activeCategory: 'all',
            activeModal: null,
            showAddModal: false,
            
            newProduct: {
                name: '',
                price: '',
                category: 'electronics',
                image: '',
                description: '',
                stockQuantity: 10
            },

            categories: [
                { id: 'electronics', label: 'Electronics' },
                { id: 'breakfast', label: 'Breakfast' },
                { id: 'beverages', label: 'Beverages' },
                { id: 'fruits-vegetables', label: 'Fruits & Vegetables' },
                { id: 'confectionery', label: 'Confectionery' },
                { id: 'personal-hygiene', label: 'Personal Hygiene' },
                { id: 'baby-products', label: 'Baby Products' },
                { id: 'kitchen-dining', label: 'Kitchen & Dining' }
            ],

            init() {
                // Subscribe to real-time Convex product menu
                window.convex.onUpdate('products:getStoreMenu', {}, (data) => {
                    this.storeData = data || {};
                });
            },

            scrollToCategory(id) {
                this.activeCategory = id;
                const target = document.getElementById(id);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },

            async submitProduct() {
                if (!this.newProduct.name || !this.newProduct.price) return;

                await window.convex.mutation('products:addProduct', {
                    name: this.newProduct.name,
                    price: parseFloat(this.newProduct.price),
                    category: this.newProduct.category,
                    image: this.newProduct.image || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500',
                    description: this.newProduct.description || '',
                    stockQuantity: parseInt(this.newProduct.stockQuantity) || 10
                });

                // Reset form & close modal
                this.newProduct = { name: '', price: '', category: 'electronics', image: '', description: '', stockQuantity: 10 };
                this.showAddModal = false;
            }
        }"
    >
        <!-- Sticky Category Navigation Bar + Add Button -->
        <div class="sticky top-[60px] z-40 bg-white border-b border-gray-200 shadow-sm py-3 px-6 flex justify-between items-center">
            <div class="max-w-7xl mx-auto flex items-center min-w-0 flex-1">
                <button
                    type="button"
                    aria-label="Scroll categories left"
                    @click="$refs.categoryList.scrollBy({ left: -240, behavior: 'smooth' })"
                    class="shrink-0 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-cyan-100 hover:text-cyan-700 transition"
                >
                    <span aria-hidden="true">&larr;</span>
                </button>

                <div x-ref="categoryList" class="flex items-center space-x-3 overflow-x-auto no-scrollbar min-w-0 flex-1 mx-2">
                    <button
                        type="button"
                        @click="scrollToCategory('all')"
                        :aria-pressed="activeCategory === 'all'"
                        :class="activeCategory === 'all' ? 'bg-cyan-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider whitespace-nowrap transition"
                    >
                        All
                    </button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button 
                            @click="scrollToCategory(cat.id)"
                            :aria-pressed="activeCategory === cat.id"
                            :class="activeCategory === cat.id ? 'bg-cyan-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider whitespace-nowrap transition"
                            x-text="cat.label"
                        ></button>
                    </template>
                </div>

                <button
                    type="button"
                    aria-label="Scroll categories right"
                    @click="$refs.categoryList.scrollBy({ left: 240, behavior: 'smooth' })"
                    class="shrink-0 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-cyan-100 hover:text-cyan-700 transition mr-3"
                >
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </div>

            <button 
                @click="showAddModal = true"
                class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider whitespace-nowrap transition flex items-center space-x-1"
            >
                <span>+ Add Item</span>
            </button>
        </div>

        <!-- Horizontal Sliders for Categorized Products -->
        <div id="all-categories" class="max-w-7xl mx-auto py-10 px-6 space-y-16 scroll-mt-32">
            <template x-for="cat in categories" :key="cat.id">
                <section :id="cat.id" x-show="storeData[cat.id] && storeData[cat.id].length > 0" class="scroll-mt-32">
                    <h2 class="text-2xl font-black uppercase text-gray-900 tracking-tight mb-4" x-text="cat.label"></h2>

                    <div class="flex space-x-6 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar">
                        <template x-for="product in storeData[cat.id]" :key="product._id">
                            <div 
                                @click="activeModal = product"
                                class="snap-center shrink-0 w-64 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-xl transition cursor-pointer flex flex-col justify-between"
                            >
                                <div>
                                    <div class="h-44 w-full rounded-xl overflow-hidden bg-gray-50 mb-3 flex items-center justify-center">
                                        <img :src="product.image" :alt="product.name" class="h-full w-full object-cover">
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-base" x-text="product.name"></h3>
                                    <p class="text-gray-500 text-xs mt-1 line-clamp-2" x-text="product.description"></p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="font-black text-cyan-600 text-lg" x-text="'KSh ' + (product.price).toFixed(2)"></span>
                                    <span class="text-xs font-bold text-cyan-600 uppercase">View &rarr;</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>
            </template>
        </div>

        <!-- Quick View Product Detail Modal -->
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
                            <img :src="activeModal.image" :alt="activeModal.name" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-gray-900" x-text="activeModal.name"></h3>
                        <p class="text-gray-600 text-sm mb-6" x-text="activeModal.description"></p>
                        <div class="flex justify-between items-center border-t pt-4">
                            <span class="text-2xl font-black text-cyan-600" x-text="'KSh ' + (activeModal.price).toFixed(2)"></span>
                            <button class="bg-cyan-600 text-white font-bold px-6 py-2 rounded-full hover:bg-cyan-700 transition uppercase text-xs">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Dynamic Add Product Modal Form -->
        <div 
            x-show="showAddModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;"
        >
            <div @click.away="showAddModal = false" class="bg-white p-6 rounded-2xl max-w-md w-full relative shadow-2xl">
                <button @click="showAddModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-black font-bold text-2xl">&times;</button>
                <h3 class="text-xl font-black uppercase text-gray-900 mb-4">Add New Item</h3>

                <form @submit.prevent="submitProduct()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Product Name</label>
                        <input type="text" x-model="newProduct.name" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-cyan-600">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Price (KSh)</label>
                            <input type="number" step="0.01" x-model="newProduct.price" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-cyan-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Category</label>
                            <select x-model="newProduct.category" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-cyan-600">
                                <option value="electronics">Electronics</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="beverages">Beverages</option>
                                <option value="fruits-vegetables">Fruits &amp; Vegetables</option>
                                <option value="confectionery">Confectionery</option>
                                <option value="personal-hygiene">Personal Hygiene</option>
                                <option value="baby-products">Baby Products</option>
                                <option value="kitchen-dining">Kitchen &amp; Dining</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Image URL</label>
                        <input type="url" x-model="newProduct.image" placeholder="https://..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-cyan-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Description</label>
                        <textarea x-model="newProduct.description" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-cyan-600"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2.5 rounded-full uppercase text-xs tracking-wider transition">
                        Save & Publish
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
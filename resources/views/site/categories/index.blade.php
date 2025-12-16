@extends('site.layout.app')
@section('content')
    <div class="container mt-3">
        <div class="flex items-center justify-center my-6">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-purple-800 tracking-tight leading-none">
                Categories</h2>
        </div>

        <!-- Main Grid of Level 1 Categories -->
        <div id="categories-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-8">
            <!-- Categories will be loaded here via JavaScript -->
            <div class="col-span-full flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-800"></div>
            </div>
        </div>
    </div>

    <!-- Off-Canvas Drawer -->
    <div id="category-drawer" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div id="drawer-backdrop" class="absolute inset-0 bg-black/50 transition-opacity duration-300"></div>
        
        <!-- Drawer Panel -->
        <div id="drawer-panel" class="absolute right-0 top-0 bottom-0 w-full sm:w-96 bg-white shadow-xl transform transition-transform duration-300 translate-x-full overflow-y-auto">
            <!-- Drawer Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                <h3 id="drawer-title" class="text-xl font-bold text-gray-800">Categories</h3>
                <button id="drawer-close" class="text-gray-600 hover:text-gray-800 transition-colors" aria-label="Close drawer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Drawer Navigation Breadcrumb -->
            <div id="drawer-breadcrumb" class="px-4 py-3 bg-gray-50 flex items-center gap-2 text-sm">
                <button id="breadcrumb-back" class="text-purple-600 hover:text-purple-800 font-medium hidden" aria-label="Go back">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </button>
                <span id="breadcrumb-current" class="text-gray-600">Select Category</span>
            </div>

            <!-- Drawer Content -->
            <div id="drawer-content" class="p-4 space-y-2">
                <!-- Content will be loaded here via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Off-Canvas Drill-Down Implementation
        const drawer = {
            el: document.getElementById('category-drawer'),
            panel: document.getElementById('drawer-panel'),
            backdrop: document.getElementById('drawer-backdrop'),
            content: document.getElementById('drawer-content'),
            title: document.getElementById('drawer-title'),
            closeBtn: document.getElementById('drawer-close'),
            breadcrumb: document.getElementById('breadcrumb-current'),
            breadcrumbBack: document.getElementById('breadcrumb-back'),
            
            // Stack to track navigation history
            navigationStack: [],
            
            // All categories data
            categoriesData: [],

            init() {
                this.attachEventListeners();
                this.loadCategories();
            },

            attachEventListeners() {
                this.closeBtn.addEventListener('click', () => this.close());
                this.backdrop.addEventListener('click', () => this.close());
                this.breadcrumbBack.addEventListener('click', () => this.goBack());
                
                // Close on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !this.el.classList.contains('hidden')) {
                        this.close();
                    }
                });
            },

            async loadCategories() {
                try {
                    const response = await fetch('/api/category-hierarchy');
                    this.categoriesData = await response.json();
                    this.renderMainGrid();
                } catch (error) {
                    console.error('Failed to load categories:', error);
                }
            },

            renderMainGrid() {
                const grid = document.getElementById('categories-grid');
                grid.innerHTML = '';

                this.categoriesData.forEach(category => {
                    const card = document.createElement('div');
                    card.className = 'group relative overflow-hidden rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer hover:-translate-y-1 bg-white';
                    card.innerHTML = `
                        <div class="relative flex items-center justify-center bg-gradient-to-br from-pink-50 to-purple-100 h-60">
                            <img class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
                                 src="{{ url('img/') }}/service-category-images/${category.image}?w=298&h=250&q=80&f=webp"
                                 alt="${category.image_alt}"
                                 loading="lazy" decoding="async">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
                        </div>
                        <div class="p-4 text-center">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-pink-600 transition-colors duration-300">
                                ${category.title}
                            </h3>
                        </div>
                    `;

                    card.addEventListener('click', () => {
                        this.navigationStack = [];
                        this.showCategoryLevel(category, category.title);
                    });

                    grid.appendChild(card);
                });
            },

            showCategoryLevel(data, title) {
                this.navigationStack.push({
                    data: data,
                    title: title,
                    level: this.navigationStack.length
                });

                this.renderDrawerContent(data, title);
                this.open();
                this.updateBreadcrumb();
            },

            renderDrawerContent(data, title) {
                this.content.innerHTML = '';
                this.breadcrumb.textContent = title;

                const hasChildren = data.children && data.children.length > 0;
                const hasServices = data.services && data.services.length > 0;

                if (!hasChildren && !hasServices) {
                    this.content.innerHTML = '<p class="text-center text-gray-500 py-4">No items available</p>';
                    return;
                }

                // Render subcategories
                if (hasChildren) {
                    const childrenContainer = document.createElement('div');
                    childrenContainer.className = 'space-y-2';

                    data.children.forEach(child => {
                        const item = document.createElement('button');
                        item.className = 'w-full text-left px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-purple-600 hover:bg-purple-50 transition-all duration-200 font-medium text-gray-800 hover:text-purple-800 flex items-center justify-between group';
                        
                        const hasMore = (child.children && child.children.length > 0) || (child.services && child.services.length > 0);
                        
                        item.innerHTML = `
                            <span>${child.title}</span>
                            ${hasMore ? '<svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>' : ''}
                        `;

                        if (hasMore) {
                            item.addEventListener('click', () => {
                                this.showCategoryLevel(child, child.title);
                            });
                        } else if (child.services && child.services.length > 0) {
                            item.addEventListener('click', () => {
                                this.showProductsList(child);
                            });
                        }

                        childrenContainer.appendChild(item);
                    });

                    this.content.appendChild(childrenContainer);
                }

                // Render products if any at this level
                if (hasServices) {
                    if (hasChildren) {
                        const divider = document.createElement('div');
                        divider.className = 'border-t border-gray-200 my-4';
                        this.content.appendChild(divider);

                        const productsLabel = document.createElement('h4');
                        productsLabel.className = 'text-sm font-semibold text-gray-600 px-2 mt-4 mb-2';
                        productsLabel.textContent = 'Products';
                        this.content.appendChild(productsLabel);
                    }

                    const productsContainer = document.createElement('div');
                    productsContainer.className = 'space-y-2';

                    data.services.forEach(service => {
                        const item = document.createElement('a');
                        item.href = `/service/${service.slug}`;
                        item.className = 'block px-4 py-3 rounded-lg border-2 border-green-200 hover:border-green-600 hover:bg-green-50 transition-all duration-200 font-medium text-gray-800 hover:text-green-800';
                        item.textContent = service.name;
                        productsContainer.appendChild(item);
                    });

                    this.content.appendChild(productsContainer);
                }
            },

            showProductsList(category) {
                const current = this.navigationStack[this.navigationStack.length - 1];
                this.navigationStack[this.navigationStack.length - 1] = {
                    ...current,
                    data: { children: [], services: category.services || [] }
                };

                this.content.innerHTML = '';
                this.breadcrumb.textContent = category.title;

                const productsContainer = document.createElement('div');
                productsContainer.className = 'space-y-2';

                if (category.services && category.services.length > 0) {
                    category.services.forEach(service => {
                        const item = document.createElement('a');
                        item.href = `/service/${service.slug}`;
                        item.className = 'block px-4 py-3 rounded-lg border-2 border-green-200 hover:border-green-600 hover:bg-green-50 transition-all duration-200 font-medium text-gray-800 hover:text-green-800';
                        item.textContent = service.name;
                        productsContainer.appendChild(item);
                    });
                } else {
                    productsContainer.innerHTML = '<p class="text-center text-gray-500 py-4">No products available</p>';
                }

                this.content.appendChild(productsContainer);
            },

            goBack() {
                if (this.navigationStack.length > 1) {
                    this.navigationStack.pop();
                    const previous = this.navigationStack[this.navigationStack.length - 1];
                    this.renderDrawerContent(previous.data, previous.title);
                    this.updateBreadcrumb();
                }
            },

            updateBreadcrumb() {
                this.breadcrumbBack.classList.toggle('hidden', this.navigationStack.length <= 1);
            },

            open() {
                this.el.classList.remove('hidden');
                // Trigger animation
                setTimeout(() => {
                    this.panel.classList.remove('translate-x-full');
                    this.backdrop.classList.add('opacity-100');
                }, 0);
            },

            close() {
                this.panel.classList.add('translate-x-full');
                this.backdrop.classList.remove('opacity-100');
                setTimeout(() => {
                    this.el.classList.add('hidden');
                    this.navigationStack = [];
                }, 300);
            }
        };

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            drawer.init();
        });
    </script>

    <style>
        #drawer-panel {
            max-height: 100vh;
            box-shadow: -4px 0 15px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 640px) {
            #drawer-panel {
                width: 100% !important;
            }
        }

        /* Animation for drawer transitions */
        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Smooth breadcrumb transitions */
        #breadcrumb-back {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Product link styling */
        a[href*='/service/'] {
            display: block;
            text-decoration: none;
        }
    </style>
@endsection

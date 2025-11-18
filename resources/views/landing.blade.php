<x-app-layout>
    <x-slot name="header">
        <nav class="container flex items-center justify-between py-4">
            <div class="flex items-center gap-3">
                <img src="/assets/logo.svg" alt="Logo" class="h-8 w-8">
                <span class="text-xl font-bold text-brand.primary">{{ config('app.name') }}</span>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <a href="#features" class="text-gray-700 hover:text-brand.primary">Features</a>
                <a href="#how" class="text-gray-700 hover:text-brand.primary">How It Works</a>
                <a href="#pricing" class="text-gray-700 hover:text-brand.primary">Pricing</a>
                <a href="#faq" class="text-gray-700 hover:text-brand.primary">FAQ</a>
                <a href="/products" class="btn-outline">Browse Products</a>
                <a href="/register" class="btn-primary">Get Started</a>
            </div>
            <button class="md:hidden btn-outline" x-data="{ open:false }" @click="open=!open">Menu</button>
        </nav>
    </x-slot>

    <!-- SEO Meta -->
    <section class="hidden">
        <h1>{{ config('app.name') }} — Modern E-commerce Platform</h1>
        <meta name="description" content="Shop smarter with {{ config('app.name') }} — seamless checkout, personalized recommendations, and fast delivery.">
        <meta name="keywords" content="ecommerce, shop, checkout, recommendations, delivery">
    </section>

    @php($sectionsByKey = collect($sections ?? [])->keyBy('key'))
    @php($hero = $sectionsByKey['hero'] ?? null)
    <!-- Hero -->
    <section class="section bg-gradient-to-br from-brand.primary/10 to-brand.secondary/10">
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">{{ data_get($hero, 'content.title', 'Sell faster with a next‑gen commerce stack') }}</h2>
                <p class="mt-4 text-lg text-gray-700">{{ data_get($hero, 'content.subtitle', 'Powerful catalog, smart promotions, seamless checkout, and built‑in shipping & payments — all in one platform.') }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="/register" class="btn-primary">Start Free Trial</a>
                    <a href="/products" class="btn-outline">Explore Catalog</a>
                </div>
                <div class="mt-4">
                    <span class="badge-success">Trending now</span>
                    <span class="badge-warning ml-2">New features weekly</span>
                </div>
            </div>
            <div>
                <div class="card shadow-strong">
                    <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=1200&auto=format&fit=crop" alt="Hero" class="radius-lg">
                </div>
            </div>
        </div>
    </section>

    @if(!empty($banners))
    <section class="section">
        <div class="container grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($banners as $banner)
                <a href="{{ $banner->link_url ?? '#' }}" class="card card-hover">
                    <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="radius-lg">
                    <h4 class="mt-3 text-xl font-semibold">{{ $banner->title }}</h4>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Features -->
    <section id="features" class="section">
        <div class="container">
            <h3 class="text-3xl font-bold text-center">Features that drive growth</h3>
            <p class="mt-2 text-center text-gray-600">Everything you need to launch and scale your online store.</p>
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card card-hover">
                    <div class="text-brand.primary text-3xl">🛍️</div>
                    <h4 class="mt-3 text-xl font-semibold">Smart Catalog</h4>
                    <p class="mt-2 text-gray-600">Advanced product management with variants, inventory, and SEO.</p>
                </div>
                <div class="card card-hover">
                    <div class="text-brand.secondary text-3xl">⚡</div>
                    <h4 class="mt-3 text-xl font-semibold">Checkout & Payments</h4>
                    <p class="mt-2 text-gray-600">Stripe, Apple Pay, Cash on Delivery with transactional guarantees.</p>
                </div>
                <div class="card card-hover">
                    <div class="text-brand.accent text-3xl">🚚</div>
                    <h4 class="mt-3 text-xl font-semibold">Shipping & Tracking</h4>
                    <p class="mt-2 text-gray-600">Carrier integrations, zones, rates, and real‑time tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how" class="section bg-gray-50">
        <div class="container">
            <h3 class="text-3xl font-bold text-center">How it works</h3>
            <div class="mt-10 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="card">
                    <span class="badge">Step 1</span>
                    <h4 class="mt-3 text-xl font-semibold">Add Products</h4>
                    <p class="mt-2 text-gray-600">Create your catalog with variants and pricing.</p>
                </div>
                <div class="card">
                    <span class="badge">Step 2</span>
                    <h4 class="mt-3 text-xl font-semibold">Enable Payments</h4>
                    <p class="mt-2 text-gray-600">Connect gateways and set up checkout.</p>
                </div>
                <div class="card">
                    <span class="badge">Step 3</span>
                    <h4 class="mt-3 text-xl font-semibold">Configure Shipping</h4>
                    <p class="mt-2 text-gray-600">Define zones, rates, and carriers.</p>
                </div>
                <div class="card">
                    <span class="badge">Step 4</span>
                    <h4 class="mt-3 text-xl font-semibold">Launch</h4>
                    <p class="mt-2 text-gray-600">Go live and start accepting orders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="section">
        <div class="container">
            <h3 class="text-3xl font-bold text-center">Simple, transparent pricing</h3>
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card">
                    <h4 class="text-xl font-semibold">Starter</h4>
                    <p class="mt-2 text-4xl font-extrabold">$19<span class="text-lg font-medium">/mo</span></p>
                    <ul class="mt-4 text-gray-600 space-y-2">
                        <li>Up to 100 products</li>
                        <li>Basic analytics</li>
                        <li>Email support</li>
                    </ul>
                    <a href="/register" class="mt-6 inline-block btn-primary">Choose Starter</a>
                </div>
                <div class="card shadow-strong">
                    <h4 class="text-xl font-semibold">Growth</h4>
                    <p class="mt-2 text-4xl font-extrabold">$49<span class="text-lg font-medium">/mo</span></p>
                    <ul class="mt-4 text-gray-600 space-y-2">
                        <li>Unlimited products</li>
                        <li>Advanced analytics</li>
                        <li>Priority support</li>
                    </ul>
                    <a href="/register" class="mt-6 inline-block btn-secondary">Choose Growth</a>
                </div>
                <div class="card">
                    <h4 class="text-xl font-semibold">Pro</h4>
                    <p class="mt-2 text-4xl font-extrabold">$99<span class="text-lg font-medium">/mo</span></p>
                    <ul class="mt-4 text-gray-600 space-y-2">
                        <li>Custom features</li>
                        <li>Dedicated support</li>
                        <li>SLA included</li>
                    </ul>
                    <a href="/register" class="mt-6 inline-block btn-outline">Choose Pro</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section bg-gray-50">
        <div class="container">
            <h3 class="text-3xl font-bold text-center">What customers say</h3>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card">
                    <p class="text-gray-700">“We launched in days and grew fast. The checkout is seamless.”</p>
                    <span class="mt-3 text-sm text-gray-500">— Alex, Founder</span>
                </div>
                <div class="card">
                    <p class="text-gray-700">“Shipping automation saved hours weekly. Highly recommend.”</p>
                    <span class="mt-3 text-sm text-gray-500">— Priya, Ops Lead</span>
                </div>
                <div class="card">
                    <p class="text-gray-700">“Promotions engine is flexible and powerful for campaigns.”</p>
                    <span class="mt-3 text-sm text-gray-500">— Omar, Marketing</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="section">
        <div class="container">
            <h3 class="text-3xl font-bold text-center">Frequently asked questions</h3>
            <div class="mt-8 space-y-4">
                <details class="card">
                    <summary class="font-semibold">Is there a free trial?</summary>
                    <p class="mt-2 text-gray-600">Yes, 14 days free with full features.</p>
                </details>
                <details class="card">
                    <summary class="font-semibold">Can I use my payment gateway?</summary>
                    <p class="mt-2 text-gray-600">We support Stripe, Apple Pay, and Cash on Delivery out of the box.</p>
                </details>
                <details class="card">
                    <summary class="font-semibold">Do you offer support?</summary>
                    <p class="mt-2 text-gray-600">Yes, we offer email and priority support depending on your plan.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="container py-10 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <img src="/assets/logo.svg" class="h-8 w-8" alt="Logo">
                    <span class="text-white font-bold">{{ config('app.name') }}</span>
                </div>
                <p class="mt-3 text-sm">Modern e‑commerce platform for growing brands.</p>
            </div>
            <div>
                <h5 class="text-white font-semibold">Links</h5>
                <ul class="mt-2 space-y-1 text-sm">
                    <li><a href="#features" class="hover:text-white">Features</a></li>
                    <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                    <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-semibold">Social</h5>
                <ul class="mt-2 space-y-1 text-sm">
                    <li><a href="#" class="hover:text-white">Twitter</a></li>
                    <li><a href="#" class="hover:text-white">LinkedIn</a></li>
                    <li><a href="#" class="hover:text-white">GitHub</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-semibold">Contact</h5>
                <p class="mt-2 text-sm">support@example.com</p>
                <a href="/brand-guidelines" class="mt-3 inline-block btn-outline">Brand Guidelines</a>
            </div>
        </div>
        <div class="border-t border-gray-800">
            <div class="container py-4 text-sm">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
        </div>
    </footer>
</x-app-layout>

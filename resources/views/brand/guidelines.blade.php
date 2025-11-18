<x-app-layout>
    <x-slot name="header">
        <div class="container py-6">
            <h1 class="text-3xl font-bold">Brand Guidelines</h1>
            <p class="text-gray-600">Color palette, typography, logo usage, and components.</p>
        </div>
    </x-slot>

    <section class="section">
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card">
                <h2 class="text-2xl font-semibold">Colors</h2>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg" style="background:#4F46E5;">
                        <span class="text-white font-semibold">Primary</span>
                    </div>
                    <div class="p-4 rounded-lg" style="background:#0EA5E9;">
                        <span class="text-white font-semibold">Secondary</span>
                    </div>
                    <div class="p-4 rounded-lg" style="background:#F59E0B;">
                        <span class="text-white font-semibold">Accent</span>
                    </div>
                    <div class="p-4 rounded-lg" style="background:#10B981;">
                        <span class="text-white font-semibold">Success</span>
                    </div>
                    <div class="p-4 rounded-lg" style="background:#F59E0B;">
                        <span class="text-white font-semibold">Warning</span>
                    </div>
                    <div class="p-4 rounded-lg" style="background:#EF4444;">
                        <span class="text-white font-semibold">Error</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <h2 class="text-2xl font-semibold">Typography</h2>
                <div class="mt-4 space-y-3">
                    <div>
                        <div class="text-4xl font-extrabold">H1 — Plus Jakarta Sans / Inter</div>
                        <div class="text-3xl font-bold">H2 — Plus Jakarta Sans / Inter</div>
                        <div class="text-2xl font-semibold">H3 — Inter</div>
                        <div class="text-xl font-semibold">H4 — Inter</div>
                        <div class="text-lg">H5 — Inter</div>
                        <div class="text-base">Body — Inter</div>
                        <div class="text-sm text-gray-600">Caption — Inter</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-gray-50">
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-2xl font-semibold">Logo</h2>
                <p class="mt-2 text-gray-600">Text + symbol mark. Works on light/dark backgrounds.</p>
                <div class="mt-4 flex gap-6 items-center">
                    <img src="/assets/logo.svg" class="h-16 w-16" alt="Logo">
                    <div class="bg-gray-900 p-4 radius-lg">
                        <img src="/assets/logo.svg" class="h-16 w-16" alt="Logo Dark">
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/assets/logo.svg" class="btn-outline">Download SVG</a>
                </div>
            </div>
            <div class="card">
                <h2 class="text-2xl font-semibold">Components</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex gap-3">
                        <button class="btn-primary">Primary</button>
                        <button class="btn-secondary">Secondary</button>
                        <button class="btn-outline">Outline</button>
                        <button class="btn-disabled" disabled>Disabled</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input class="input" placeholder="Text input">
                        <select class="select"><option>Option</option></select>
                        <label class="flex items-center gap-2"><input type="checkbox" class="checkbox"> Checkbox</label>
                        <label class="flex items-center gap-2"><input type="radio" class="radio"> Radio</label>
                        <input type="file" class="file-upload">
                    </div>
                    <div class="flex gap-2">
                        <span class="badge-success">Success</span>
                        <span class="badge-warning">Warning</span>
                        <span class="badge-error">Error</span>
                        <span class="tag">Tag</span>
                    </div>
                    <div class="card">Card example with spacing and shadow.</div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

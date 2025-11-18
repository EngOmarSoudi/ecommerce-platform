<x-app-layout>
    <x-slot name="header">
        <div class="container py-6">
            <h1 class="text-2xl font-bold">Create Account</h1>
            <p class="text-gray-600">Join us and start selling/buying.</p>
        </div>
    </x-slot>

    <section class="section">
        <div class="container max-w-md">
            <form method="POST" action="{{ route('auth.register.submit') }}" x-data="{ name:'', email:'', password:'', confirm:'', strength:0, errors:{} }" @submit.prevent="validate() && $el.submit()">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" x-model="name" class="input mt-1" placeholder="Your name" required @input="errors.name=''">
                        <p class="text-red-600 text-sm" x-show="errors.name" x-text="errors.name"></p>
                        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" x-model="email" class="input mt-1" placeholder="you@example.com" required @input="errors.email=''">
                        <p class="text-red-600 text-sm" x-show="errors.email" x-text="errors.email"></p>
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" x-model="password" class="input mt-1" placeholder="••••••••" required minlength="6" @input="updateStrength()">
                        <div class="mt-1 h-2 bg-gray-200 rounded">
                            <div class="h-2 rounded" :class="strengthClass()" :style="`width:${strength}%`"></div>
                        </div>
                        <p class="text-xs text-gray-500">Use at least 6 characters, mix letters & numbers.</p>
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" x-model="confirm" class="input mt-1" placeholder="••••••••" required @input="errors.confirm=''">
                        <p class="text-red-600 text-sm" x-show="errors.confirm" x-text="errors.confirm"></p>
                    </div>

                    <button type="submit" class="btn-primary w-full">Create Account</button>

                    <p class="mt-4 text-center text-sm">Already have an account? <a href="{{ route('auth.login') }}" class="text-brand.primary hover:underline">Sign in</a></p>
                </div>

                <script>
                    function updateStrength(){
                        const p = this.password || '';
                        let s = 0;
                        if(p.length >= 6) s += 25;
                        if(/[A-Z]/.test(p)) s += 25;
                        if(/[0-9]/.test(p)) s += 25;
                        if(/[^A-Za-z0-9]/.test(p)) s += 25;
                        this.strength = s;
                        this.errors.password = '';
                    }
                    function strengthClass(){
                        if(this.strength < 50) return 'bg-red-500';
                        if(this.strength < 75) return 'bg-amber-500';
                        return 'bg-green-500';
                    }
                    function validate(){
                        this.errors = {};
                        if(!this.name) this.errors.name = 'Name is required';
                        const emailValid = /.+@.+\..+/.test(this.email);
                        if(!emailValid){ this.errors.email = 'Enter a valid email address'; }
                        if(!this.password || this.password.length < 6){ this.errors.password = 'Password must be at least 6 characters'; }
                        if(this.confirm !== this.password){ this.errors.confirm = 'Passwords do not match'; }
                        return Object.keys(this.errors).length === 0;
                    }
                </script>
            </form>
        </div>
    </section>
</x-app-layout>

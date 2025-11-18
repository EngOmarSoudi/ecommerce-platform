<x-app-layout>
    <x-slot name="header">
        <div class="container py-6">
            <h1 class="text-2xl font-bold">Login</h1>
            <p class="text-gray-600">Welcome back! Please sign in.</p>
        </div>
    </x-slot>

    <section class="section">
        <div class="container max-w-md">
            <form method="POST" action="{{ route('auth.login.submit') }}" x-data="{ email:'', password:'', errors:{} }" @submit.prevent="validate() && $el.submit()">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" x-model="email" class="input mt-1" placeholder="you@example.com" required @input="errors.email=''">
                        <p class="text-red-600 text-sm" x-show="errors.email" x-text="errors.email"></p>
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" x-model="password" class="input mt-1" placeholder="••••••••" required minlength="6" @input="errors.password=''">
                        <p class="text-red-600 text-sm" x-show="errors.password" x-text="errors.password"></p>
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="checkbox"> Remember me</label>
                        <a href="{{ route('auth.password.forgot') }}" class="text-sm text-brand.primary hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary w-full">Sign In</button>

                    <div class="mt-4 text-center text-sm text-gray-600">Or continue with</div>
                    <div class="mt-2 grid grid-cols-3 gap-3">
                        <button type="button" class="btn-outline">Google</button>
                        <button type="button" class="btn-outline">Apple</button>
                        <button type="button" class="btn-outline">Facebook</button>
                    </div>

                    <p class="mt-4 text-center text-sm">New here? <a href="{{ route('auth.register') }}" class="text-brand.primary hover:underline">Create an account</a></p>
                </div>

                <script>
                    function validate(){
                        this.errors = {};
                        const emailValid = /.+@.+\..+/.test(this.email);
                        if(!emailValid){ this.errors.email = 'Enter a valid email address'; }
                        if(!this.password || this.password.length < 6){ this.errors.password = 'Password must be at least 6 characters'; }
                        return Object.keys(this.errors).length === 0;
                    }
                </script>
            </form>
        </div>
    </section>
</x-app-layout>

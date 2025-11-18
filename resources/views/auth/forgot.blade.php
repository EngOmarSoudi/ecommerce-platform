<x-app-layout>
    <x-slot name="header">
        <div class="container py-6">
            <h1 class="text-2xl font-bold">Forgot Password</h1>
            <p class="text-gray-600">Enter your email to receive a reset link.</p>
        </div>
    </x-slot>

    <section class="section">
        <div class="container max-w-md">
            <form method="POST" action="{{ route('auth.password.email') }}" x-data="{ email:'', errors:{} }" @submit.prevent="validate() && $el.submit()">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" x-model="email" class="input mt-1" placeholder="you@example.com" required @input="errors.email=''">
                        <p class="text-red-600 text-sm" x-show="errors.email" x-text="errors.email"></p>
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">Send Reset Link</button>
                    <p class="mt-4 text-center text-sm">Remembered? <a href="{{ route('auth.login') }}" class="text-brand.primary hover:underline">Back to login</a></p>
                </div>

                <script>
                    function validate(){
                        this.errors = {};
                        const emailValid = /.+@.+\..+/.test(this.email);
                        if(!emailValid){ this.errors.email = 'Enter a valid email address'; }
                        return Object.keys(this.errors).length === 0;
                    }
                </script>
            </form>
        </div>
    </section>
</x-app-layout>

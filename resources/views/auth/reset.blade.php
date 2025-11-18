<x-app-layout>
    <x-slot name="header">
        <div class="container py-6">
            <h1 class="text-2xl font-bold">Reset Password</h1>
            <p class="text-gray-600">Choose a new password to regain access.</p>
        </div>
    </x-slot>

    <section class="section">
        <div class="container max-w-md">
            <form method="POST" action="{{ route('auth.password.reset.submit', ['token' => $token]) }}" x-data="{ password:'', confirm:'', errors:{} }" @submit.prevent="validate() && $el.submit()">
                @csrf
                <input type="hidden" name="email" value="{{ request('email') }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" x-model="password" class="input mt-1" placeholder="••••••••" required minlength="6" @input="errors.password=''">
                        <p class="text-red-600 text-sm" x-show="errors.password" x-text="errors.password"></p>
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" x-model="confirm" class="input mt-1" placeholder="••••••••" required @input="errors.confirm=''">
                        <p class="text-red-600 text-sm" x-show="errors.confirm" x-text="errors.confirm"></p>
                    </div>

                    <button type="submit" class="btn-primary w-full">Reset Password</button>
                    <p class="mt-4 text-center text-sm">Back to <a href="{{ route('auth.login') }}" class="text-brand.primary hover:underline">Login</a></p>
                </div>

                <script>
                    function validate(){
                        this.errors = {};
                        if(!this.password || this.password.length < 6){ this.errors.password = 'Password must be at least 6 characters'; }
                        if(this.confirm !== this.password){ this.errors.confirm = 'Passwords do not match'; }
                        return Object.keys(this.errors).length === 0;
                    }
                </script>
            </form>
        </div>
    </section>
</x-app-layout>

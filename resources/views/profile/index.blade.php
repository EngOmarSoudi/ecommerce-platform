<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your personal information and security</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Picture -->
        <div class="lg:col-span-1">
            <div class="card" x-data="{ avatarPreview: '{{ auth()->user()->avatar ?? '/assets/placeholder.png' }}' }">
                <h2 class="text-lg font-semibold mb-4 dark:text-white">Profile Picture</h2>
                
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mb-4">
                        <img :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    
                    <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" class="w-full">
                        @csrf
                        @method('PUT')
                        
                        <input 
                            type="file" 
                            name="avatar" 
                            accept="image/*"
                            class="input w-full mb-3"
                            @change="event => {
                                const file = event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => avatarPreview = e.target.result;
                                    reader.readAsDataURL(file);
                                }
                            }">
                        
                        <button type="submit" class="btn-primary w-full">Upload Photo</button>
                    </form>
                    
                    <p class="text-xs text-gray-500 mt-2 text-center">JPG or PNG, max 2MB</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mt-6">
                <h2 class="text-lg font-semibold mb-4 dark:text-white">Account Stats</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Member Since</span>
                        <span class="font-medium dark:text-white">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Role</span>
                        <span class="font-medium dark:text-white">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Email Verified</span>
                        <span class="font-medium {{ auth()->user()->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ auth()->user()->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information & Password -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="card">
                <h2 class="text-xl font-semibold mb-6 dark:text-white">Personal Information</h2>
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Full Name *</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', auth()->user()->name) }}" 
                                required
                                class="input w-full">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Email *</label>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email', auth()->user()->email) }}" 
                                required
                                class="input w-full">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Phone</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                value="{{ old('phone', auth()->user()->phone) }}" 
                                class="input w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Date of Birth</label>
                            <input 
                                type="date" 
                                name="birth_date" 
                                value="{{ old('birth_date', auth()->user()->birth_date) }}" 
                                class="input w-full">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t dark:border-gray-700">
                        <button type="reset" class="btn-outline">Reset</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="card">
                <h2 class="text-xl font-semibold mb-6 dark:text-white">Change Password</h2>
                
                <form action="{{ route('profile.update-password') }}" method="POST" x-data="{ 
                    currentPassword: '', 
                    newPassword: '', 
                    confirmPassword: '',
                    passwordStrength: 0,
                    passwordMatch: true,
                    calculateStrength() {
                        let strength = 0;
                        if (this.newPassword.length >= 8) strength++;
                        if (/[a-z]/.test(this.newPassword)) strength++;
                        if (/[A-Z]/.test(this.newPassword)) strength++;
                        if (/[0-9]/.test(this.newPassword)) strength++;
                        if (/[^a-zA-Z0-9]/.test(this.newPassword)) strength++;
                        this.passwordStrength = strength;
                    },
                    checkMatch() {
                        this.passwordMatch = this.newPassword === this.confirmPassword || this.confirmPassword === '';
                    }
                }">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Current Password *</label>
                            <input 
                                type="password" 
                                name="current_password" 
                                x-model="currentPassword"
                                required
                                class="input w-full">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">New Password *</label>
                            <input 
                                type="password" 
                                name="new_password" 
                                x-model="newPassword"
                                @input="calculateStrength()"
                                required
                                minlength="8"
                                class="input w-full">
                            
                            <!-- Password Strength Indicator -->
                            <div class="mt-2">
                                <div class="flex gap-1">
                                    <div class="h-2 flex-1 rounded" :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200'"></div>
                                    <div class="h-2 flex-1 rounded" :class="passwordStrength >= 2 ? 'bg-orange-500' : 'bg-gray-200'"></div>
                                    <div class="h-2 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-yellow-500' : 'bg-gray-200'"></div>
                                    <div class="h-2 flex-1 rounded" :class="passwordStrength >= 4 ? 'bg-lime-500' : 'bg-gray-200'"></div>
                                    <div class="h-2 flex-1 rounded" :class="passwordStrength >= 5 ? 'bg-green-500' : 'bg-gray-200'"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span x-show="passwordStrength < 3">Weak</span>
                                    <span x-show="passwordStrength === 3">Fair</span>
                                    <span x-show="passwordStrength === 4">Good</span>
                                    <span x-show="passwordStrength === 5">Strong</span>
                                </p>
                            </div>
                            
                            @error('new_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Confirm New Password *</label>
                            <input 
                                type="password" 
                                name="new_password_confirmation" 
                                x-model="confirmPassword"
                                @input="checkMatch()"
                                required
                                class="input w-full">
                            <p x-show="!passwordMatch" class="mt-1 text-sm text-red-600">Passwords do not match</p>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Password Requirements:</strong> At least 8 characters, including uppercase, lowercase, numbers, and special characters.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t dark:border-gray-700">
                        <button type="reset" class="btn-outline">Cancel</button>
                        <button type="submit" class="btn-primary" :disabled="!passwordMatch || passwordStrength < 3">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif
</x-layouts.dashboard>

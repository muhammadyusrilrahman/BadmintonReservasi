<x-layouts.app :title="'Profil Saya'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Profil Saya</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola informasi akun dan keamanan Anda.</p>
    </div>

    <div class="max-w-3xl space-y-6">
        {{-- Update Profile Information --}}
        <div class="card">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Delete Account --}}
        <div class="card">
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-layouts.app>

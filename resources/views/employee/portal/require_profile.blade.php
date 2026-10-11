@extends('employee.portal.layout')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-sm">
        <i class="fa-solid fa-user-lock"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-800 mb-3">Profile Completion Required</h2>
    <p class="text-slate-500 mb-8 max-w-md">You must complete your profile information before you can access this page and its features.</p>
    
    <a href="{{ route('employee.complete-profile') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition-colors flex items-center gap-2 max-w-xs mx-auto">
        <i class="fa-solid fa-id-card"></i> Complete Profile Now
    </a>
</div>
@endsection

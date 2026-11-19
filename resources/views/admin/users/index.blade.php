@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Team Members</h1>
    <p class="text-sm text-gray-500">Manage admin roles and permissions</p>
</div>

<div class="bg-white rounded-[2rem] border border-gray-100 p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach(range(1,4) as $user)
    <div class="flex items-center p-4 bg-gray-50 rounded-2xl">
        <img src="https://ui-avatars.com/api/?name=User+{{ $user }}" class="w-12 h-12 rounded-xl">
        <div class="ml-4">
            <p class="font-bold text-gray-800 text-sm">Zeeshan Ahmad</p>
            <p class="text-[10px] text-blue-600 font-black uppercase">Technical Admin</p>
        </div>
        <button class="ml-auto text-gray-400 hover:text-red-500"><i class="fas fa-trash"></i></button>
    </div>
    @endforeach
</div>
@endsection
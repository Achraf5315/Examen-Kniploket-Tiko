@extends('layouts.app')
 
@section('title', 'Productbeheer')
 
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Livewire Product List Component -->
        @livewire('products.product-list-component')
    </div>
</div>
 
<!-- Notificatie system -->
@if(session('success'))
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Livewire.dispatch('notify', {
                type: 'success',
                message: '{{ session("success") }}'
            });
        });
    </script>
@endif
 
@if(session('error'))
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Livewire.dispatch('notify', {
                type: 'error',
                message: '{{ session("error") }}'
            });
        });
    </script>
@endif
 
@if(session('warning'))
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Livewire.dispatch('notify', {
                type: 'warning',
                message: '{{ session("warning") }}'
            });
        });
    </script>
@endif
@endsection
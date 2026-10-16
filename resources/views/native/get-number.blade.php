@extends('native.layouts.mobile')

@section('title', 'Purchase Virtual Number')
@section('header_title', 'Get Number')
@section('back_url', route('mobile.home'))

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px; padding-bottom: 30px;">

    <!-- Reusable State-Of-The-Art Purchase Number Component -->
    <x-purchase-number-section 
        :wallet="$wallet" 
        :countries="$countries" 
        :services="$services" 
        :activeSessions="$activeSessions ?? collect()" 
        :isModal="false" 
    />

</div>
@endsection

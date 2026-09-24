@extends('layouts.app')

@section('title', 'Dashboard — HIVEFIVE Prospect System')

@section('content')
    @if ($role === 'marketing')
        @include('dashboard._marketing', ['user' => $user, 'myProspek' => $myProspek, 'todayTodo' => $todayTodo, 'prospekToday' => $prospekToday, 'prospekMonth' => $prospekMonth])
    @elseif (in_array($role, ['manager_marketing','super_admin']))
        @include('dashboard._manager', ['totalProspekMonth' => $totalProspekMonth, 'closingMonth' => $closingMonth, 'nominalMonth' => $nominalMonth, 'activeMarketings' => $activeMarketings])
    @elseif ($role === 'cs')
        @include('dashboard._cs', ['myCreatedToday' => $myCreatedToday, 'myCreatedMonth' => $myCreatedMonth])
    @endif
@endsection

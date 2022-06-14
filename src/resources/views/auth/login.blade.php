@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">

                           <h3><a href="{{ route('sso.login')  }}" class="btn btn-block btn-info btn-sm" >Acessar Área de Login - SSO </a></h3>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

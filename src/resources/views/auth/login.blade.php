@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center"><h3>Acesso a Área Adminstrativa</h3></div>
                <div class="card-body mb-3">
                    <form method="POST" action="{{ route('login') }}">
                        <div class="row mb-3">
                            <div class="col text-center">
                                   <h3><a href="{{ route('sso.login')  }}" class="btn btn-primary btn-lg btn-block" >ACESSAR ÁREA DE LOGIN - SSO</a></h3>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

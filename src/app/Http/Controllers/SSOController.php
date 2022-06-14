<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

/**
 * SSO CONTROLLER
 */
class SSOController extends Controller
{
    /**
     * GET LOGIN
     * CRIA UMA SESSÃO CHAMADA STATE, PARA INDENTIFICAR O USUÁRIO APÓS O LOGIN NO OUTRO SERVIDOR
     * O USUÁRIO É REDIRECIONADO PARA A OUTRA PÁGINA COM AS CREDENCIAIS
     * LÁ O USUÁRIO IRÁ FAZER O LOGIN NO SERVIDOR - SS0 1
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getLogin(Request $request)
    {
        $request->session()->put("state", $state = Str::random(40));
        $query = http_build_query([
            "client_id" => config("auth.sso_client_id"),
            "redirect_url" => config("app.url") . "/callback",
            "response_type" => "code",
            "scope" => config("auth.sso_scope"),
            "state" => $state
        ]);
        return redirect(config("auth.sso_http_host")."/oauth/authorize?" . $query);
    }

    /**
     * CALL BACK PAGE
     * VERIFICA O USUÁRIO COM A SESSÃO STATE
     * SE FOR UMA SESSÃO INVÁLIDA, PRINTA UM ERRO
     * CRIA UMA SESSÃO COM OS DADOS DO CLIENTE
     * REDIRECIONA PARA A CONECÇÃO DE USUÁRIO NO SERVIDOR -SSO 2
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function getCallBack(Request $request)
    {
        $state = $request->session()->pull("state");
        throw_unless(strlen($state) > 0 && $state == $request->state, InvalidArgumentException::class);
        $response = Http::asForm()->post(
            config("auth.sso_request_host")."/oauth/token",
            [
                "grant_type" => "authorization_code",
                "client_id" => config("auth.sso_client_id"),
                "client_secret" => config("auth.sso_client_secret"),
                "redirect_url" => config("auth.sso_http_host"). "/callback",
                "code" => $request->code
            ]);
        $request->session()->put($response->json());
        return redirect(route("sso.connect"));
    }

    /** CONNECT USER
     *  PEGA O ACCESS TOKEN ATRAVÉS DE UMA SESSÃO
     *  FAZ UMA REQUISIÇÃO AO SERVIDOR SSO 1 PARA PEGAR OS DADOS DO USUÁRIO
     * CASO O USUÁRIO NÃO EXISTE , ENTÃO ELE CRIA
     * LIBERA A AUTENTICAÇÃO DO USUÁRIO
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function connectUser(Request $request)
    {
        $access_token = $request->session()->get("access_token");
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer " . $access_token
        ])->get(config("auth.sso_request_host") . "/api/user");
        $userData =  $response->json();
        try {
            $email = $userData['email'];
        }catch (\Throwable $e) {
            return redirect('login')->withErrors('Fail to get Login Information');
        }
        $user = User::where('email', $email)->first();
        if(!$user) {
            $user = new User;
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->email_verified_at = $userData['email_verified_at'];
            $user->save();
        }
        Auth::login($user);
        return redirect(route("home"));
    }
}

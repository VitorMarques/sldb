<?php
/**
 * Created by PhpStorm.
 * User: Vitor
 * Date: 30/08/2017
 * Time: 10:17
 */

namespace sldb\Services;


use sldb\Models\Compra;
use sldb\Models\User;

class UsuarioService extends Service
{

    public function listaTodos() {
        return User::all();
    }

    public function lista($qtdItens, $textoPesquisa=null)
    {
        return User::where('name', 'like', '%'.$textoPesquisa.'%')->paginate($qtdItens);
    }

    public function listaCompras($id)
    {
        return Compra::where('user_id', '=', $id)->paginate(10)->sortByDesc();
    }

    public function buscaCompra($compraId)
    {
        return Compra::where('id', '=', $compraId)->first();
    }

    public function atualiza($id, $request)
    {
        User::where('id', $id)->update($request);
    }

    public function adiciona($request)
    {

        $usuario = $request->all();
        $usuario['password'] = bcrypt($request->input('password'));

        User::create($usuario);
    }

    public function buscaPorId($id)
    {
        return User::find($id);
    }

    public function remove($id)
    {
        User::where('id', $id)->delete($id);
    }
}

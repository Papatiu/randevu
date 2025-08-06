<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/'; // Kayıt sonrası da ana sayfaya yönlendir.

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Gelen kayıt isteğini doğrula (validation).
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ad' => ['required', 'string', 'max:255'],
            'soyad' => ['required', 'string', 'max:255'],
            'tc_kimlik' => ['required', 'string', 'size:11', 'unique:users'],
            'telefon' => ['required', 'string', 'max:15'],
            'dogum_tarihi' => ['required', 'date'],
            'adres' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Doğrulamadan sonra yeni bir kullanıcı oluştur.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'ad' => $data['ad'],
            'soyad' => $data['soyad'],
            'tc_kimlik' => $data['tc_kimlik'],
            'telefon' => $data['telefon'],
            'dogum_tarihi' => $data['dogum_tarihi'],
            'adres' => $data['adres'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
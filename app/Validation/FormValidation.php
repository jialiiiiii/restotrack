<?php

namespace App\Validation;

class FormValidation
{
    public static function name()
    {
        return ['name' => 'required|regex:/^[A-Za-z\s]{1,50}$/'];
    }

    public static function nameErr()
    {
        return ['name.regex' => 'The name field must be within 50 characters long, and contain alphabets and spaces only.'];
    }

    public static function email()
    {
        return ['email' => 'required|email|unique:customers'];
    }

    public static function emailErr()
    {
        return ['email.unique' => 'The email has already been registered.'];
    }

    public static function password()
    {
        return ['password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?\W).{6,20}$/'];
    }

    public static function passwordErr()
    {
        return ['password.regex' => 'The password field must be 6-20 characters long, and contain at least one capital letter, one small letter, one number, one symbol.'];
    }

    public static function confirmPassword()
    {
        return ['confirmPassword' => 'required|same:password'];
    }

    public static function emailOrId()
    {
        return ['emailOrId' => 'required'];
    }

    public static function loginPassword()
    {
        return ['password' => 'required'];
    }

    public static function mealImage()
    {
        return ['mealImage' => 'required|image|max:2048'];
    }

    public static function mealName()
    {
        return ['mealName' => 'required|string|max:100'];
    }

    public static function mealCategory()
    {
        return ['mealCategory' => 'required|string|max:30'];
    }

    public static function mealDescription()
    {
        return ['mealDescription' => 'required|string|max:500'];
    }

    public static function mealPrice()
    {
        return ['mealPrice' => 'required|numeric|max:10000|regex:/^\d+(\.\d{1,2})?$/'];
    }

    public static function mealPriceErr()
    {
        return ['mealPrice.regex' => 'The meal price must be a valid numeric value with up to 2 decimal places.'];
    }

    public static function mealSales()
    {
        return ['mealSales' => 'required|numeric|max:100|regex:/^\d+(\.\d{1,2})?$/'];
    }

    public static function mealSalesErr()
    {
        return ['mealSales.regex' => 'The meal sales must be a valid numeric value with up to 2 decimal places.'];
    }

    // helpers
    public static function validate($request, $inputs)
    {
        $rules = [];
        $errors = [];

        foreach ($inputs as $input) {
            if (method_exists(static::class, $input)) {
                $rule = call_user_func([static::class, $input]);
                $rules = array_merge($rules, $rule);
            }

            $input .= 'Err';
            if (method_exists(static::class, $input)) {
                $error = call_user_func([static::class, $input]);
                $errors = array_merge($errors, $error);
            }
        }

        $request->validate($rules, $errors);
    }

}
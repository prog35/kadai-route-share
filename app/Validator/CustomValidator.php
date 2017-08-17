// 
<?php
namespace App\Services;

class CustomValidator extends \Illuminate\Validation\Validator
{
    public function validateFoo($attribute,$value,$parameters)
    {
        return false;
    }
}
<?php
$user = \App\Models\User::create([
    'name'=>'test', 
    'email'=>uniqid().'@gmail.com', 
    'password'=>\Illuminate\Support\Facades\Hash::make('123456')
]);
echo "USER ID: " . $user->id . "\n";
echo "USER ID SET? " . (isset($user->id) ? 'YES' : 'NO') . "\n";

$fillable = \App\Models\Student::getModel()->getFillable();
echo "IS user_id FILLABLE? " . (in_array('user_id', $fillable) ? 'YES' : 'NO') . "\n";
print_r($fillable);

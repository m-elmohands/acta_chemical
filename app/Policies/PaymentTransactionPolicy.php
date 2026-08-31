<?php

namespace App\Policies;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view payments');
    }

    public function view(User $user, PaymentTransaction $payment): bool
    {
        return $user->can('view payments') || $payment->user_id === $user->id;
    }

    public function update(User $user, PaymentTransaction $payment): bool
    {
        return $user->can('manage payments');
    }

    public function delete(User $user, PaymentTransaction $payment): bool
    {
        return false;
    }
}

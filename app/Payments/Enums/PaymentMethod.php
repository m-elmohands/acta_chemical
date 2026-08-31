<?php

namespace App\Payments\Enums;

enum PaymentMethod: string
{
    case Paymob = 'paymob';
    case CashOnDelivery = 'cash_on_delivery';
}

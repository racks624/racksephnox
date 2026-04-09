<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends MachineInvestment
{
    // This class now extends MachineInvestment
    // All investment logic is handled by MachineInvestment model
    // Legacy investments are preserved in investments_legacy table
}

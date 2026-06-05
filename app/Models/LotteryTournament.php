<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LotteryTournament extends Model
{
    protected $table = 'lottery_tournaments';
    protected $fillable = ['name', 'description', 'period', 'start_date', 'end_date', 'prize_pool', 'prize_distribution', 'is_active', 'prize_distributed'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime', 'prize_distribution' => 'array', 'is_active' => 'boolean', 'prize_distributed' => 'boolean'];
    public function entries() { return $this->hasMany(LotteryTournamentEntry::class); }
}

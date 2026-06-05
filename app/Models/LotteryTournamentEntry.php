<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LotteryTournamentEntry extends Model
{
    protected $table = 'lottery_tournament_entries';
    protected $fillable = ['tournament_id', 'user_id', 'total_win', 'total_spins', 'rank', 'prize_awarded'];
    protected $casts = ['total_win' => 'decimal:2', 'prize_awarded' => 'decimal:2'];
    public function tournament() { return $this->belongsTo(LotteryTournament::class); }
    public function user() { return $this->belongsTo(User::class); }
}

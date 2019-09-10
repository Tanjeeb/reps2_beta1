<?php

namespace App\Services\Tournament;

use App\{File,
    TourneyList,
    Services\Base\UserViewService,
    Services\User\UserService,
    TourneyMatch,
    TourneyPlayer,
    User};
use App\Http\Controllers\TournamentController;
use App\Services\Base\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TourneyService
{

    /**
     * @param int $limit
     * @return mixed
     */

    public static function getUpTournaments($limit = 5)
    {
        $upcoming_tournaments = TourneyList::where('visible', 1)
            ->where('start_time', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->orderBy('created_at', 'Desc')
            ->limit($limit)->get();
        return $upcoming_tournaments;
    }

    /**
     * @return mixed
     */
    public static function getTournaments()
    {
        $tournaments = TourneyList::orderBy('updated_at', 'Desc')
            ->withCount('players')
            ->withCount('checkin_players')
            ->with('admin_user')
            ->with(['win_player' => function ($query) {
                $query->with(['user' => function ($q) {
                    $q->with('avatar')->withTrashed();
                }]);
            }])
            ->paginate(20);
        return $tournaments;
    }

    /**
     * @return mixed
     */
    public static function getTourneyPlayers($tournament_id)
    {
        $players = TourneyPlayer::where('tourney_id', $tournament_id)->orderBy('check_in','desc')->orderByRaw('LENGTH(place_result)')->orderBy('place_result')
            ->with('user')
            ->get();
        return $players;

    }

    /**
     *
     * @return mixed
     */
    public static function getTourneyMatches($tournament_id)
    {
        $matches = TourneyMatch::where('tourney_id', $tournament_id)->orderBy('round_id')
            ->with(['player1' => function ($query) {
                $query->with(['user' => function ($q) {
                    $q->with('avatar')->withTrashed();
                }]);
            }])
            ->with(['player2' => function ($query) {
                $query->with(['user' => function ($q) {
                    $q->with('avatar')->withTrashed();
                }]);
            }])
            ->with(['file1','file2','file3','file4','file5','file6','file7'])
            ->get();
        $matches_array = array();
        $round_array = array();
        foreach ($matches as $match) {
            $matches_array[$match->round_id][] = $match;
            $round_array[$match->round_id] = $match->round;
        }

        return ['matches' => $matches_array, 'rounds'=>$round_array];
    }
}

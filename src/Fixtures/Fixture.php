<?php

declare(strict_types=1);

namespace Fixtures;

class Fixture
{
    private $name;

    public function __construct(array $seasons)
    {
        $this->seasons = $seasons;
    }

    public function getMatchResult($team_home, $team_away, $score_home = NULL, $score_away = NULL)
    {
        if ($score_home == $score_away && $score_home != NULL) {
            return ['match_winner' => NULL, 'match_loser' => NULL, 'match_draw' => true];
        } else if ($score_home > $score_away) {
            return ['match_winner' => $team_home, 'match_loser' => $team_away, 'match_draw' => false];
        } else if ($score_away < $score_home) {
            return ['match_winner' => $team_away, 'match_loser' => $team_home, 'match_draw' => false];
        } else {
            return ['match_winner' => NULL, 'match_loser' => NULL, 'match_draw' => NULL];
        }
    }

    public function getFixture($event)
    {
        $summary = $event->getSummary();
        //  get teams
        $teams = explode(' - ', $summary);
        $team_home = $teams[0];
        $team_away = preg_replace('/ [0-9][0-9]?:[0-9][0-9]?/', '', $teams[1]);

        // get league and gameday
        $description = $event->getDescription();
        $match_league = explode(': ', $description);
        preg_match('/[0-9][0-9]?/', $description, $match_gameday);

        // get scores
        preg_match('/[0-9][0-9]?:[0-9][0-9]?/', $teams[1], $score_match);
        if (count($score_match) === 1) {
            $score = explode(':', $score_match[0]);
            $score_home = $score[0];
            $score_away = $score[1];
        }

        // get match results
        if (isset($score)) {
            $results = $this->getMatchResult($team_home, $team_away, $score_home, $score_away);
        } else {
            $results = $this->getMatchResult($team_home, $team_away);
        }

        //  get season
        $match_date = new \DateTime($event->start->dateTime);
        foreach ($this->seasons as $key => $season) {
            $season_start = new \DateTime($season['start']);
            $season_end   = new \DateTime($season['end']);

            if (($match_date >= $season_start) && ($match_date <= $season_end)){
                $match_season = $season['season'];
            }
        }

        return [
            'match_id'          => $event->id,
            'match_summary'     => $summary,
            'match_description' => $description,
            'match_season'      => isset($match_season) ? $match_season : NULL,
            'match_date'        => $event->start->dateTime,
            'match_league'      => isset($match_league[1]) ? $match_league[1] : NULL,
            'match_gameday'     => isset($match_gameday[0]) ? $match_gameday[0] : NULL,
            'match_adress'      => $event->getLocation(),
            'match_winner'      => isset($results['match_winner']) ? $results['match_winner'] : NULL,
            'match_loser'       => isset($results['match_loser']) ? $results['match_loser'] : NULL,
            'match_draw'        => isset($results['match_draw']) ? $results['match_draw'] : NULL,
            'team_home'         => $team_home,
            'team_away'         => $team_away,
            'score_home'        => isset($score_home) ? $score_home : NULL,
            'score_away'        => isset($score_away) ? $score_away : NULL,
            'score_match'       => isset($score_match[0]) ? $score_match[0] : NULL
        ];
    }
}

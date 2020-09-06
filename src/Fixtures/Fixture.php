<?php

declare(strict_types=1);

namespace Fixtures;

class Fixture
{
    public function __construct(array $seasons, $event)
    {
        $this->seasons = $seasons;
        $this->fixture = [];
        $this->event = $event;
    }

    public function getEvent($event)
    {
        $this->event = $event;
    }

    public function getFixture()
    {
        $this->getMatchId();
        $this->getMatchSummary();
        $this->getDescription();
        $this->getMatchSeason();
        $this->getMatchDate();
        $this->getMatchLeague();
        $this->getMatchGameDay();
        $this->getMatchLocation();
        $this->getTeams();
        $this->getMatchScore();
        $this->getMatchResult();

        return $this->fixture;
    }

    public function getMatchId()
    {
        $this->fixture['match_id'] = $this->event->id;
    }

    public function getMatchSummary()
    {
        $this->fixture['match_summary'] = $this->event->getSummary();
    }

    public function getDescription()
    {
        $this->fixture['match_description'] = $this->event->getDescription();
    }

    public function getTeams()
    {
        $match_summary = $this->event->getSummary();
        $match_teams = explode(' - ', $match_summary);
        $this->fixture['team_home'] = $match_teams[0];
        $this->fixture['team_away'] = preg_replace('/ [0-9][0-9]?:[0-9][0-9]?/', '', $match_teams[1]);
    }

    public function getMatchScore()
    {
        $match_summary = $this->event->getSummary();
        preg_match('/[0-9][0-9]?:[0-9][0-9]?/', $match_summary, $score_match);
        if (count($score_match) === 1) {
            $score_match = $score_match[0];
            $score = explode(':', $score_match);
            $this->fixture['score_match'] = isset($score_match) ? $score_match : '0:0';
            if (isset($score[0]) && isset($score[1])) {
                $this->fixture['score_home']  =  $score[0];
                $this->fixture['score_away']  =  $score[1];
            } else {
                $this->fixture['score_home']  =  0;
                $this->fixture['score_away']  =  0;
            }
        }
    }

    public function getMatchLeague()
    {
        $match_league = explode(': ', $this->event->getDescription());
        $this->fixture['match_league'] = $match_league[1];
    }

    // get league and gameday
    public function getMatchGameDay()
    {
        preg_match('/[0-9][0-9]?/', $this->event->getDescription(), $match_gameday);
        !isset($match_gameday[0]) ?: $this->fixture['match_gameday'] = $match_gameday[0];
    }

    public function getMatchLocation()
    {
        $this->fixture['match_adress'] = $this->event->getLocation();
    }

    public function getMatchSeason()
    {
        $match_date = new \DateTime($this->event->start->dateTime);
        foreach ($this->seasons as $key => $season) {
            $season_start = new \DateTime($season['start']);
            $season_end   = new \DateTime($season['end']);

            if (($match_date >= $season_start) && ($match_date <= $season_end)) {
                $this->fixture['match_season'] = $season['season'];
            }
        }
    }

    public function getMatchDate()
    {
        $this->fixture['match_date'] = $this->event->start->dateTime;
    }

    public function getMatchResult()
    {
        if (!isset($this->fixture['score_match'])) return;
        if ($this->fixture['score_home'] == $this->fixture['score_away'] && $this->fixture['score_home'] != NULL) {
            $this->fixture['match_draw'] = true;
        } else if ($this->fixture['score_home'] > $this->fixture['score_away']) {
            $this->fixture['match_winner'] = $this->fixture['team_home'];
            $this->fixture['match_loser'] = $this->fixture['team_away'];
        } else if ($this->fixture['score_home'] < $this->fixture['score_away']) {
            $this->fixture['match_winner'] = $this->fixture['team_away'];
            $this->fixture['match_loser'] = $this->fixture['team_home'];
        }
    }
}

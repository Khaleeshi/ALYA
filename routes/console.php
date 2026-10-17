<?php

use Illuminate\Support\Facades\Schedule;

// Chaque nuit : suppression des codes de connexion de plus d'un jour.
Schedule::command('model:prune')->dailyAt('03:15');

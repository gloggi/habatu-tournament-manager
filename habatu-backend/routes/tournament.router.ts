import express, { Router } from 'express';
import {  getGamesPreview, getTimePreview, getTournamentTable } from '../controllers/tournament.controller';
export const router: Router = express.Router();

router.route('/preview').get(getGamesPreview)
router.route('/table').get(getTournamentTable)
router.route('/time-preview').get(getTimePreview)

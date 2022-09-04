import express, { Router } from 'express';
import { createGame, deleteGame, getGame, getGames, updateGame, getGamesPreview } from '../controllers/game.controller';
export const router: Router = express.Router();

router.route('/').post(createGame).get(getGames)
router.route('/preview').get(getGamesPreview)
router.route('/:id').put(updateGame).get(getGame).delete(deleteGame)
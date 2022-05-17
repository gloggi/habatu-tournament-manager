import express, { Router } from 'express';
import { createGame, deleteGame, getGame, getGames, updateGame } from '../controllers/game.controller';
export const router: Router = express.Router();

router.route('/').post(createGame).get(getGames)
router.route('/:id').put(updateGame).get(getGame).delete(deleteGame)
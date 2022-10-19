import express, { Router } from 'express';
import { createGame, deleteGame, getGame, getGames, updateGame } from '../controllers/game.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(adminMiddleware,createGame).get(getGames)
router.route('/:id').put(updateGame).get(getGame).delete(adminMiddleware,deleteGame)
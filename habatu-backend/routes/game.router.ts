import express, { Router } from "express";
import {
  createGame,
  deleteGame,
  getGame,
  getGames,
  updateGame,
} from "../controllers/game.controller";
import { adminMiddleware, refereeMiddleware } from "../middlewares/middlewares";
export const router: Router = express.Router();

router.route("/").post(adminMiddleware, createGame).get(getGames);
router
  .route("/:id")
  .put(refereeMiddleware, updateGame)
  .get(getGame)
  .delete(adminMiddleware, deleteGame);

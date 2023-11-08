import express, { Router } from "express";
import {
  createTournamentFinals,
  getGamesPreview,
  getGroupTable,
  getTimePreview,
  getTournamentRanking,
  getTournamentTable,
  addGameToReferee,
  assingnReferees,
} from "../controllers/tournament.controller";
import { adminMiddleware, refereeMiddleware } from "../middlewares/middlewares";
export const router: Router = express.Router();

router.route("/preview").get(getGamesPreview);
router.route("/table").get(getTournamentTable);
router.route("/time-preview").get(adminMiddleware, getTimePreview);
router.route("/ranking").get(getTournamentRanking);
router.route("/create-finals").get(createTournamentFinals);
router.route("/table/:id").get(getGroupTable);
router.route("/referee").post(addGameToReferee); 
router.route("/assign-referees").post(assingnReferees);
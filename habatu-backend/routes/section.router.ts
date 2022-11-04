import express, { Router } from "express";
import {
  createSection,
  deleteSection,
  getSection,
  getSections,
  updateSection,
} from "../controllers/section.controller";
import { adminMiddleware } from "../middlewares/middlewares";
export const router: Router = express.Router();

router.route("/").post(adminMiddleware, createSection).get(getSections);
router
  .route("/:id")
  .put(adminMiddleware, updateSection)
  .get(getSection)
  .delete(adminMiddleware, deleteSection);

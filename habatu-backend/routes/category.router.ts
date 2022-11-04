import express, { Router } from "express";
import {
  createCategory,
  deleteCategory,
  getCategory,
  getCategorys,
  updateCategory,
} from "../controllers/category.controller";
import { adminMiddleware } from "../middlewares/middlewares";
export const router: Router = express.Router();

router.route("/").post(adminMiddleware, createCategory).get(getCategorys);
router
  .route("/:id")
  .put(adminMiddleware, updateCategory)
  .get(getCategory)
  .delete(adminMiddleware, deleteCategory);

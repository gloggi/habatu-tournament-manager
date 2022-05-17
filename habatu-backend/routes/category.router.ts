import express, { Router } from 'express';
import { createCategory, deleteCategory, getCategory, getCategorys, updateCategory } from '../controllers/category.controller';
export const router: Router = express.Router();

router.route('/').post(createCategory).get(getCategorys)
router.route('/:id').put(updateCategory).get(getCategory).delete(deleteCategory)
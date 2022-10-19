import express, { Router } from 'express';
import { createOption, deleteOption, getOption, getOptions, updateOption } from '../controllers/option.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(createOption).get(getOptions)
router.route('/:id').put(updateOption).get(getOption).delete(adminMiddleware,deleteOption)
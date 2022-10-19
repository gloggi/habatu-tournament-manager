import express, { Router } from 'express';
import { createHall, deleteHall, getHall, getHalls, updateHall } from '../controllers/hall.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(adminMiddleware,createHall).get(getHalls)
router.route('/:id').put(adminMiddleware, updateHall).get(getHall).delete(adminMiddleware, deleteHall)
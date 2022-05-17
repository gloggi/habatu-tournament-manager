import express, { Router } from 'express';
import { createHall, deleteHall, getHall, getHalls, updateHall } from '../controllers/hall.controller';
export const router: Router = express.Router();

router.route('/').post(createHall).get(getHalls)
router.route('/:id').put(updateHall).get(getHall).delete(deleteHall)
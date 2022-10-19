import express, { Router } from 'express';
import { createTimeslot, deleteTimeslot, getTimeslot, getTimeslots, updateTimeslot } from '../controllers/timeslot.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(adminMiddleware, createTimeslot).get(getTimeslots)
router.route('/:id').put(adminMiddleware, updateTimeslot).get(getTimeslot).delete(adminMiddleware, deleteTimeslot)
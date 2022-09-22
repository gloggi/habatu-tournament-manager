import express, { Router } from 'express';
import { createTimeslot, deleteTimeslot, getTimeslot, getTimeslots, updateTimeslot } from '../controllers/timeslot.controller';
export const router: Router = express.Router();

router.route('/').post(createTimeslot).get(getTimeslots)
router.route('/:id').put(updateTimeslot).get(getTimeslot).delete(deleteTimeslot)
import express, { Router } from 'express';
import { createTeam, deleteTeam, getTeam, getTeams, updateTeam } from '../controllers/team.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(adminMiddleware,createTeam).get(getTeams)
router.route('/:id').put(adminMiddleware, updateTeam).get(getTeam).delete(adminMiddleware, deleteTeam)
import express, { Router } from 'express';
import { createTeam, deleteTeam, getTeam, getTeams, updateTeam } from '../controllers/team.controller';
export const router: Router = express.Router();

router.route('/').post(createTeam).get(getTeams)
router.route('/:id').put(updateTeam).get(getTeam).delete(deleteTeam)